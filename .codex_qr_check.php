<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sale = App\Models\Sale::query()->first();
if (! $sale) {
    echo "sale_found=0\n";
    exit(0);
}
$url = App\Support\SaleReceipt::publicUrl($sale);
$svg = App\Support\SaleReceipt::qrSvg($sale, 96);
echo "sale_found=1\n";
echo "sale_id={$sale->id}\n";
echo "receipt_url={$url}\n";
echo "svg_has_svg_tag=" . (str_contains($svg, '<svg') ? '1' : '0') . "\n";
echo "svg_length=" . strlen($svg) . "\n";
