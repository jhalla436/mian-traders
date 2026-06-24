<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "Starting Slab Sheet Thickness Synchronization...\n";

$products = Product::withTrashed()
    ->where(function($q) {
        $q->where('name', 'like', '%slab%')
          ->orWhere('name', 'like', '%sheet%');
    })
    ->get();

$updated = 0;
foreach ($products as $p) {
    $thickness = null;
    if (preg_match('/\(Slab\s+Sheets\)\s+(\.\d+|\d+(?:\.\d+)?)\b/i', $p->name, $matches)) {
        $thickness = (float)$matches[1];
    } elseif (preg_match('/(\d+(?:\.\d+)?)\s*[xX*×]\s*\d+/i', $p->name)) {
        if (preg_match('/[xX*×]\s*(\d+(?:\.\d+)?)\s*$/i', $p->name, $matches)) {
            $thickness = (float)$matches[1];
        }
    }

    if ($thickness !== null && (float)$p->height_in !== (float)$thickness) {
        $oldHeight = $p->height_in;
        $p->height_in = $thickness;
        $p->save();
        echo "Updated product ID {$p->id}: '{$p->name}' from height {$oldHeight} to {$thickness}\n";
        $updated++;
    }
}

echo "Successfully synchronized thickness for {$updated} slab sheet products.\n";
