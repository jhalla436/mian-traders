<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $shopId = ShopContext::activeShopId();

        $sales = Sale::query()
            ->select([
                'id',
                'shop_id',
                'customer_name',
                'customer_phone',
                'total_amount',
                'paid_amount',
                'balance_amount',
                'status',
                'sale_type',
            ])
            ->when(is_numeric($shopId), function ($query) use ($shopId) {
                $query->where('shop_id', (int)$shopId);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($search) use ($q) {
                    $search->where('customer_phone', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%");

                    if (is_numeric($q)) {
                        $search->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderByDesc('id')
            ->simplePaginate(25)
            ->withQueryString();

        return view('mt/sales/index', compact('sales', 'q'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items', 'customer', 'payments', 'shop']);

        $items = $sale->items ?? collect([]);

        $overallDiscount = (float)($sale->overall_discount_amount ?? 0);

        // profit_total: sum of line-level profit less any overall bill discount
        $profitTotal = 0.0;
        foreach ($items as $it) {
            $profitTotal += ((float)$it->price - (float)$it->purchase_price) * (float)$it->qty;
        }
        $profitTotal = round($profitTotal - $overallDiscount, 2);

        // Keep realized profit proportional to the paid amount.
        $saleTotal = (float)($sale->total_amount ?? 0);
        $paidAmount = (float)($sale->paid_amount ?? 0);
        $ratio = $saleTotal > 0 ? min(max($paidAmount / $saleTotal, 0), 1) : 0;
        $profitRealized = round($profitTotal * $ratio, 2);

        // Update columns only if they exist
        $dirty = false;
        if (Schema::hasColumn('sales', 'profit_total') && (float)($sale->profit_total ?? 0) !== $profitTotal) {
            $sale->profit_total = $profitTotal;
            $dirty = true;
        }
        if (Schema::hasColumn('sales', 'profit_realized') && (float)($sale->profit_realized ?? 0) !== (float)$profitRealized) {
            $sale->profit_realized = round($profitRealized, 2);
            $dirty = true;
        }
        if ($dirty) $sale->save();

        return view('mt/sales/show', compact('sale', 'items', 'profitTotal', 'profitRealized'));
    }

    public function publicReceipt(Sale $sale)
    {
        $sale->load(['items', 'shop', 'payments']);

        return view('mt.sales.public_receipt', compact('sale'));
    }
}
