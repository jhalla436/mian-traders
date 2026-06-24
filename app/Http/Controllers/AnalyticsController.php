<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $sid = ShopContext::activeShopId();

        // 1. Top Selling Products
        $topProducts = SaleItem::query()
            ->select('product_id', 'product_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(line_total) as total_revenue'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($sid, function($q) use ($sid) {
                return $q->where('sales.shop_id', $sid);
            })
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 2. Top Selling Categories
        $topCategories = SaleItem::query()
            ->select('categories.name', DB::raw('SUM(sale_items.qty) as total_qty'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->when($sid, function($q) use ($sid) {
                return $q->where('sales.shop_id', $sid);
            })
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 3. Top Selling Companies
        $topCompanies = SaleItem::query()
            ->select('companies.name', DB::raw('SUM(sale_items.qty) as total_qty'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->when($sid, function($q) use ($sid) {
                return $q->where('sales.shop_id', $sid);
            })
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 4. Sales Trend (Daily Revenue)
        $salesTrend = Sale::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_revenue'))
            ->when($sid, function($q) use ($sid) {
                return $q->where('shop_id', $sid);
            })
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('mt.analytics.index', compact(
            'topProducts',
            'topCategories',
            'topCompanies',
            'salesTrend',
            'days'
        ));
    }
}
