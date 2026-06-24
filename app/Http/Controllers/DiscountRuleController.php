<?php

namespace App\Http\Controllers;

use App\Models\DiscountRule;
use App\Models\DiscountType;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DiscountRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index()
    {
        $rules = DiscountRule::with('discountType')
            ->orderBy('scope_type')
            ->orderBy('scope_id')
            ->get();

        $companies = Company::query()->get()->keyBy('id');
        $categories = Category::query()->get()->keyBy('id');
        $products  = Product::query()->get()->keyBy('id');

        return view('mt/discount_rules/index', compact('rules','companies','categories','products'));
    }

    public function create()
    {
        $types = DiscountType::where('is_active', true)->orderBy('name')->get();
        $companies = Company::query()->get();
        $categories = Category::query()->get();
        $products = Product::query()->get();

        $ruleTypes = [
            'percent_once' => 'Single % (e.g. 12.5)',
            'percent_twostep' => 'Two-step % (e.g. 26 + 5)',
            'fixed_purchase' => 'Fixed Purchase Price',
            'none' => 'No Discount (use manual)',
        ];

        return view('mt/discount_rules/create', compact('types','companies','categories','products','ruleTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scope_type' => ['required','in:company,category,product'],
            'company_id' => ['nullable','exists:companies,id'],
            'category_id' => ['nullable','exists:categories,id'],
            'product_id' => ['nullable','exists:products,id'],

            'discount_type_id' => ['required','exists:discount_types,id'],
            'rule_type' => ['required','in:percent_once,percent_twostep,fixed_purchase,none'],
            'percent_1' => ['nullable','numeric','min:0','max:100'],
            'percent_2' => ['nullable','numeric','min:0','max:100'],
            'fixed_purchase_price' => ['nullable','numeric','min:0'],
            'note' => ['nullable','string','max:255'],
        ]);

        $scopeId = null;
        if ($data['scope_type'] === 'company')  $scopeId = $data['company_id'] ?? null;
        if ($data['scope_type'] === 'category') $scopeId = $data['category_id'] ?? null;
        if ($data['scope_type'] === 'product')  $scopeId = $data['product_id'] ?? null;

        if (!$scopeId) {
            return back()->with('error', 'Select Company/Category/Product according to chosen scope.')->withInput();
        }

        $payload = [
            'scope_type' => $data['scope_type'],
            'scope_id' => $scopeId,
            'discount_type_id' => (int)$data['discount_type_id'],
            'rule_type' => $data['rule_type'],
            'percent_1' => $data['percent_1'] ?? null,
            'percent_2' => $data['percent_2'] ?? null,
            'fixed_purchase_price' => $data['fixed_purchase_price'] ?? null,
            'note' => $data['note'] ?? null,
        ];

        DiscountRule::updateOrCreate(
            [
                'scope_type' => $payload['scope_type'],
                'scope_id' => $payload['scope_id'],
                'discount_type_id' => $payload['discount_type_id'],
            ],
            $payload
        );

        $this->bumpAffectedProductGroupsCache($payload['scope_type'], $payload['scope_id']);

        return redirect()->route('mt.discount_rules.index')->with('success','Rule saved.');
    }

    // IMPORTANT: match resource param name => {discount_rule}
    public function edit(DiscountRule $discount_rule)
    {
        $types = DiscountType::where('is_active', true)->orderBy('name')->get();
        $companies = Company::query()->get();
        $categories = Category::query()->get();
        $products = Product::query()->get();

        $ruleTypes = [
            'percent_once' => 'Single % (e.g. 12.5)',
            'percent_twostep' => 'Two-step % (e.g. 26 + 5)',
            'fixed_purchase' => 'Fixed Purchase Price',
            'none' => 'No Discount (use manual)',
        ];

        return view('mt/discount_rules/edit', compact('discount_rule','types','companies','categories','products','ruleTypes'));
    }

    public function update(Request $request, DiscountRule $discount_rule)
    {
        $data = $request->validate([
            'scope_type' => ['required','in:company,category,product'],
            'company_id' => ['nullable','exists:companies,id'],
            'category_id' => ['nullable','exists:categories,id'],
            'product_id' => ['nullable','exists:products,id'],

            'discount_type_id' => ['required','exists:discount_types,id'],
            'rule_type' => ['required','in:percent_once,percent_twostep,fixed_purchase,none'],
            'percent_1' => ['nullable','numeric','min:0','max:100'],
            'percent_2' => ['nullable','numeric','min:0','max:100'],
            'fixed_purchase_price' => ['nullable','numeric','min:0'],
            'note' => ['nullable','string','max:255'],
        ]);

        $scopeId = null;
        if ($data['scope_type'] === 'company')  $scopeId = $data['company_id'] ?? null;
        if ($data['scope_type'] === 'category') $scopeId = $data['category_id'] ?? null;
        if ($data['scope_type'] === 'product')  $scopeId = $data['product_id'] ?? null;

        if (!$scopeId) {
            return back()->with('error', 'Select Company/Category/Product according to chosen scope.')->withInput();
        }

        // Check if another rule already has this combination
        $oldScopeType = $discount_rule->scope_type;
        $oldScopeId = $discount_rule->scope_id;

        $existing = DiscountRule::where('scope_type', $data['scope_type'])
            ->where('scope_id', $scopeId)
            ->where('discount_type_id', (int)$data['discount_type_id'])
            ->where('id', '!=', $discount_rule->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'A rule with this scope and discount type already exists.')->withInput();
        }

        $discount_rule->update([
            'scope_type' => $data['scope_type'],
            'scope_id' => $scopeId,
            'discount_type_id' => (int)$data['discount_type_id'],
            'rule_type' => $data['rule_type'],
            'percent_1' => $data['percent_1'] ?? null,
            'percent_2' => $data['percent_2'] ?? null,
            'fixed_purchase_price' => $data['fixed_purchase_price'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        $this->bumpAffectedProductGroupsCache($oldScopeType, $oldScopeId);
        $this->bumpAffectedProductGroupsCache($data['scope_type'], $scopeId);

        return redirect()->route('mt.discount_rules.index')->with('success','Rule updated.');
    }

    public function destroy(DiscountRule $discount_rule)
    {
        $this->bumpAffectedProductGroupsCache($discount_rule->scope_type, $discount_rule->scope_id);
        $discount_rule->delete();
        return back()->with('success','Rule deleted.');
    }

    private function bumpAffectedProductGroupsCache(string $scopeType, ?int $scopeId): void
    {
        if (!$scopeId) {
            return;
        }

        if ($scopeType === 'company') {
            $this->bumpProductGroupsCacheVersionForCompany($scopeId);
            return;
        }

        if ($scopeType === 'product') {
            $companyId = Product::whereKey($scopeId)->value('company_id');
            $this->bumpProductGroupsCacheVersionForCompany($companyId ? (int)$companyId : null);
            return;
        }

        if ($scopeType === 'category') {
            $companyIds = Product::where('category_id', $scopeId)
                ->distinct()
                ->pluck('company_id')
                ->all();

            foreach ($companyIds as $companyId) {
                $this->bumpProductGroupsCacheVersionForCompany($companyId ? (int)$companyId : null);
            }
        }
    }

    private function bumpProductGroupsCacheVersionForCompany(?int $companyId): void
    {
        $companyKey = $companyId === null ? 'null' : (string) $companyId;
        $versionKey = "product_groups_company_version_{$companyKey}";
        Cache::forever($versionKey, (int) Cache::get($versionKey, 0) + 1);
    }
}
