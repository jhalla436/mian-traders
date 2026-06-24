<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ShopProduct;

$names = [
    '72-36-6-AIR_SPINE',
    '78-42-6-AIR_SPINE'
];

foreach ($names as $n) {
    $p = Product::where('name', $n)->first();
    if (!$p) {
        echo "Product $n not found\n";
        continue;
    }
    echo "Product: {$p->name}\n";
    echo "  selling_price_default: " . ($p->selling_price_default ?? 'NULL') . "\n";
    echo "  purchase_price_manual: " . ($p->purchase_price_manual ?? 'NULL') . "\n";
    echo "  pricing_mode: " . ($p->pricing_mode ?? 'NULL') . "\n";
    echo "  shell_rate: " . ($p->shell_rate ?? 'NULL') . "\n";

    $sp = ShopProduct::where('shop_id', 1)->where('product_id', $p->id)->first();
    if ($sp) {
        echo "  ShopProduct selling_price: " . ($sp->selling_price ?? 'NULL') . "\n";
        echo "  ShopProduct stock_qty: " . ($sp->stock_qty ?? 'NULL') . "\n";
    } else {
        echo "  No ShopProduct for shop 1\n";
    }

    // show computed purchasePrice via PricingService
    $pp = App\Services\PricingService::purchasePrice($p);
    echo "  PricingService purchasePrice: {$pp}\n";
}
