<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLedgerEntry;
use App\Models\CompanyOrder;
use App\Models\CompanyOrderItem;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Services\PricingService;
use App\Support\Inventory;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $companyId = trim((string)$request->get('company_id', ''));
        $status = trim((string)$request->get('status', ''));

        $q = CompanyOrder::query()->with(['company','createdBy','receivedPurchase']);

        if ($companyId !== '' && is_numeric($companyId)) {
            $q->where('company_id', (int)$companyId);
        }
        if ($status !== '') {
            $q->where('status', $status);
        }

        $rows = $q->orderByDesc('order_date')->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $companies = Company::orderBy('name')->get();

        return view('mt.company_orders.index', compact('rows','companies','companyId','status'));
    }

    public function create(Request $request)
    {
        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $prefillCompany = $request->get('company_id');
        return view('mt.company_orders.create', compact('companies','products','prefillCompany'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required','integer','exists:companies,id'],
            'order_date' => ['required','date'],
            'note' => ['nullable','string'],
            'global_original_discount' => ['nullable','numeric','min:0','max:100'],
            'global_extra_discount' => ['nullable','numeric','min:0','max:100'],

            'product_id' => ['required','array','min:1'],
            'product_id.*' => ['required','integer','exists:products,id'],
            'qty' => ['required','array','min:1'],
            'qty.*' => ['nullable','numeric','min:0'],
            'original_discount' => ['required','array','min:1'],
            'original_discount.*' => ['nullable','numeric','min:0','max:100'],
            'extra_discount' => ['required','array','min:1'],
            'extra_discount.*' => ['nullable','numeric','min:0','max:100'],
            'unit_cost' => ['required','array','min:1'],
            'unit_cost.*' => ['required','numeric','min:0'],
        ], [
            'product_id.required' => 'Add at least one product line.'
        ]);

        $companyId = (int)$data['company_id'];

        $productIds = $data['product_id'];
        $qtys = $data['qty'];
        $originalDiscounts = $data['original_discount'];
        $extraDiscounts = $data['extra_discount'];
        $costs = $data['unit_cost'];

        $items = [];
        $goodsTotal = 0.0;
        for ($i = 0; $i < count($productIds); $i++) {
            if (!isset($productIds[$i])) continue;
            $pid = (int)$productIds[$i];
            $qty = (float)($qtys[$i] ?? 0);
            $origDisc = (float)($originalDiscounts[$i] ?? 0);
            $extraDisc = (float)($extraDiscounts[$i] ?? 0);
            $cost = isset($costs[$i]) ? (float)$costs[$i] : 0.0;
            if ($qty <= 0) continue;

            $line = round($qty * $cost, 2);
            $goodsTotal += $line;
            $items[] = [
                'product_id' => $pid,
                'qty' => $qty,
                'original_discount' => $origDisc,
                'extra_discount' => $extraDisc,
                'unit_cost' => $cost,
                'line_total' => $line
            ];
        }

        if (count($items) < 1) {
            return back()->withInput()->with('error', 'Please add at least one product with quantity greater than 0.');
        }

        $order = null;

        DB::transaction(function () use ($companyId, $data, $items, $goodsTotal, &$order) {
            $order = CompanyOrder::create([
                'company_id' => $companyId,
                'order_date' => $data['order_date'],
                'status' => 'open',
                'original_discount' => (float)($data['global_original_discount'] ?? 0),
                'extra_discount' => (float)($data['global_extra_discount'] ?? 0),
                'paid_amount' => 0.0,
                'payment_status' => 'unpaid',
                'goods_total' => round($goodsTotal, 2),
                'note' => $data['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $it) {
                CompanyOrderItem::create([
                    'company_order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'original_discount' => $it['original_discount'],
                    'extra_discount' => $it['extra_discount'],
                    'unit_cost' => $it['unit_cost'],
                    'line_total' => $it['line_total'],
                ]);
            }

            // Add to company ledger immediately as an estimated payable
            CompanyLedgerEntry::create([
                'company_id' => $companyId,
                'entry_date' => $order->order_date,
                'entry_type' => 'order',
                'direction' => 'debit',
                'amount' => round($goodsTotal, 2),
                'description' => 'Order placed (Order #' . $order->id . ')',
                'ref_type' => 'company_order',
                'ref_id' => $order->id,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('mt.company_orders.show', $order)->with('success', 'Order saved.');
    }

    public function show(CompanyOrder $order)
    {
        $order->load(['company', 'createdBy', 'items.product', 'receivedPurchase', 'ledgerEntries.user']);
        return view('mt.company_orders.show', compact('order'));
    }

    /**
     * Convert an order into a purchase (Stock IN) and ledger entries.
     */
    public function receive(Request $request, CompanyOrder $order)
    {
        if ($order->status !== 'open') {
            return back()->with('error', 'Only OPEN orders can be received.');
        }

        $data = $request->validate([
            'purchase_date' => ['required','date'],
            'transport_charges' => ['nullable','numeric','min:0'],
            'payment_made' => ['nullable','numeric','min:0'],
            'note' => ['nullable','string'],
        ]);

        $transport = (float)($data['transport_charges'] ?? 0);
        $payment = (float)($data['payment_made'] ?? 0);

        $order->load(['items']);
        $goodsTotal = (float)$order->goods_total;

        $purchase = null;

        DB::transaction(function () use ($order, $data, $transport, $payment, $goodsTotal, &$purchase) {
            $purchase = Purchase::create([
                'company_id' => $order->company_id,
                'supplier_name' => null,
                'invoice_no' => 'Order #' . $order->id,
                'purchase_date' => $data['purchase_date'],
                'goods_total' => round($goodsTotal, 2),
                'transport_charges' => round($transport, 2),
                'payment_made' => round($payment, 2),
                'note' => $data['note'] ?? ('Received from order #' . $order->id),
                'user_id' => auth()->id(),
            ]);

            foreach ($order->items as $it) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it->product_id,
                    'qty' => $it->qty,
                    'unit_cost' => $it->unit_cost,
                    'line_total' => $it->line_total,
                ]);

                // Stock IN
                Inventory::increment(
                    ShopContext::requireSingleShopId(),
                    (int) $it->product_id,
                    (float) $it->qty,
                    (float) $it->unit_cost
                );
                StockMovement::create([
                    'shop_id' => ShopContext::requireSingleShopId(),
                    'product_id' => $it->product_id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'qty' => $it->qty,
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'note' => 'Purchase #' . $purchase->id . ' (from order #' . $order->id . ')',
                ]);
            }

            // Ledger: reverse order estimate (credit), then add actual purchase (debit), plus transport/payment credits
            CompanyLedgerEntry::create([
                'company_id' => $order->company_id,
                'entry_date' => $purchase->purchase_date,
                'entry_type' => 'order_reverse',
                'direction' => 'credit',
                'amount' => round((float)$order->goods_total, 2),
                'description' => 'Order received - reverse estimate (Order #' . $order->id . ')',
                'ref_type' => 'company_order',
                'ref_id' => $order->id,
                'user_id' => auth()->id(),
            ]);

            CompanyLedgerEntry::create([
                'company_id' => $order->company_id,
                'entry_date' => $purchase->purchase_date,
                'entry_type' => 'purchase',
                'direction' => 'debit',
                'amount' => round($goodsTotal, 2),
                'description' => 'Goods received (Purchase #' . $purchase->id . ' from order #' . $order->id . ')',
                'ref_type' => 'purchase',
                'ref_id' => $purchase->id,
                'user_id' => auth()->id(),
            ]);

            if ($transport > 0) {
                CompanyLedgerEntry::create([
                    'company_id' => $order->company_id,
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

            if ($payment > 0) {
                CompanyLedgerEntry::create([
                    'company_id' => $order->company_id,
                    'entry_date' => $purchase->purchase_date,
                    'entry_type' => 'payment',
                    'direction' => 'credit',
                    'amount' => round($payment, 2),
                    'description' => 'Payment to company (Purchase #' . $purchase->id . ' / Order #' . $order->id . ')',
                    'ref_type' => 'company_order',
                    'ref_id' => $order->id,
                    'user_id' => auth()->id(),
                ]);
            }

            $order->update([
                'status' => 'received',
                'received_purchase_id' => $purchase->id,
            ]);

            $order->updatePaymentStatus();
        });

        return redirect()->route('mt.purchases.show', $purchase)->with('success', 'Order received. Stock updated and ledger saved.');
    }

    /**
     * AJAX: Get active products for selected company with solved discounts and base prices.
     */
    public function getCompanyProducts(Request $request)
    {
        $companyId = (int)$request->get('company_id');
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['ok' => false, 'products' => []]);
        }

        $products = Product::where('company_id', $companyId)
            ->where('is_active', 1)
            ->with(['category'])
            ->get();

        $result = [];
        foreach ($products as $p) {
            $discountTypeId = (int)($p->discount_type_id ?? 0);
            $p1 = 0.0;
            $p2 = 0.0;

            if ($discountTypeId > 0) {
                $rule = DiscountRule::where('discount_type_id', $discountTypeId)
                    ->where('scope_type', 'product')
                    ->where('scope_id', $p->id)
                    ->first();

                if (!$rule && !empty($p->category_id)) {
                    $catId = (int)$p->category_id;
                    while ($catId > 0) {
                        $rule = DiscountRule::where('discount_type_id', $discountTypeId)
                            ->where('scope_type', 'category')
                            ->where('scope_id', $catId)
                            ->first();
                        if ($rule) {
                            break;
                        }
                        $category = \App\Models\Category::find($catId);
                        $catId = $category && $category->parent_id ? (int)$category->parent_id : 0;
                    }
                }

                if (!$rule) {
                    $rule = DiscountRule::where('discount_type_id', $discountTypeId)
                        ->where('scope_type', 'company')
                        ->where('scope_id', $p->company_id)
                        ->first();
                }

                if ($rule) {
                    $p1 = (float)($rule->percent_1 ?? 0);
                    $p2 = (float)($rule->percent_2 ?? 0);
                }
            }

            $isShell = ($p->pricing_mode === 'shell');
            $basePrice = 0.0;
            $cft = 0.0;
            if ($isShell) {
                $w = (float)($p->width_in ?? 0);
                $l = (float)($p->length_in ?? 0);
                $h = (float)($p->height_in ?? 0);
                if ($w > 0 && $l > 0 && $h > 0) {
                    $cft = ($w * $l * $h) / 144.0;
                    $basePrice = $cft * (float)($p->shell_rate ?? 0);
                }
            } else {
                $basePrice = (float)($p->mrp ?? 0);
            }

            $result[] = [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category_id' => $p->category_id,
                'category_name' => $p->category ? $p->category->name : 'Uncategorized',
                'pricing_mode' => $p->pricing_mode,
                'base_price' => round($basePrice, 2),
                'mrp' => (float)$p->mrp,
                'shell_rate' => (float)$p->shell_rate,
                'cft' => round($cft, 3),
                'dimensions' => $isShell ? "{$p->width_in}x{$p->length_in}x{$p->height_in}" : '',
                'default_original_discount' => $p1,
                'default_extra_discount' => $p2,
            ];
        }

        usort($result, function($a, $b) {
            $catCmp = strcmp($a['category_name'], $b['category_name']);
            if ($catCmp !== 0) return $catCmp;
            return strcmp($a['name'], $b['name']);
        });

        return response()->json([
            'ok' => true,
            'default_original_discount' => (float)($company->default_original_discount ?? 0),
            'default_extra_discount' => (float)($company->default_extra_discount ?? 0),
            'products' => $result
        ]);
    }

    /**
     * Record a manual payment specifically against a Company Order.
     */
    public function recordPayment(Request $request, CompanyOrder $order)
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float)$data['amount'];

        DB::transaction(function() use ($order, $data, $amount) {
            CompanyLedgerEntry::create([
                'company_id' => $order->company_id,
                'entry_date' => $data['payment_date'],
                'entry_type' => 'payment',
                'direction' => 'credit',
                'amount' => round($amount, 2),
                'description' => $data['description'] ?: ('Payment for Order #' . $order->id),
                'ref_type' => 'company_order',
                'ref_id' => $order->id,
                'user_id' => auth()->id(),
            ]);

            $order->updatePaymentStatus();
        });

        return back()->with('success', 'Payment of Rs. ' . number_format($amount, 2) . ' recorded successfully.');
    }

    /**
     * Export the company order sheet as PDF.
     */
    public function pdf(CompanyOrder $order)
    {
        $order->load(['company', 'createdBy', 'items.product']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mt.company_orders.pdf', compact('order'))
            ->setPaper('a4');
        return $pdf->download('order_' . $order->id . '.pdf');
    }

    public function destroy(CompanyOrder $order)
    {
        if ($order->status === 'received') {
            return back()->with('error', 'Received orders cannot be deleted. Please delete the linked Purchase first to reverse stock and ledger changes.');
        }

        DB::transaction(function () use ($order) {
            // Delete all associated ledger entries (order estimate and payments)
            CompanyLedgerEntry::where('ref_type', 'company_order')
                ->where('ref_id', $order->id)
                ->delete();

            // Delete items
            $order->items()->delete();

            // Delete order
            $order->delete();
        });

        return redirect()->route('mt.company_orders.index')->with('success', 'Order deleted.');
    }

    /**
     * AJAX: legacy calculate unit cost using discount rules.
     */
    public function calcCost(Request $request)
    {
        $companyId = (int)$request->get('company_id');
        $productId = (int)$request->get('product_id');

        $p = Product::find($productId);
        if (!$p) {
            return response()->json(['ok' => false, 'message' => 'Product not found'], 404);
        }

        if ($companyId > 0) {
            $p->company_id = $companyId;
        }

        $cost = PricingService::purchasePrice($p);

        return response()->json([
            'ok' => true,
            'unit_cost' => round((float)$cost, 2),
        ]);
    }
}
