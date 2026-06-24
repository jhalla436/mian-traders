<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Support\ShopContext;

class SaleWhatsappController extends Controller
{
    public function send(Sale $sale)
    {
        // ensure user has access to this shop
        $sid = ShopContext::activeShopId();
        if (is_numeric($sid) && (int)$sid !== (int)$sale->shop_id) {
            abort(403);
        }

        if (!$sale->customer_phone) {
            return back()->with('error', 'Customer phone number missing.');
        }

        // build message similar to the javascript version
        $text = "🧾 Invoice #{$sale->id}\n";
        $text .= ($sale->created_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A')) . "\n";
        $text .= "────────────────\n";
        foreach ($sale->items as $it) {
            $text .= "• {$it->product_name} ×{$it->qty} = Rs " . number_format((float)$it->line_total, 2) . "\n";
        }
        $text .= "────────────────\n";
        $text .= "*Total: Rs " . number_format((float)$sale->total_amount, 2) . "*\n";
        $text .= "Paid: Rs " . number_format((float)$sale->paid_amount, 2) . "\n";
        if ((float)$sale->balance_amount > 0) {
            $text .= "⚠️ Balance Due: Rs " . number_format((float)$sale->balance_amount, 2) . "\n";
        }
        $text .= "\nThank you for your purchase! 🙏";

        // normalize phone to international format (92 prefix)
        $phone = preg_replace('/[^0-9]/', '', $sale->customer_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '92')) {
            $phone = '92' . $phone;
        }

        $waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($text);

        return redirect()->away($waUrl);
    }
}
