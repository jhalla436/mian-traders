<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLedgerEntry;
use App\Models\CompanyOrder;
use App\Models\CompanyOrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Services\PricingService;
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

            'product_id' => ['required','array','min:1'],
            'product_id.*' => ['required','integer','exists:products,id'],
            'qty' => ['required','array','min:1'],
            'qty.*' => ['required','numeric','min:0.01'],
            'unit_cost' => ['required','array','min:1'],
            'unit_cost.*' => ['required','numeric','min:0'],
        ], [
            'product_id.required' => 'Add at least one product line.'
        ]);

        $companyId = (int)$data['company_id'];

        $productIds = $data['product_id'];
        $qtys = $data['qty'];
        $costs = $data['unit_cost'];

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

        $order = null;

        DB::transaction(function () use ($companyId, $data, $items, $goodsTotal, &$order) {
            $order = CompanyOrder::create([
                'company_id' => $companyId,
                'order_date' => $data['order_date'],
                'status' => 'open',
                'goods_total' => round($goodsTotal, 2),
                'note' => $data['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $it) {
                CompanyOrderItem::create([
                    'company_order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_cost' => $it['unit_cost'],
                    'line_total' => $it['line_total'],
                ]);
            }

            // Optional: Add to company ledger immediately as an estimated payable
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
        $order->load(['company','createdBy','items.product','receivedPurchase']);
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
                Product::where('id', $it->product_id)->increment('stock_qty', $it->qty);
                StockMovement::create([
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
                    'description' => 'Payment to company (Purchase #' . $purchase->id . ')',
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'user_id' => auth()->id(),
                ]);
            }

            $order->update([
                'status' => 'received',
                'received_purchase_id' => $purchase->id,
            ]);
        });

        return redirect()->route('mt.purchases.show', $purchase)->with('success', 'Order received. Stock updated and ledger saved.');
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

        // Use selected company for discount scoping
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
