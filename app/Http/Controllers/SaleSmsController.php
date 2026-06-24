<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\LocalSmsService;
use App\Support\ShopContext;
use Illuminate\Http\Request;

class SaleSmsController extends Controller
{
    public function send(Request $request, Sale $sale)
    {
        $sid = ShopContext::activeShopId();
        if (is_numeric($sid) && (int)$sid !== (int)$sale->shop_id) {
            abort(403);
        }

        if (!$sale->customer_phone) {
            return back()->with('error', 'Customer phone number missing.');
        }

        $msg =
            "Invoice #{$sale->id} | " .
            ($sale->shop?->name ?? 'Shop') .
            " | Total: {$sale->total_amount} | Paid: " . ($sale->paid_amount ?? 0) .
            " | Balance: " . ($sale->balance_amount ?? 0);

        try {
            LocalSmsService::send($sale->customer_phone, $msg);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'SMS sent.');
    }
}