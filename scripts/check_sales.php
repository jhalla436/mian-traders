<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Sales of pricing_mode = 'shell' products ---\n";
$sales = App\Models\SaleItem::whereHas('product', function($q) {
    $q->where('pricing_mode', 'shell');
})->with('product', 'sale')->get();

foreach ($sales as $s) {
    echo "Sale ID: {$s->sale_id} | Product: {$s->product->name} | Qty: {$s->qty} | Price: {$s->price} | BuyRate: {$s->buy_rate}\n";
}

echo "\n--- Purchases of pricing_mode = 'shell' products ---\n";
$purchases = App\Models\PurchaseItem::whereHas('product', function($q) {
    $q->where('pricing_mode', 'shell');
})->with('product', 'purchase')->get();

foreach ($purchases as $p) {
    echo "Purchase ID: {$p->purchase_id} | Product: {$p->product->name} | Qty: {$p->qty} | Price: {$p->price}\n";
}
