<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerProfileController extends Controller
{
    public function show(string $phone)
    {
        $phone = trim($phone);

        // Basic customer info from sales history (phone is the key)
        $customerName = Sale::where('customer_phone', $phone)
            ->whereNotNull('customer_name')
            ->orderByDesc('id')
            ->value('customer_name') ?? '-';

        $lastPurchaseAt = Sale::where('customer_phone', $phone)
            ->max('created_at');

        // Lifetime totals
        $totalSales = (float) Sale::where('customer_phone', $phone)->sum('total_amount');
        $totalProfit = (float) Sale::where('customer_phone', $phone)->sum('profit_total');
        $profitRealized = (float) Sale::where('customer_phone', $phone)->sum('profit_realized');

        $totalUdhar = (float) Sale::where('customer_phone', $phone)
            ->where('balance_amount', '>', 0)
            ->sum('balance_amount');

        // Outstanding bills only
        $outstandingBills = Sale::query()
            ->where('customer_phone', $phone)
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->get();

        // Recent bills (optional)
        $recentBills = Sale::query()
            ->where('customer_phone', $phone)
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        // Top purchased products (amount + qty) using sale_items
        // Assumes SaleItem has: sale_id, product_name, qty, line_total
        $saleIds = Sale::where('customer_phone', $phone)->pluck('id');

        $topProducts = SaleItem::query()
            ->select([
                'product_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(line_total) as total_amount'),
            ])
            ->whereIn('sale_id', $saleIds)
            ->groupBy('product_name')
            ->orderByDesc(DB::raw('SUM(line_total)'))
            ->limit(12)
            ->get();

        // Profit by month (sales, profit, realized profit)
        $profitByMonth = Sale::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('SUM(profit_total) as total_profit'),
                DB::raw('SUM(profit_realized) as realized_profit'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(balance_amount) as total_balance'),
            ])
            ->where('customer_phone', $phone)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderByDesc('ym')
            ->limit(24)
            ->get();

        // WhatsApp number formatting for Pakistan (simple)
        $waNumber = $this->toWhatsAppNumber($phone);

        return view('mt.customers.profile', compact(
            'phone',
            'customerName',
            'waNumber',
            'lastPurchaseAt',
            'totalSales',
            'totalProfit',
            'profitRealized',
            'totalUdhar',
            'outstandingBills',
            'recentBills',
            'topProducts',
            'profitByMonth'
        ));
    }

    private function toWhatsAppNumber(string $phone): string
    {
        // Remove spaces, dashes, plus
        $p = preg_replace('/[^0-9]/', '', $phone) ?? '';

        // If starts with 0 (local PK), convert to 92xxxxxxxxxx
        if (strlen($p) >= 11 && str_starts_with($p, '0')) {
            $p = '92' . substr($p, 1);
        }

        // If already starts with 92, keep
        return $p;
    }
}
