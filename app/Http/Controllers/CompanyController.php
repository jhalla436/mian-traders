<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DiscountRule;
use App\Models\DiscountType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function __construct()
    {
        // Only admin/manager can manage companies
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $companies = Company::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('mt.companies.index', compact('companies', 'q'));
    }

    public function create()
    {
        $discountTypes = DiscountType::orderBy('name')->get();
        $company = new Company();
        $company->is_active = 1;
        $companyDiscountRules = [];
        return view('mt.companies.create', compact('company', 'discountTypes', 'companyDiscountRules'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $company = Company::create($data);
        $this->syncCompanyDiscountRules($company, $request);
        $this->bumpProductGroupsCacheVersionForCompany($company->id);
        return redirect()->route('mt.companies.index')->with('success', 'Company created.');
    }

    public function edit(Company $company)
    {
        $discountTypes = DiscountType::orderBy('name')->get();
        $companyDiscountRules = DiscountRule::query()
            ->where('scope_type', 'company')
            ->where('scope_id', $company->id)
            ->get()
            ->keyBy('discount_type_id')
            ->all();

        return view('mt.companies.edit', compact('company', 'discountTypes', 'companyDiscountRules'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $oldDiscount = (float)($company->max_discount_percent ?? 0);
        $company->update($data);
        $newDiscount = (float)($company->max_discount_percent ?? 0);

        // Update products that had the old discount value to the new one
        if ($oldDiscount !== $newDiscount) {
            Product::where('company_id', $company->id)
                ->where('max_discount_percent', $oldDiscount)
                ->update(['max_discount_percent' => $newDiscount]);
        }

        $this->syncCompanyDiscountRules($company, $request);
        $this->bumpProductGroupsCacheVersionForCompany($company->id);
        return redirect()->route('mt.companies.index')->with('success', 'Company updated.');
    }

    private function syncCompanyDiscountRules(Company $company, Request $request): void
    {
        $rules = $request->input('discount_rules', []);
        if (!is_array($rules)) {
            return;
        }

        foreach ($rules as $discountTypeId => $r) {
            if (!is_array($r)) {
                continue;
            }

            $ruleType = trim((string)($r['rule_type'] ?? ''));
            $percent1 = isset($r['percent_1']) ? (float)$r['percent_1'] : null;
            $percent2 = isset($r['percent_2']) ? (float)$r['percent_2'] : null;
            $fixed = isset($r['fixed_purchase_price']) ? (float)$r['fixed_purchase_price'] : null;

            // Clear rule
            if ($ruleType === '' || $ruleType === 'none') {
                DiscountRule::where('scope_type', 'company')
                    ->where('scope_id', $company->id)
                    ->where('discount_type_id', (int)$discountTypeId)
                    ->delete();
                continue;
            }

            DiscountRule::updateOrCreate(
                [
                    'scope_type' => 'company',
                    'scope_id' => $company->id,
                    'discount_type_id' => (int)$discountTypeId,
                ],
                [
                    'rule_type' => $ruleType,
                    'percent_1' => $percent1,
                    'percent_2' => $percent2,
                    'fixed_purchase_price' => $fixed,
                ]
            );
        }
    }

    public function destroy(Company $company)
    {
        $this->bumpProductGroupsCacheVersionForCompany($company->id);
        $company->delete();
        return redirect()->route('mt.companies.index')->with('success', 'Company deleted.');
    }

    private function bumpProductGroupsCacheVersionForCompany(?int $companyId): void
    {
        $companyKey = $companyId === null ? 'null' : (string) $companyId;
        $versionKey = "product_groups_company_version_{$companyKey}";
        Cache::forever($versionKey, (int) Cache::get($versionKey, 0) + 1);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_key' => ['nullable', 'string', 'in:foam,uncovered_foam,hardware,fabric,spring,accessories,other'],

            // New company info
            'phone_main' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            // existing
            'discount_type_id' => ['nullable', 'integer'],
            'max_discount_percent' => ['nullable','numeric','min:0','max:100'],
            'default_original_discount' => ['nullable','numeric','min:0','max:100'],
            'default_extra_discount' => ['nullable','numeric','min:0','max:100'],
            'default_lamination_rate' => ['nullable','numeric','min:0'],
        ]);
    }
}
