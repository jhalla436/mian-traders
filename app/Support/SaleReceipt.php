<?php

namespace App\Support;

use App\Models\Sale;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\URL;

class SaleReceipt
{
    public static function publicUrl(Sale $sale): string
    {
        return URL::signedRoute('mt.sales.receipt', ['sale' => $sale->id]);
    }

    public static function qrSvg(Sale $sale, int $size = 132): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 2),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString(self::qrData($sale));
    }

    public static function qrData(Sale $sale): string
    {
        $lines = [
            ($sale->shop?->name ?? 'Shop') . ' | ' . ($sale->shop?->phone ?? ''),
            ($sale->shop?->address ?? ''),
            'INV#' . $sale->id . ' | ' . $sale->created_at->format('d/m/y H:i'),
            'Cust: ' . ($sale->customer_name ?? 'Walk-in'),
        ];

        if ($sale->customer_phone) {
            $lines[] = 'Ph: ' . $sale->customer_phone;
        }

        $lines[] = '---ITEMS---';

        foreach ($sale->items as $item) {
            $lines[] = sprintf(
                '%s|%s|%s|%s',
                substr($item->product_name, 0, 15), // Truncate long names
                number_format($item->quantity, 1),
                number_format($item->price, 1),
                number_format($item->line_total, 1)
            );
        }

        $lines[] = '---TOTAL---';
        $lines[] = 'Amt:' . number_format($sale->total_amount, 1);
        $lines[] = 'Rcv:' . number_format($sale->received_amount ?? 0, 1);
        $lines[] = 'Due:' . number_format($sale->balance_amount ?? 0, 1);

        return implode("\n", $lines);
    }
}
