<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\RecurringExpense;
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

        // Total Udhar (all customers)
        $totalUdharAll = (float) Sale::where('balance_amount', '>', 0)->sum('balance_amount');

        // Sales totals
        $salesToday = (float) Sale::whereDate('created_at', $today)->sum('total_amount');
        $salesThisWeek = (float) Sale::where('created_at', '>=', $weekStart)->sum('total_amount');
        $salesThisMonth = (float) Sale::where('created_at', '>=', $monthStart)->sum('total_amount');

        // Profit totals
        $profitToday = (float) Sale::whereDate('created_at', $today)->sum('profit_total');
        $profitThisWeek = (float) Sale::where('created_at', '>=', $weekStart)->sum('profit_total');
        $profitThisMonth = (float) Sale::where('created_at', '>=', $monthStart)->sum('profit_total');

        // Recent sales
        $recentSales = Sale::query()
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        // Low stock count
        $lowStockCount = (int) Product::whereNotNull('low_stock_alert_qty')
            ->whereColumn('stock_qty', '<=', 'low_stock_alert_qty')
            ->count();

        return view('dashboard', compact(
            'totalUdharAll',
            'salesToday', 'salesThisWeek', 'salesThisMonth',
            'profitToday', 'profitThisWeek', 'profitThisMonth',
            'recentSales',
            'lowStockCount'
        ));
    }
}
