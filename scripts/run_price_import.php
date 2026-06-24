<?php
// CLI helper: php scripts/run_price_import.php <csv-path> [dry_run=1]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\UploadedFile as LaravelUploadedFile;

if ($argc < 2) {
    echo "Usage: php scripts/run_price_import.php <csv-path> [dry_run=1] [company_id=4]\n";
    exit(1);
}

$csv = $argv[1];
$dry = isset($argv[2]) ? (bool)$argv[2] : true;
$companyArg = isset($argv[3]) ? (int)$argv[3] : 4;

if (!file_exists($csv)) {
    echo "File not found: $csv\n";
    exit(1);
}

// ensure shop context - adjust if your shop id is different
Session::put('shop_id', 1);

$content = file_get_contents($csv);

if ($dry) {
    $request = Request::create('/products/price-import', 'POST', ['dry_run' => $dry ? 1 : 0, 'paste' => $content], [], []);
    $controller = new App\Http\Controllers\PriceImportController();
    $response = $controller->import($request);
} else {
    // run real product import (creates/updates products)
    $uploaded = new LaravelUploadedFile($csv, basename($csv), 'text/csv', null, true);
    $request = Request::create('/products/import', 'POST', [
        'mode' => 'upsert',
        'default_company_id' => $companyArg,
        'default_category_id' => 1,
    ], [], ['file' => $uploaded]);

    $controller = new App\Http\Controllers\ProductController();
    $response = $controller->importProcess($request);
}

if (is_object($response) && method_exists($response, 'getData')) {
    $data = $response->getData();
    echo "Import report:\n";
    print_r($data['report'] ?? $data);
} else {
    var_dump($response);
}
