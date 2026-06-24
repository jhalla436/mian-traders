<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ShopProduct;
use App\Models\RecurringExpense;
use App\Support\ShopContext;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Auto-generate monthly recurring expenses (e.g. shop rent on 21st)
        try {
            RecurringExpense::generateDueForDate($today);
        } catch (\Throwable $e) {
            // Never break dashboard due to recurring expense generation.
            Log::warning('RecurringExpense generate failed: ' . $e->getMessage());
        }
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $monthStart = Carbon::now()->startOfMonth();

        $sid = ShopContext::activeShopId();
        $salesBase = Sale::query();
        if (is_numeric($sid)) {
            $salesBase->where('shop_id', (int)$sid);
        }

        $summary = (clone $salesBase)
            ->selectRaw('COALESCE(SUM(CASE WHEN balance_amount > 0 THEN balance_amount ELSE 0 END), 0) as total_udhar_all')
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_amount ELSE 0 END), 0) as sales_today', [$today->copy()->startOfDay()])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_amount ELSE 0 END), 0) as sales_this_week', [$weekStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_amount ELSE 0 END), 0) as sales_this_month', [$monthStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_total ELSE 0 END), 0) as profit_today', [$today->copy()->startOfDay()])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_total ELSE 0 END), 0) as profit_this_week', [$weekStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_total ELSE 0 END), 0) as profit_this_month', [$monthStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_realized ELSE 0 END), 0) as realized_today', [$today->copy()->startOfDay()])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_realized ELSE 0 END), 0) as realized_this_week', [$weekStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN profit_realized ELSE 0 END), 0) as realized_this_month', [$monthStart])
            ->first();

        $totalUdharAll = (float) ($summary?->total_udhar_all ?? 0);
        $salesToday = (float) ($summary?->sales_today ?? 0);
        $salesThisWeek = (float) ($summary?->sales_this_week ?? 0);
        $salesThisMonth = (float) ($summary?->sales_this_month ?? 0);
        $profitToday = (float) ($summary?->profit_today ?? 0);
        $profitThisWeek = (float) ($summary?->profit_this_week ?? 0);
        $profitThisMonth = (float) ($summary?->profit_this_month ?? 0);
        $realizedToday = (float) ($summary?->realized_today ?? 0);
        $realizedThisWeek = (float) ($summary?->realized_this_week ?? 0);
        $realizedThisMonth = (float) ($summary?->realized_this_month ?? 0);

        // Recent sales
        $recentSales = (clone $salesBase)
            ->select(['id', 'customer_name', 'customer_phone', 'total_amount', 'paid_amount', 'balance_amount', 'status'])
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        // Low stock count
        $sp = ShopProduct::query()->where('low_stock_alert_qty', '>', 0)
            ->whereColumn('stock_qty', '<=', 'low_stock_alert_qty');
        if (is_numeric($sid)) {
            $sp->where('shop_id', (int)$sid);
        }
        $lowStockCount = (int) $sp->count();

        return view('dashboard', compact(
            'totalUdharAll',
            'salesToday', 'salesThisWeek', 'salesThisMonth',
            'profitToday', 'profitThisWeek', 'profitThisMonth',
            'realizedToday', 'realizedThisWeek', 'realizedThisMonth',
            'recentSales',
            'lowStockCount'
        ));
    }
}
