<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShopProduct;
use App\Models\DiscountRule;
use App\Models\Category;
use App\Models\CategorySize;
use App\Services\PricingService;
use App\Models\Company;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PriceImportController extends Controller
{
    public function showForm()
    {
        $companies = Company::orderBy('name')->get();
        return view('mt.import.price_import', ['companies' => $companies]);
    }

    /**
     * Find or create a sub-category for a product type/variant.
     * 
     * Example: For type "Dura Air", company_id 1:
     * - Find category named "Dura Air" for company 1
     * - If not found, create it as a sub-category (or root if no parent exists)
     * 
     * @param string $typeValue Category/variant name (e.g., "Dura Air", "Dura Memory 2in1")
     * @param int $companyId Company ID for scoping
     * @return int Category ID
     */
    private function cleanCategoryName(string $name): string
    {
        $name = trim($name);
        $lower = strtolower($name);
        if (str_contains($lower, 'slab') || str_contains($lower, 'sheet')) {
            $cleaned = preg_replace('/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*$/i', '', $name);
            return trim($cleaned) ?: $name;
        }
        return $name;
    }

    private function findOrCreateSubcategory(string $typeValue, int $companyId): int
    {
        $typeValue = $this->cleanCategoryName($typeValue);
        $typeValue = trim($typeValue);
        if (empty($typeValue)) {
            // Fallback: use a default category
            return Category::where('company_id', $companyId)
                ->where('name', 'Uncategorized')
                ->first()?->id ?? Category::firstOrCreate(
                    ['company_id' => $companyId, 'name' => 'Uncategorized', 'unit_type' => 'unit'],
                    ['group_key' => null, 'parent_id' => null]
                )->id;
        }

        // Try to find existing category with this name for the company
        $existing = Category::where('company_id', $companyId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower($typeValue)])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // Find the root category for the company to set as parent for clean nesting
        $parentCategory = Category::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->first();

        // Create new sub-category
        $category = Category::create([
            'company_id' => $companyId,
            'name' => $typeValue,
            'unit_type' => 'unit',
            'group_key' => null,
            'parent_id' => $parentCategory ? $parentCategory->id : null,
        ]);

        $sourceCategory = Category::where('company_id', $companyId)
            ->where('id', '!=', $category->id)
            ->whereHas('sizes')
            ->orderBy('id')
            ->first();

        if ($sourceCategory) {
            $category->copySizesFrom($sourceCategory);
        }

        return $category->id;
    }

    private function inferDiscountTypeId(int $companyId, ?int $categoryId = null): ?int
    {
        $query = DiscountRule::where('scope_type', 'company')
            ->where('scope_id', $companyId);

        if ($categoryId) {
            $categoryIds = $this->collectCategoryRuleIds($categoryId);

            $orderBy = 'FIELD(scope_id,' . implode(',', array_map('intval', $categoryIds)) . ')';
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
                $cases = [];
                foreach ($categoryIds as $index => $id) {
                    $cases[] = "WHEN scope_id = " . (int)$id . " THEN " . ($index + 1);
                }
                $orderBy = "CASE " . implode(' ', $cases) . " ELSE " . (count($categoryIds) + 1) . " END";
            }

            $categoryRule = DiscountRule::where('scope_type', 'category')
                ->whereIn('scope_id', $categoryIds)
                ->orderByRaw($orderBy)
                ->orderBy('id')
                ->first();

            if ($categoryRule && !empty($categoryRule->discount_type_id)) {
                return (int) $categoryRule->discount_type_id;
            }
        }

        $rule = $query
            ->orderBy('id')
            ->first();

        return $rule && !empty($rule->discount_type_id) ? (int)$rule->discount_type_id : null;
    }

    private function collectCategoryRuleIds(int $categoryId): array
    {
        $ids = [$categoryId];

        $current = Category::find($categoryId);
        while ($current && $current->parent_id) {
            $parentId = (int) $current->parent_id;
            if (in_array($parentId, $ids, true)) {
                break;
            }
            $ids[] = $parentId;
            $current = $current->parent;
        }

        $childIds = Category::where('parent_id', $categoryId)->pluck('id')->map(fn ($id) => (int) $id)->all();
        foreach ($childIds as $childId) {
            if (!in_array($childId, $ids, true)) {
                $ids[] = $childId;
            }
        }

        return $ids;
    }

    private function estimatePurchasePrice(float $mrp, int $companyId, ?int $categoryId = null): float
    {
        if ($mrp <= 0) {
            return 0.0;
        }

        $product = new Product();
        $product->mrp = $mrp;
        $product->company_id = $companyId;
        $product->category_id = $categoryId;
        $product->discount_type_id = $this->inferDiscountTypeId($companyId, $categoryId);
        $product->pricing_source = 'pdf_import';

        return (float) PricingService::purchasePrice($product);
    }

    /**
     * Create a product with all predefined sizes from its category.
     * If dimensions are provided in the CSV, create a single product with those dimensions instead.
     */
    private function createProductWithSizes(
        string $name,
        int $categoryId,
        float $price,
        ?int $companyId = null,
        ?float $rowLen = null,
        ?float $rowWid = null,
        ?float $rowHei = null
    ): ?Product {
        $category = Category::find($categoryId);
        if (!$category) {
            return null;
        }

        $sizes = $category->sizes;
        // Ensure sizes are ordered by height (thickness), then length, then width
        if ($sizes->count() > 0) {
            $sizes = $sizes->sort(function ($a, $b) {
                $ha = $a->height_in ?? 0; $hb = $b->height_in ?? 0;
                if ($ha <=> $hb) return $ha <=> $hb;
                $la = $a->length_in ?? 0; $lb = $b->length_in ?? 0;
                if ($la <=> $lb) return $la <=> $lb;
                $wa = $a->width_in ?? 0; $wb = $b->width_in ?? 0;
                return $wa <=> $wb;
            })->values();
        }

        // If dimensions provided in CSV, create single product with those dimensions
        if (!is_null($rowLen) || !is_null($rowWid) || !is_null($rowHei)) {
            return Product::create([
                'name' => $name,
                'is_active' => 1,
                'pricing_source' => 'import',
                'category_id' => $categoryId,
                'company_id' => $companyId,
                'length_in' => $rowLen,
                'width_in' => $rowWid,
                'height_in' => $rowHei,
            ]);
        }

        // If no dimensions AND category has predefined sizes, create one product per size
        if ($sizes->count() > 0) {
            $firstProduct = null;
            foreach ($sizes as $size) {
                $product = Product::create([
                    'name' => $name . ' ' . $size->getDisplayName(),
                    'is_active' => 1,
                    'pricing_source' => 'import',
                    'category_id' => $categoryId,
                    'company_id' => $companyId,
                    'length_in' => $size->length_in,
                    'width_in' => $size->width_in,
                    'height_in' => $size->height_in,
                ]);
                if (!$firstProduct) {
                    $firstProduct = $product;
                }
            }
            return $firstProduct;
        }

        // No dimensions, no predefined sizes: create single product
        return Product::create([
            'name' => $name,
            'is_active' => 1,
            'pricing_source' => 'import',
            'category_id' => $categoryId,
            'company_id' => $companyId,
        ]);
    }

    public function import(Request $request)
    {
        @set_time_limit(180);
        $request->validate([
            'file' => ['nullable','file'],
            'files' => ['nullable','array'],
            'files.*' => ['nullable','file'],
            'paste' => ['nullable','string'],
            'dry_run' => ['nullable','boolean'],
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                if ($f) $uploadedFiles[] = $f;
            }
        }

        $allContents = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $allContents[] = file_get_contents($uploadedFile->getRealPath());
        }
        if ($request->filled('paste')) {
            $allContents[] = $request->input('paste');
        }

        if (empty($allContents)) {
            return back()->withErrors(['file' => 'Please upload a CSV file or paste tabular text.']);
        }

        $dryRun = (bool) $request->input('dry_run', false);
        $defaultCategoryId = $request->input('default_category_id') ?? null;
        $defaultCompanyId = $request->input('default_company_id') ?? null;
        $sid = ShopContext::requireSingleShopId();

        $report = [
            'total' => 0,
            'created_products' => 0,
            'updated_shop_prices' => 0,
            'created_shop_prices' => 0,
            'failed' => []
        ];

        DB::beginTransaction();
        try {
            foreach ($allContents as $contentIndex => $content) {
                // normalize newlines
                $content = str_replace(["\r\n", "\r"], "\n", $content);
                $lines = array_filter(array_map('trim', explode("\n", $content)));
                if (count($lines) === 0) {
                    continue;
                }

                // parse CSV rows
                $rows = array_map(function ($r) {
                    return str_getcsv($r);
                }, $lines);

                $header = array_map(function ($h) {
                    return Str::lower(trim($h));
                }, $rows[0]);

                // detect schema: either (name + price) OR (product_key,length,width,height,type,price)
                $nameIdx = null;
                $sellIdx = null;
                $productKeyIdx = null;
                $lengthIdx = null;
                $widthIdx = null;
                $heightIdx = null;
                $typeIdx = null;

                foreach ($header as $i => $h) {
                    if (in_array($h, ['name', 'product', 'product name', 'product_name'])) $nameIdx = $i;
                    if (in_array($h, ['sell', 'sell_price', 'sell price', 'selling_price', 'selling price', 'price'])) $sellIdx = $i;
                    if ($h === 'product_key') $productKeyIdx = $i;
                    if ($h === 'length') $lengthIdx = $i;
                    if ($h === 'width') $widthIdx = $i;
                    if ($h === 'height') $heightIdx = $i;
                    if ($h === 'type') $typeIdx = $i;
                }

                if ($productKeyIdx === null && ($nameIdx === null || $sellIdx === null)) {
                    throw new \Exception('Could not find required columns in file index ' . ($contentIndex + 1) . '. Expected either (name + price) or (product_key + price).');
                }

                // process rows (skip header)
                for ($r = 1; $r < count($rows); $r++) {
                    $report['total']++;
                    $cols = $rows[$r];
                    if ($productKeyIdx !== null) {
                        $productKey = isset($cols[$productKeyIdx]) ? trim($cols[$productKeyIdx]) : '';
                        $name = $productKey !== '' ? $productKey : (isset($cols[$nameIdx]) ? trim($cols[$nameIdx]) : '');
                        $priceRaw = isset($cols[$sellIdx]) ? $cols[$sellIdx] : '';
                    } else {
                        $name = isset($cols[$nameIdx]) ? trim($cols[$nameIdx]) : '';
                        $priceRaw = isset($cols[$sellIdx]) ? $cols[$sellIdx] : '';
                    }
                    $price = is_numeric($priceRaw) ? (float)$priceRaw : floatval(str_replace(',', '', $priceRaw));

                    if ($name === '') {
                        $report['failed'][] = ['row' => $r + 1, 'reason' => 'Empty product name'];
                        continue;
                    }

                    // parse possible dimensions from row
                    $rowLen = ($lengthIdx !== null && isset($cols[$lengthIdx]) && is_numeric($cols[$lengthIdx])) ? (float)$cols[$lengthIdx] : null;
                    $rowWid = ($widthIdx !== null && isset($cols[$widthIdx]) && is_numeric($cols[$widthIdx])) ? (float)$cols[$widthIdx] : null;
                    $rowHei = ($heightIdx !== null && isset($cols[$heightIdx]) && is_numeric($cols[$heightIdx])) ? (float)$cols[$heightIdx] : null;

                    // find product by name (trim, case-insensitive) and by dimensions if provided
                    $baseQuery = Product::whereRaw('LOWER(TRIM(name)) = ?', [Str::lower($name)]);
                    $product = null;
                    if (!is_null($rowLen) || !is_null($rowWid) || !is_null($rowHei)) {
                        $q = clone $baseQuery;
                        if (!is_null($rowLen)) $q->where('length_in', $rowLen);
                        if (!is_null($rowWid)) $q->where('width_in', $rowWid);
                        if (!is_null($rowHei)) $q->where('height_in', $rowHei);
                        $product = $q->first();
                    }

                    if (!$product) {
                        // fallback: any product with same name (no size match)
                        $product = $baseQuery->first();
                    }

                    if (!$product) {
                        if ($dryRun) {
                            $report['created_products']++;
                            $report['created_shop_prices']++;
                            continue;
                        }

                        // Determine category for new product
                        $categoryId = (int)$defaultCategoryId;
                        
                        // If type is provided and company is set, create sub-category
                        if ($typeIdx !== null && isset($cols[$typeIdx]) && trim($cols[$typeIdx]) !== '' && !empty($defaultCompanyId)) {
                            $typeValue = trim($cols[$typeIdx]);
                            $categoryId = $this->findOrCreateSubcategory($typeValue, (int)$defaultCompanyId);
                        }

                        // create product (category is required in this schema)
                        if (empty($categoryId)) {
                            $report['failed'][] = ['row' => $r + 1, 'reason' => 'Missing category_id for new product'];
                            continue;
                        }

                        // prepare payload with optional dimensions if present
                        $payload = ['name' => $name, 'is_active' => 1, 'pricing_source' => 'import', 'category_id' => $categoryId];

                        if (!is_null($rowLen)) {
                            $payload['length_in'] = $rowLen;
                        }
                        if (!is_null($rowWid)) {
                            $payload['width_in'] = $rowWid;
                        }
                        if (!is_null($rowHei)) {
                            $payload['height_in'] = $rowHei;
                        }

                        $product = Product::create($payload);

                        // attach default company if provided
                        if (!empty($defaultCompanyId)) {
                            $product->company_id = (int)$defaultCompanyId;
                        }

                        $product->save();
                        $report['created_products']++;
                    } else {
                        // existing product
                        $dimsProvided = (!is_null($rowLen) || !is_null($rowWid) || !is_null($rowHei));
                        $dimsMatch = true;
                        if ($dimsProvided) {
                            if (!is_null($rowLen) && ((float)$product->length_in !== $rowLen)) $dimsMatch = false;
                            if (!is_null($rowWid) && ((float)$product->width_in !== $rowWid)) $dimsMatch = false;
                            if (!is_null($rowHei) && ((float)$product->height_in !== $rowHei)) $dimsMatch = false;
                        }

                        if (!$dimsMatch && $dimsProvided) {
                            if ($dryRun) {
                                $report['created_products']++;
                                $report['created_shop_prices']++;
                                continue;
                            }

                            // Determine category for new product variant
                            $categoryId = (int)$defaultCategoryId;
                            
                            // If type is provided and company is set, create/find sub-category
                            if ($typeIdx !== null && isset($cols[$typeIdx]) && trim($cols[$typeIdx]) !== '' && !empty($defaultCompanyId)) {
                                $typeValue = trim($cols[$typeIdx]);
                                $categoryId = $this->findOrCreateSubcategory($typeValue, (int)$defaultCompanyId);
                            }

                            if (empty($categoryId)) {
                                $report['failed'][] = ['row' => $r + 1, 'reason' => 'Missing category_id for new product'];
                                continue;
                            }

                            $payload = ['name' => $name, 'is_active' => 1, 'pricing_source' => 'import', 'category_id' => $categoryId];
                            if (!is_null($rowLen)) $payload['length_in'] = $rowLen;
                            if (!is_null($rowWid)) $payload['width_in'] = $rowWid;
                            if (!is_null($rowHei)) $payload['height_in'] = $rowHei;
                            
                            $newProduct = Product::create($payload);
                            // attach default company if provided
                            if (!empty($defaultCompanyId)) {
                                $newProduct->company_id = (int)$defaultCompanyId;
                                $newProduct->save();
                            }

                            $product = $newProduct;
                            $report['created_products']++;
                        } else {
                            // update dimension fields when present
                            $update = [];
                            if (!is_null($rowLen)) $update['length_in'] = $rowLen;
                            if (!is_null($rowWid)) $update['width_in'] = $rowWid;
                            if (!is_null($rowHei)) $update['height_in'] = $rowHei;
                            if (!empty($update)) {
                                $product->update($update);
                            }
                        }
                        // attach default company if provided and not already set
                        if (!empty($defaultCompanyId) && empty($product->company_id)) {
                            $product->company_id = (int)$defaultCompanyId;
                            $product->save();
                        }
                    }

                    if ($dryRun) {
                        $exists = ShopProduct::where('shop_id', $sid)->where('product_id', $product->id)->exists();
                        if ($exists) $report['updated_shop_prices']++;
                        else $report['created_shop_prices']++;
                        continue;
                    }

                    $changed = false;
                    if (empty($product->mrp) || (float)$product->mrp <= 0) {
                        $product->mrp = $price;
                        $changed = true;
                    }

                    if (!empty($product->company_id)) {
                        $inferredDiscountTypeId = $this->inferDiscountTypeId((int)$product->company_id, $product->category_id);
                        if ((int)$product->discount_type_id !== (int)$inferredDiscountTypeId) {
                            $product->discount_type_id = $inferredDiscountTypeId;
                            $changed = true;
                        }
                    }

                    if ($changed) $product->save();

                    $computed = (float)PricingService::purchasePrice($product);
                    if ($computed > 0) {
                        $product->purchase_price_manual = $computed;
                        $product->save();
                    }

                    $sp = ShopProduct::where('shop_id', $sid)->where('product_id', $product->id)->first();
                    if ($sp) {
                        $sp->selling_price = $price;
                        if ($computed > 0) $sp->avg_cost = $computed;
                        $sp->save();
                        $report['updated_shop_prices']++;
                    } else {
                        ShopProduct::create([
                            'shop_id' => $sid,
                            'product_id' => $product->id,
                            'selling_price' => $price,
                            'stock_qty' => 0,
                            'avg_cost' => $computed > 0 ? $computed : 0,
                        ]);
                        $report['created_shop_prices']++;
                    }
                }
            }

            if (!$dryRun) DB::commit();
            else DB::rollBack();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'An error occurred during import: ' . $e->getMessage()]);
        }

        return view('mt.import.price_import', ['report' => $report, 'dry_run' => $dryRun]);
    }

    public function showPdfForm()
    {
        $companies = Company::orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('mt.import.price_import_pdf', [
            'companies' => $companies,
            'categories' => $categories
        ]);
    }

    public function importPdf(Request $request)
    {
        $request->validate([
            'file' => ['nullable', 'file', 'mimes:pdf,csv,txt'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'mimes:pdf,csv,txt'],
            'default_company_id' => ['required', 'exists:companies,id'],
            'default_category_id' => ['required', 'exists:categories,id'],
        ]);

        if (!$request->hasFile('file') && !$request->hasFile('files')) {
            return back()->withErrors(['file' => 'Please select at least one price list file to upload.']);
        }

        $companyId = $request->input('default_company_id');
        $categoryId = $request->input('default_category_id');

        $uploadedFiles = [];
        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                if ($f) $uploadedFiles[] = $f;
            }
        }

        try {
            $parser = new \App\Services\PdfPriceListParser();
            $parsedItems = [];

            foreach ($uploadedFiles as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext === 'pdf') {
                    $items = $parser->parse($file->getRealPath(), (int)$companyId);
                } else {
                    $items = $parser->parseCsv($file->getRealPath(), (int)$companyId);
                }

                foreach ($items as &$item) {
                    $item['purchase_price'] = $this->estimatePurchasePrice((float)($item['price'] ?? 0), (int)$companyId, (int)$categoryId);
                }
                unset($item);

                $parsedItems = array_merge($parsedItems, $items);
            }

            $companies = Company::orderBy('name')->get();
            $categories = Category::whereNull('parent_id')->orderBy('name')->get();

            return view('mt.import.price_import_pdf', [
                'companies' => $companies,
                'categories' => $categories,
                'parsedItems' => $parsedItems,
                'selectedCompanyId' => $companyId,
                'selectedCategoryId' => $categoryId,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error parsing files: ' . $e->getMessage()]);
        }
    }

    public function confirmPdfImport(Request $request)
    {
        @set_time_limit(180);
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'items' => ['required_without:items_json', 'array'],
            'items_json' => ['required_without:items', 'string'],
        ]);

        $companyId = (int)$request->input('company_id');
        $defaultCategoryId = (int)$request->input('category_id');
        
        if ($request->filled('items_json')) {
            $items = json_decode($request->input('items_json'), true) ?? [];
        } else {
            $items = $request->input('items', []);
        }
        
        $sid = ShopContext::requireSingleShopId();

        $report = [
            'total' => 0,
            'created_products' => 0,
            'updated_shop_prices' => 0,
            'created_shop_prices' => 0,
            'failed' => []
        ];

        DB::beginTransaction();
        try {
            foreach ($items as $idx => $item) {
                // Only import checked items
                if (empty($item['import'])) {
                    continue;
                }

                $report['total']++;

                $name = trim($item['name'] ?? '');
                $price = (float)($item['price'] ?? 0);
                $rowLen = isset($item['length_in']) && is_numeric($item['length_in']) ? (float)$item['length_in'] : null;
                $rowWid = isset($item['width_in']) && is_numeric($item['width_in']) ? (float)$item['width_in'] : null;
                $rowHei = isset($item['height_in']) && is_numeric($item['height_in']) ? (float)$item['height_in'] : null;

                $isSlab = str_contains(strtolower($name), 'slab') || str_contains(strtolower($name), 'sheet');
                if ($isSlab && ($rowHei === null || $rowHei == 0)) {
                    if (preg_match('/(?:\s|^)(\.\d+|\d+(?:\.\d+)?)\b/', $name, $matches)) {
                        $rowHei = (float)$matches[1];
                    }
                }

                if (empty($name)) {
                    $report['failed'][] = ['row' => $idx + 1, 'reason' => 'Empty product name'];
                    continue;
                }

                // Construct full name with size suffix (e.g. "Dura memory 2 in 1 72x36x4") if not present
                $fullName = $name;
                $sizeSuffix = '';
                if (!is_null($rowLen) && !is_null($rowWid)) {
                    $sizeSuffix = $rowLen . 'x' . $rowWid;
                    if (!is_null($rowHei) && $rowHei > 0) {
                        $sizeSuffix .= 'x' . $rowHei;
                    }
                }
                if ($sizeSuffix !== '' && !Str::contains(strtolower($name), strtolower($sizeSuffix))) {
                    $fullName = $name . ' ' . $sizeSuffix;
                }

                // Automatically find or create a specific sub-category for this product line (e.g., "Dura memory 2 in 1")
                // so that all these variant sizes are organized under their correct category!
                $categoryId = $this->findOrCreateSubcategory($name, $companyId);

                // Find or create product using full name
                $baseQuery = Product::whereRaw('LOWER(TRIM(name)) = ?', [Str::lower($fullName)]);
                $product = null;

                if (!is_null($rowLen) || !is_null($rowWid) || !is_null($rowHei)) {
                    $q = clone $baseQuery;
                    if (!is_null($rowLen)) $q->where('length_in', $rowLen);
                    if (!is_null($rowWid)) $q->where('width_in', $rowWid);
                    if (!is_null($rowHei)) $q->where('height_in', $rowHei);
                    $product = $q->first();
                }

                if (!$product) {
                    $product = $baseQuery->first();
                }

                if (!$product) {
                    // Create product
                    $payload = [
                        'name' => $fullName,
                        'is_active' => 1,
                        'pricing_source' => 'pdf_import',
                        'category_id' => $categoryId,
                        'company_id' => $companyId,
                    ];

                    if (!is_null($rowLen)) $payload['length_in'] = $rowLen;
                    if (!is_null($rowWid)) $payload['width_in'] = $rowWid;
                    if (!is_null($rowHei)) $payload['height_in'] = $rowHei;

                    $product = Product::create($payload);
                    $report['created_products']++;
                } else {
                    // Existing product dimensions check
                    $dimsMatch = true;
                    if (!is_null($rowLen) && ((float)$product->length_in !== $rowLen)) $dimsMatch = false;
                    if (!is_null($rowWid) && ((float)$product->width_in !== $rowWid)) $dimsMatch = false;
                    if (!is_null($rowHei) && ((float)$product->height_in !== $rowHei)) $dimsMatch = false;

                    if (!$dimsMatch) {
                        // Create a separate variant product
                        $payload = [
                            'name' => $fullName,
                            'is_active' => 1,
                            'pricing_source' => 'pdf_import',
                            'category_id' => $categoryId,
                            'company_id' => $companyId,
                        ];

                        if (!is_null($rowLen)) $payload['length_in'] = $rowLen;
                        if (!is_null($rowWid)) $payload['width_in'] = $rowWid;
                        if (!is_null($rowHei)) $payload['height_in'] = $rowHei;

                        $product = Product::create($payload);
                        $report['created_products']++;
                    } else {
                        // Update existing product
                        $update = [];
                        if (!is_null($rowLen)) $update['length_in'] = $rowLen;
                        if (!is_null($rowWid)) $update['width_in'] = $rowWid;
                        if (!is_null($rowHei)) $update['height_in'] = $rowHei;
                        if (!empty($update)) {
                            $product->update($update);
                        }
                    }

                    // Scope company
                    if (empty($product->company_id)) {
                        $product->company_id = $companyId;
                        $product->save();
                    }
                }

                // If mrp is missing, set it from incoming price
                $changed = false;
                if (empty($product->mrp) || (float)$product->mrp <= 0) {
                    $product->mrp = $price;
                    $changed = true;
                }

                // Auto-infer discount rule
                if (!empty($product->company_id)) {
                    $inferredDiscountTypeId = $this->inferDiscountTypeId((int)$product->company_id, $product->category_id);
                    if ((int)$product->discount_type_id !== (int)$inferredDiscountTypeId) {
                        $product->discount_type_id = $inferredDiscountTypeId;
                        $changed = true;
                    }
                }

                if ($changed) {
                    $product->save();
                }

                // Compute purchase price using PricingService
                $computed = (float)PricingService::purchasePrice($product);
                if ($computed > 0) {
                    $product->purchase_price_manual = $computed;
                    $product->save();
                }

                // Create or update shop pricing
                $sp = ShopProduct::where('shop_id', $sid)->where('product_id', $product->id)->first();
                if ($sp) {
                    $sp->selling_price = $price;
                    if ($computed > 0) $sp->avg_cost = $computed;
                    $sp->save();
                    $report['updated_shop_prices']++;
                } else {
                    ShopProduct::create([
                        'shop_id' => $sid,
                        'product_id' => $product->id,
                        'selling_price' => $price,
                        'stock_qty' => 0,
                        'avg_cost' => $computed > 0 ? $computed : 0,
                    ]);
                    $report['created_shop_prices']++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'An error occurred during import save: ' . $e->getMessage()]);
        }

        $companies = Company::orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('mt.import.price_import_pdf', [
            'companies' => $companies,
            'categories' => $categories,
            'report' => $report,
        ])->with('success', 'PDF Price List successfully imported!');
    }
}
