<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Company;

$rows = Company::orderBy('id')->get();
foreach ($rows as $r) {
    echo "{$r->id}: {$r->name}\n";
}
