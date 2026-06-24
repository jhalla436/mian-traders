<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLedgerEntry;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $companyId = trim((string)$request->get('company_id', ''));
        $from = trim((string)$request->get('from', ''));
        $to = trim((string)$request->get('to', ''));

        $q = Purchase::query()->with(['company','user']);

        if ($companyId !== '' && is_numeric($companyId)) {
            $q->where('company_id', (int)$companyId);
        }
        if ($from !== '') {
            $q->where('purchase_date', '>=', $from);
        }
        if ($to !== '') {
            $q->where('purchase_date', '<=', $to);
        }

        $rows = $q->orderByDesc('purchase_date')->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $companies = Company::orderBy('name')->get();

        return view('mt.purchases.index', compact('rows','companies','companyId','from','to'));
    }

    public function create(Request $request)
    {
        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $products = Product::where('is_active', 1)->orderBy('name')->get();

        $prefillCompany = $request->get('company_id');

        return view('mt.purchases.create', compact('companies','products','prefillCompany'));
    }

    /**
     * AJAX: calculate unit cost using discount rules for the selected company.
     */
    public function calcCost(Request $request)
    {
        $companyId = (int)$request->get('company_id');
        $productId = (int)$request->get('product_id');

        $p = Product::find($productId);
        if (!$p) {
            return response()->json(['ok' => false, 'message' => 'Product not found'], 404);
        }

        // Use selected company for discount scoping (even if product has company_id null)
        if ($companyId > 0) {
            $p->company_id = $companyId;
        }

        $cost = PricingService::purchasePrice($p);

        return response()->json([
            'ok' => true,
            'unit_cost' => round((float)$cost, 2),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['nullable','integer','exists:companies,id'],
            'supplier_name' => ['nullable','string','max:255'],
            'invoice_no' => ['nullable','string','max:80'],
            'purchase_date' => ['required','date'],
            'transport_charges' => ['nullable','numeric','min:0'],
            'payment_made' => ['nullable','numeric','min:0'],
            'note' => ['nullable','string'],

            'product_id' => ['required','array','min:1'],
            'product_id.*' => ['required','integer','exists:products,id'],
            'qty' => ['required','array','min:1'],
            'qty.*' => ['required','numeric','min:0.01'],
            'unit_cost' => ['required','array','min:1'],
            'unit_cost.*' => ['required','numeric','min:0'],
        ], [
            'product_id.required' => 'Add at least one product line.',
        ]);

        $transport = (float)($data['transport_charges'] ?? 0);
        $payment = (float)($data['payment_made'] ?? 0);

        $productIds = $data['product_id'];
        $qtys = $data['qty'];
        $costs = $data['unit_cost'];

        // Build items & totals
        $items = [];
        $goodsTotal = 0.0;
        for ($i = 0; $i < count($productIds); $i++) {
            $pid = (int)$productIds[$i];
            $qty = (float)$qtys[$i];
            $cost = (float)$costs[$i];
            if ($qty <= 0) continue;
            $line = round($qty * $cost, 2);
            $goodsTotal += $line;
            $items[] = ['product_id' => $pid, 'qty' => $qty, 'unit_cost' => $cost, 'line_total' => $line];
        }

        if (count($items) < 1) {
            return back()->withInput()->with('error', 'Please add at least one valid product line.');
        }

        $purchase = null;

        DB::transaction(function () use ($data, $items, $goodsTotal, $transport, $payment, &$purchase) {
            $purchase = Purchase::create([
                'company_id' => $data['company_id'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'invoice_no' => $data['invoice_no'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'goods_total' => round($goodsTotal, 2),
                'transport_charges' => round($transport, 2),
                'payment_made' => round($payment, 2),
                'note' => $data['note'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($items as $it) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_cost' => $it['unit_cost'],
                    'line_total' => $it['line_total'],
                ]);

                // Stock IN
                Product::where('id', $it['product_id'])->increment('stock_qty', $it['qty']);

                StockMovement::create([
                    'product_id' => $it['product_id'],
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'qty' => $it['qty'],
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'note' => 'Purchase #' . $purchase->id,
                ]);
            }

            // Company ledger (only if linked to a company)
            if (!empty($purchase->company_id)) {
                // 1) Goods invoice increases payable
                CompanyLedgerEntry::create([
                    'company_id' => $purchase->company_id,
                    'entry_date' => $purchase->purchase_date,
                    'entry_type' => 'purchase',
                    'direction' => 'debit',
                    'amount' => round($goodsTotal, 2),
                    'description' => 'Goods received (Purchase #' . $purchase->id . ')',
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'user_id' => auth()->id(),
                ]);

                // 2) Transport paid by us is usually deducted from next company payment (credit)
                if ($transport > 0) {
                    CompanyLedgerEntry::create([
                        'company_id' => $purchase->company_id,
                        'entry_date' => $purchase->purchase_date,
                        'entry_type' => 'transport',
                        'direction' => 'credit',
                        'amount' => round($transport, 2),
                        'description' => 'Transport paid by us (deduct later) (Purchase #' . $purchase->id . ')',
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'user_id' => auth()->id(),
                    ]);
                }

                // 3) Payment made to company reduces payable (credit)
                if ($payment > 0) {
                    CompanyLedgerEntry::create([
                        'company_id' => $purchase->company_id,
                        'entry_date' => $purchase->purchase_date,
                        'entry_type' => 'payment',
                        'direction' => 'credit',
                        'amount' => round($payment, 2),
                        'description' => 'Payment to company (Purchase #' . $purchase->id . ')',
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        });

        return redirect()->route('mt.purchases.show', $purchase)->with('success', 'Purchase saved and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['company','user','items.product']);
        return view('mt.purchases.show', compact('purchase'));
    }
}
