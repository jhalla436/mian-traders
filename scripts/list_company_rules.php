<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DiscountRule;

$rows = DiscountRule::where('scope_type','company')->get();
if ($rows->isEmpty()) {
    echo "No company-scoped discount rules found.\n";
} else {
    foreach ($rows as $r) {
        echo "id:{$r->id} discount_type_id:{$r->discount_type_id} scope_id:{$r->scope_id} percent1:{$r->percent_1} percent2:{$r->percent_2} fixed:{$r->fixed_purchase_price}\n";
    }
}
