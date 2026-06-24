<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Support\ShopContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SalePdfController extends Controller
{
    public function download(Sale $sale)
    {
        // shop safety: if user is scoped to a shop, must match
        $sid = ShopContext::activeShopId();
        if (is_numeric($sid) && (int)$sid !== (int)$sale->shop_id) {
            abort(403);
        }

        $sale->load(['items.product', 'shop', 'payments']);

        $pdf = Pdf::loadView('mt.sales.invoice_pdf', [
            'sale' => $sale,
        ])->setPaper('a4');

        // save to local storage
        $filename = 'invoices/invoice_' . $sale->id . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $pdf->download('invoice_' . $sale->id . '.pdf');
    }
}
