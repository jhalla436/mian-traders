<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $sales = Sale::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('customer_phone', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%")
                      ->orWhere('id', $q);
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('mt/sales/index', compact('sales', 'q'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items', 'customer', 'payments']);

        $items = $sale->items ?? collect([]);

        // profit_total: sum of (sell - purchase) * qty
        $profitTotal = 0.0;
        foreach ($items as $it) {
            $profitTotal += ((float)$it->price - (float)$it->purchase_price) * (float)$it->qty;
        }
        $profitTotal = round($profitTotal, 2);

        // profit_realized: only when fully paid (your rule)
        $total = (float)$sale->total_amount;
        $paid  = (float)$sale->paid_amount;
        $profitRealized = ($total > 0 && $paid >= $total) ? $profitTotal : 0.0;

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
}
