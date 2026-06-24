<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\DiscountType;
use App\Models\ShopProduct;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $shopId = ShopContext::activeShopId();

        if ($q === '') {
            $productGroups = collect();

            $companies = Company::orderBy('name')->get(['id', 'name']);
            foreach ($companies as $company) {
                $group = $this->cachedCompanyProductGroup((int) $company->id, $shopId);
                if ($group !== null) {
                    $productGroups->push($group);
                }
            }

            $uncategorizedGroup = $this->cachedCompanyProductGroup(null, $shopId);
            if ($uncategorizedGroup !== null) {
                $productGroups->push($uncategorizedGroup);
            }
        } else {
            $productGroups = $this->getProductGroups($q, $shopId);
        }

        $products = collect();

        return view('mt.products.index', compact('products', 'productGroups', 'q'));
    }

    private function cachedCompanyProductGroup(?int $companyId, $shopId): ?array
    {
        $companyKey = $companyId === null ? 'null' : (string) $companyId;
        $versionKey = "product_groups_company_version_{$companyKey}";
        $cacheVersion = (int) Cache::get($versionKey, 0);
        $cacheKey = "product_groups_shop_{$shopId}_company_{$companyKey}_v{$cacheVersion}";

        return Cache::remember($cacheKey, 3600, function () use ($companyId, $shopId) {
            return $this->buildCompanyProductGroup($companyId, $shopId);
        });
    }

    private function buildCompanyProductGroup(?int $companyId, $shopId): ?array
    {
        $productsQuery = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.sku',
                'products.company_id',
                'products.category_id',
                'products.mrp',
                'products.purchase_price_manual',
                'products.selling_price_default',
                'products.pricing_source',
                'products.max_discount_percent',
                'products.low_stock_alert_qty',
                'products.is_active',
                'products.created_at',
                'products.discount_type_id',
                'products.pricing_mode',
                'products.shell_rate',
                'products.mrp_markup',
                'products.width_in',
                'products.length_in',
                'products.height_in',
            ])
            ->when(is_numeric($shopId), function ($query) use ($shopId) {
                $query->leftJoin('shop_products as sp', function ($join) use ($shopId) {
                    $join->on('sp.product_id', '=', 'products.id')
                        ->where('sp.shop_id', '=', (int) $shopId);
                })->addSelect(DB::raw('COALESCE(sp.stock_qty, products.stock_qty) as stock_qty, COALESCE(sp.avg_cost, products.purchase_price_manual, 0) as buying_price'));
            }, function ($query) {
                $query->addSelect('products.stock_qty', DB::raw('COALESCE(products.purchase_price_manual, 0) as buying_price'));
            })
            ->with(['company', 'category', 'shopProducts.shop'])
            ->orderByDesc('products.id');

        if ($companyId === null) {
            $productsQuery->whereNull('products.company_id');
        } else {
            $productsQuery->where('products.company_id', $companyId);
        }

        $products = $productsQuery->get();

        if ($products->isEmpty()) {
            return null;
        }

        static $foamCategoryIds = null;
        if ($foamCategoryIds === null) {
            $foamCategoryIds = Category::where('name', 'like', '%foam%')->pluck('id')->all();
        }
        if (!empty($foamCategoryIds)) {
            $products = $products->sortBy(function ($p) use ($foamCategoryIds) {
                if (!in_array($p->category_id, $foamCategoryIds)) return PHP_INT_MAX;
                return [
                    (float)($p->height_in ?? 0),
                    (float)($p->length_in ?? 0),
                    (float)($p->width_in ?? 0),
                    $p->name,
                ];
            })->values();
        }

        $families = collect($products)
            ->groupBy(fn ($p) => $this->deriveProductFamilyName($p->name))
            ->map(function ($items) {
                return $items
                    ->sort(function ($a, $b) {
                        $isSlabA = false;
                        if ($a->category) {
                            $catLower = strtolower($a->category->name);
                            if (str_contains($catLower, 'slab') || str_contains($catLower, 'sheet')) {
                                $isSlabA = true;
                            }
                        }

                        $ha = (float)($a->height_in ?? 0);
                        $hb = (float)($b->height_in ?? 0);
                        if ($isSlabA) {
                            if ($ha != $hb) return $hb <=> $ha;
                        } else {
                            if ($ha != $hb) return $ha <=> $hb;
                        }

                        $la = (float)($a->length_in ?? 0);
                        $lb = (float)($b->length_in ?? 0);
                        if ($la != $lb) return $la <=> $lb;

                        $wa = (float)($a->width_in ?? 0);
                        $wb = (float)($b->width_in ?? 0);
                        if ($wa != $wb) return $wa <=> $wb;

                        return strcasecmp($a->name, $b->name);
                    })
                    ->values();
            })
            ->sortBy(fn ($items, $familyName) => strtolower((string) $familyName), SORT_NATURAL | SORT_FLAG_CASE);

        return [
            'name' => $products[0]->company?->name ?? '-',
            'families' => $families,
            'variant_count' => $products->count(),
        ];
    }

    private function serializeProductGroups($productGroups)
    {
        return $productGroups->map(function ($group) {
            $families = collect($group['families'])->map(function ($familyProducts) {
                return $familyProducts->map(function ($p) {
                    $pricingSource = strtolower((string)($p->pricing_source ?? ''));
                    $purchase = (float)($p->buying_price ?? 0);
                    if ($pricingSource !== '' && $pricingSource !== 'manual') {
                        $purchase = \App\Services\PricingService::purchasePrice($p);
                    } elseif ($purchase <= 0) {
                        $purchase = \App\Services\PricingService::purchasePrice($p);
                    }
                    
                    $attributes = $p->getAttributes();
                    $attributes['buying_price'] = $purchase;
                    
                    $shopProductsSerialized = [];
                    if ($p->relationLoaded('shopProducts')) {
                        foreach ($p->shopProducts as $sp) {
                            $shopProductsSerialized[] = [
                                'stock_qty' => $sp->stock_qty,
                                'shop_name' => $sp->shop->name ?? 'Shop',
                            ];
                        }
                    }
                    
                    return [
                        'attributes' => $attributes,
                        'company_attributes' => $p->company ? $p->company->getAttributes() : null,
                        'category_attributes' => $p->category ? $p->category->getAttributes() : null,
                        'shop_products' => $shopProductsSerialized,
                    ];
                })->all();
            })->all();

            return [
                'name' => $group['name'],
                'families' => $families,
                'variant_count' => $group['variant_count'],
            ];
        })->all();
    }

    private function deserializeProductGroups(array $cachedData)
    {
        return collect($cachedData)->map(function ($group) {
            $families = collect($group['families'])->map(function ($familyProducts) {
                return collect($familyProducts)->map(function ($item) {
                    $p = new \App\Support\ProductPresenter($item['attributes']);
                    
                    if ($item['company_attributes']) {
                        $p->company = (object)$item['company_attributes'];
                    }
                    if ($item['category_attributes']) {
                        $p->category = (object)$item['category_attributes'];
                    }
                    if (isset($item['shop_products'])) {
                        $p->shop_products = $item['shop_products'];
                    } else {
                        $p->shop_products = [];
                    }
                    return $p;
                });
            });

            return [
                'name' => $group['name'],
                'families' => $families,
                'variant_count' => $group['variant_count'],
            ];
        });
    }

    private function getProductGroups(string $q, $shopId)
    {
        $productsQuery = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.sku',
                'products.company_id',
                'products.category_id',
                'products.mrp',
                'products.purchase_price_manual',
                'products.selling_price_default',
                'products.max_discount_percent',
                'products.low_stock_alert_qty',
                'products.is_active',
                'products.created_at',
                // include pricing inputs so PricingService can compute when needed
                'products.discount_type_id',
                'products.pricing_mode',
                'products.shell_rate',
                'products.mrp_markup',
                'products.width_in',
                'products.length_in',
                'products.height_in',
            ])
            ->when(is_numeric($shopId), function ($query) use ($shopId) {
                $query->leftJoin('shop_products as sp', function ($join) use ($shopId) {
                    $join->on('sp.product_id', '=', 'products.id')
                        ->where('sp.shop_id', '=', (int) $shopId);
                })->addSelect(\Illuminate\Support\Facades\DB::raw('COALESCE(sp.stock_qty, products.stock_qty) as stock_qty, COALESCE(sp.avg_cost, products.purchase_price_manual, 0) as buying_price'));
            }, function ($query) {
                // no active shop: still expose stock and buying price from product
                $query->addSelect('products.stock_qty', \Illuminate\Support\Facades\DB::raw('COALESCE(products.purchase_price_manual, 0) as buying_price'));
            })
            ->with(['company', 'category', 'shopProducts.shop'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function($qq) use ($q) {
                    $qq->where('products.name', 'like', "%{$q}%")
                       ->orWhere('products.sku', 'like', "%{$q}%")
                       ->when(is_numeric($q), fn ($idQuery) => $idQuery->orWhere('products.id', (int) $q));
                });
            })
            ->orderByDesc('products.id');

        $products = $productsQuery->get();

        // If foam category, order by height, length, width ascending
        $foamCategoryIds = Category::where('name', 'like', '%foam%')->pluck('id')->all();
        if (!empty($foamCategoryIds)) {
            $products = $products->sortBy(function ($p) use ($foamCategoryIds) {
                if (!in_array($p->category_id, $foamCategoryIds)) return PHP_INT_MAX;
                return [
                    (float)($p->height_in ?? 0),
                    (float)($p->length_in ?? 0),
                    (float)($p->width_in ?? 0),
                    $p->name,
                ];
            })->values();
        }

        $productGroups = $products
            ->groupBy(fn ($p) => $p->company?->name ?? '-')
            ->map(function ($companyProducts) {
                $families = collect($companyProducts)
                    ->groupBy(fn ($p) => $this->deriveProductFamilyName($p->name))
                    ->map(function ($items) {
                        return $items
                            ->sort(function ($a, $b) {
                                $isSlabA = false;
                                if ($a->category) {
                                    $catLower = strtolower($a->category->name);
                                    if (str_contains($catLower, 'slab') || str_contains($catLower, 'sheet')) {
                                        $isSlabA = true;
                                    }
                                }

                                $ha = (float)($a->height_in ?? 0);
                                $hb = (float)($b->height_in ?? 0);
                                if ($isSlabA) {
                                    if ($ha != $hb) return $hb <=> $ha; // descending height for slab sheets
                                } else {
                                    if ($ha != $hb) return $ha <=> $hb; // ascending height for standard
                                }

                                $la = (float)($a->length_in ?? 0);
                                $lb = (float)($b->length_in ?? 0);
                                if ($la != $lb) return $la <=> $lb; // ascending length

                                $wa = (float)($a->width_in ?? 0);
                                $wb = (float)($b->width_in ?? 0);
                                if ($wa != $wb) return $wa <=> $wb; // ascending width

                                return strcasecmp($a->name, $b->name);
                            })
                            ->values();
                    })
                    ->sortBy(fn ($items, $familyName) => strtolower((string) $familyName), SORT_NATURAL | SORT_FLAG_CASE);

                return [
                    'name' => $companyProducts[0]->company?->name ?? '-',
                    'families' => $families,
                    'variant_count' => $companyProducts->count(),
                ];
            })
            ->values();

        return $productGroups;
    }

    public function create()
    {
        $categories = Category::with('children', 'company')
            ->orderBy('name')
            ->get();
        $companies = Company::orderBy('name')->get();
        $discountTypes = DiscountType::orderBy('name')->get();

        $product = new Product();
        // ✅ default so pricing_mode never null
        $product->pricing_mode = 'manual';

        $sheetDesigns = \App\Models\SheetDesign::orderBy('name')->get();

        return view('mt.products.create', compact('product', 'categories', 'companies', 'discountTypes', 'sheetDesigns'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // checkbox fix
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // ✅ pricing_mode cannot be null in DB
        $data['pricing_mode'] = trim((string)($data['pricing_mode'] ?? 'manual'));
        if ($data['pricing_mode'] === '') {
            $data['pricing_mode'] = 'manual';
        }

        // optional (if you use pricing_source)
        if (!isset($data['pricing_source']) || trim((string)$data['pricing_source']) === '') {
            $data['pricing_source'] = 'manual';
        }

        if (array_key_exists('max_discount_percent', $data) && $data['max_discount_percent'] === '') {
            $data['max_discount_percent'] = null;
        }

        // ✅ Default stock quantities to 0 if not provided (DB columns don't allow null)
        if (!isset($data['stock_qty']) || $data['stock_qty'] === null || $data['stock_qty'] === '') {
            $data['stock_qty'] = 0;
        }
        if (!isset($data['low_stock_alert_qty']) || $data['low_stock_alert_qty'] === null || $data['low_stock_alert_qty'] === '') {
            $data['low_stock_alert_qty'] = 0;
        }

        // Check if category has predefined sizes
        $category = Category::find($data['category_id'] ?? null);
        $sizes = $category?->sizes ?? collect();
        // Order sizes by height (thickness), then length, then width to match desired variant ordering
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
        $selectedSizes = array_filter((array)($request->get('selected_sizes', [])), fn($item) => is_string($item) && $item !== '');

        // If no checkbox sizes selected, allow custom_sizes input (comma-separated)
        if (empty($selectedSizes)) {
            $custom = (string)($request->get('custom_sizes', ''));
            if (trim($custom) !== '') {
                $parts = array_map('trim', explode(',', $custom));
                $parts = array_filter($parts, fn($v) => $v !== '');
                $selectedSizes = array_values($parts);
            }
        }

        if (count($selectedSizes) > 0) {
            $createdProductIds = [];

            // index category sizes by display name for lookup
            $sizesByName = $sizes->mapWithKeys(fn($s) => [$s->getDisplayName() => $s]);

            // Ensure variant creation follows the same small-to-large size order as the size list
            $orderedSelectedSizes = collect($selectedSizes)
                ->sortBy(function (string $szName) use ($sizesByName) {
                    $size = $sizesByName[$szName] ?? null;
                    return [
                        (float)($size->height_in ?? 0),
                        (float)($size->length_in ?? 0),
                        (float)($size->width_in ?? 0),
                        $szName,
                    ];
                })
                ->values()
                ->all();

            foreach ($orderedSelectedSizes as $szName) {
                $productData = $data;
                $productData['name'] = $data['name'] . ' ' . $szName;

                if (isset($sizesByName[$szName])) {
                    $size = $sizesByName[$szName];
                    $productData['length_in'] = $size->length_in;
                    $productData['width_in'] = $size->width_in;
                    $productData['height_in'] = $size->height_in;
                } else {
                    // custom size — leave dimensions null so user can fill later
                    $productData['length_in'] = $productData['width_in'] = $productData['height_in'] = null;
                }

                // Clear pricing fields so user adds them manually per quality
                $productData['mrp'] = null;
                $productData['selling_price_default'] = null;
                $productData['purchase_price_manual'] = null;

                $product = Product::create($productData);
                $this->syncActiveShopInventory($product, $productData);
                $createdProductIds[] = $product->id;
            }

            $this->bumpProductGroupsCacheVersionForCompany($data['company_id'] ?? null);

            if (!empty($createdProductIds)) {
                return redirect()->route('mt.products.bulk_price', [
                    'ids' => implode(',', $createdProductIds),
                    'name' => $data['name'],
                ]);
            }
        }

        // No predefined sizes or no selected sizes: create single product
        if ((!isset($data['mrp']) || (float)$data['mrp'] <= 0) && !empty($data['category_id']) && !empty($data['company_id'])) {
            $catObj = Category::find($data['category_id']);
            if ($catObj && $catObj->isLamination()) {
                $compObj = Company::find($data['company_id']);
                if ($compObj && $compObj->default_lamination_rate !== null && $compObj->default_lamination_rate > 0) {
                    $data['mrp'] = (float)$compObj->default_lamination_rate;
                }
            }
        }
        $product = Product::create($data);
        $this->syncActiveShopInventory($product, $data);
        $this->bumpProductGroupsCacheVersionForCompany($data['company_id'] ?? null);
        return redirect()->route('mt.products.index')->with('success', 'Product created.');
    }

    private function deriveProductFamilyName(string $name): string
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '') {
            return '';
        }

        $patterns = [
            '/\s+(?:\d+(?:\.\d+)?(?:\s*[x×]\s*\d+(?:\.\d+)?)+)(?:\s*(?:ft|feet|in|inch|inches|["\']+))?$/i',
            '/\s+\d+(?:\.\d+)?(?:\s*(?:ft|feet|in|inch|inches|["\']+))?$/i',
        ];

        foreach ($patterns as $pattern) {
            $family = preg_replace($pattern, '', $trimmed);
            if ($family !== null && trim($family) !== $trimmed) {
                $trimmed = trim((string) $family);
                break;
            }
        }

        // Additional cleanup for slab sheets
        $lower = strtolower($trimmed);
        if (str_contains($lower, 'slab') || str_contains($lower, 'sheet')) {
            $trimmed = preg_replace('/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*$/i', '', $trimmed);
            $trimmed = trim($trimmed);
        }

        return $trimmed;
    }

    public function edit(Product $product)
    {
        $categories = Category::with('children', 'company')
            ->orderBy('name')
            ->get();
        $companies = Company::orderBy('name')->get();
        $discountTypes = DiscountType::orderBy('name')->get();

        // ✅ ensure not null
        if (empty($product->pricing_mode)) {
            $product->pricing_mode = 'manual';
        }

        $sheetDesigns = \App\Models\SheetDesign::orderBy('name')->get();

        return view('mt.products.edit', compact('product', 'categories', 'companies', 'discountTypes', 'sheetDesigns'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $oldCompanyId = $product->company_id;

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // ✅ pricing_mode cannot be null in DB
        $pm = trim((string)($data['pricing_mode'] ?? ''));
        if ($pm === '') {
            $data['pricing_mode'] = $product->pricing_mode ?: 'manual';
        }

        // optional
        if (isset($data['pricing_source']) && trim((string)$data['pricing_source']) === '') {
            unset($data['pricing_source']);
        }

        if (array_key_exists('max_discount_percent', $data) && $data['max_discount_percent'] === '') {
            $data['max_discount_percent'] = null;
        }

        $product->update($data);
        $this->syncActiveShopInventory($product, $data);
        $this->bumpProductGroupsCacheVersionForCompany($oldCompanyId);
        $this->bumpProductGroupsCacheVersionForCompany($product->company_id ?? null);

        return redirect()->route('mt.products.index')->with('success', 'Product updated.');
    }



    // ----------------------------
    // Import (CSV) - Excel can be exported to CSV
    // ----------------------------

    public function importForm()
    {
        $companies = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $discountTypes = DiscountType::orderBy('name')->get();

        return view('mt.products.import', compact('companies', 'categories', 'discountTypes'));
    }

    public function downloadImportTemplate()
    {
        $csv = implode("\n", [
            'name,sku,mrp,selling_price_default,purchase_price_manual,stock_qty,low_stock_alert_qty,pricing_mode,shell_rate,width_in,length_in,height_in,sheet_full_w,sheet_full_l,category,company',
            'Molty Foam Mattress 78x42x3,MF-78423,10000,15000,,10,2,manual,,,,,6,3,Foam Mattressess,Master Foam',
            'Jumbolon Shell 22x22x3.5,JS-22223,0,0,,5,1,shell,250,22,22,3.5,,,Sofa accessories,',
        ]);

        $filename = 'products_import_template.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importProcess(Request $request)
    {
        @set_time_limit(180);
        $data = $request->validate([
            'file' => ['nullable', 'file', 'mimes:csv,txt'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'mimes:csv,txt'],
            'mode' => ['nullable', 'string', 'in:upsert,create_only'],
            'default_company_id' => ['nullable', 'integer'],
            'default_category_id' => ['nullable', 'integer'],
            'default_discount_type_id' => ['nullable', 'integer'],
            'default_is_active' => ['nullable', 'in:1,0'],
        ]);

        if (!$request->hasFile('file') && !$request->hasFile('files')) {
            return back()->with('error', 'Please select at least one CSV file to upload.');
        }

        $mode = (string)($data['mode'] ?? 'upsert');
        $defaultCompanyId = $data['default_company_id'] ?? null;
        $defaultCategoryId = $data['default_category_id'] ?? null;
        $defaultDiscountTypeId = $data['default_discount_type_id'] ?? null;
        $defaultIsActive = isset($data['default_is_active']) ? (int)$data['default_is_active'] : 1;

        $uploadedFiles = [];
        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                if ($f) $uploadedFiles[] = $f;
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $totalRowsRead = 0;
        $errorDetails = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $path = $uploadedFile->getRealPath();
            $fh = fopen($path, 'r');
            if (!$fh) {
                $errors++;
                $errorDetails[] = "Failed to open file: " . $uploadedFile->getClientOriginalName();
                continue;
            }

            // Handle BOM (Byte Order Mark) for UTF-8 files
            $bom = fread($fh, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($fh);
            }

            $header = fgetcsv($fh);
            if (!$header) {
                fclose($fh);
                $errors++;
                $errorDetails[] = "CSV header row missing in file: " . $uploadedFile->getClientOriginalName();
                continue;
            }

            $headerMap = [];
            foreach ($header as $i => $h) {
                $key = strtolower(trim((string)$h));
                $headerMap[$key] = $i;
            }

            // Accept supplier CSVs that use `product_key` as the identifier
            if (isset($headerMap['product_key']) && !isset($headerMap['name'])) {
                $headerMap['name'] = $headerMap['product_key'];
            }
            // Accept simple 'price' header as selling_price_default
            if (isset($headerMap['price']) && !isset($headerMap['selling_price_default'])) {
                $headerMap['selling_price_default'] = $headerMap['price'];
            }

            $get = function(array $row, string $key) use ($headerMap) {
                $k = strtolower(trim($key));
                if (!isset($headerMap[$k])) return null;
                $idx = $headerMap[$k];
                return isset($row[$idx]) ? trim((string)$row[$idx]) : null;
            };

            // Clean price value: "Rs. 29,600" -> 29600
            $cleanPrice = function(string $priceStr) {
                $priceStr = preg_replace('/[^0-9.]/', '', $priceStr);
                return $priceStr ? (float)$priceStr : null;
            };

            $fileRowsRead = 0;
            while (($row = fgetcsv($fh)) !== false) {
                $fileRowsRead++;
                $totalRowsRead++;
                
                // Skip embedded headers (rows where first column is "Product Name" or contains "Size" and "MRP")
                $firstCol = isset($row[0]) ? strtolower(trim((string)$row[0])) : '';
                if ($firstCol === 'product name' || $firstCol === 'name' || 
                    (isset($row[1]) && strtolower(trim((string)$row[1])) === 'size' && 
                     isset($row[2]) && strtolower(trim((string)$row[2])) === 'mrp')) {
                    $skipped++;
                    continue;
                }
                
                // skip empty rows
                $name = $get($row, 'name');
                if ($name === null || $name === '') {
                    $skipped++;
                    continue;
                }

                $sku = $get($row, 'sku') ?: null;

                // Parse Size field early so SKU generator can use them
                $widthIn = $get($row, 'width_in');
                $lengthIn = $get($row, 'length_in');
                $heightIn = $get($row, 'height_in');
                
                $isSlab = str_contains(strtolower($name), 'slab') || str_contains(strtolower($name), 'sheet');
                if ($isSlab && ($heightIn === null || $heightIn === '' || (float)$heightIn == 0)) {
                    if (preg_match('/(?:\s|^)(\.\d+|\d+(?:\.\d+)?)\b/', $name, $matches)) {
                        $heightIn = (float)$matches[1];
                    }
                }
                
                $sizeField = $get($row, 'size');
                if ($sizeField && !$widthIn && !$lengthIn && !$heightIn) {
                    $sizeParts = preg_split('/\s+/', trim($sizeField));
                    if (count($sizeParts) >= 3) {
                        $widthIn = (float)$sizeParts[0] ?: null;
                        $lengthIn = (float)$sizeParts[1] ?: null;
                        $heightIn = (float)$sizeParts[2] ?: null;
                    }
                } else {
                    $widthIn = ($widthIn !== null && $widthIn !== '') ? (float)$widthIn : null;
                    $lengthIn = ($lengthIn !== null && $lengthIn !== '') ? (float)$lengthIn : null;
                    $heightIn = ($heightIn !== null && $heightIn !== '') ? (float)$heightIn : null;
                }
                
                $sheetW = $get($row, 'sheet_full_w');
                $sheetW = ($sheetW !== null && $sheetW !== '') ? (float)$sheetW : null;

                $sheetL = $get($row, 'sheet_full_l');
                $sheetL = ($sheetL !== null && $sheetL !== '') ? (float)$sheetL : null;

                // If no SKU provided, generate one from name + dimensions (if available)
                if (!$sku && ($widthIn || $lengthIn || $heightIn)) {
                    $formatSkuDimension = function ($value): string {
                        return rtrim(rtrim(number_format((float)$value, 3, '.', ''), '0'), '.');
                    };
                    $dimParts = [];
                    if ($widthIn) $dimParts[] = $formatSkuDimension($widthIn);
                    if ($lengthIn) $dimParts[] = $formatSkuDimension($lengthIn);
                    if ($heightIn) $dimParts[] = $formatSkuDimension($heightIn);
                    $baseSku = strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $name), 0, 6));
                    $sku = $baseSku . '-' . implode('x', $dimParts);
                }
                
                // If still no SKU (name + no dimensions), use row number to ensure uniqueness
                if (!$sku) {
                    $baseSku = strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $name), 0, 6));
                    $sku = $baseSku . '-F' . $created . 'R' . $fileRowsRead;
                }
                
                // Ensure SKU is not null
                if (!$sku) {
                    $sku = 'SKU-' . bin2hex(random_bytes(4));
                }

                // company/category (optional columns)
                $companyName = $get($row, 'company') ?: $get($row, 'company_name');
                $categoryName = $get($row, 'category') ?: $get($row, 'category_name');

                $companyId = $defaultCompanyId;
                if ($companyName) {
                    $co = Company::where('name', $companyName)->first();
                    if ($co) {
                        $companyId = $co->id;
                    } else {
                        // Auto-create missing company
                        $co = Company::create(['name' => $companyName]);
                        $companyId = $co->id;
                    }
                }

                $categoryId = $defaultCategoryId;
                if ($categoryName) {
                    $categoryName = $this->cleanCategoryName($categoryName);
                    $cat = Category::where('name', $categoryName)
                        ->when($companyId, function($q) use ($companyId) {
                            $q->where('company_id', $companyId);
                        })
                        ->first();
                    if ($cat) {
                        $categoryId = $cat->id;
                    } else {
                        // Auto-create missing category, cleanly nested under root category if exists
                        $parentCat = null;
                        if ($companyId) {
                            $parentCat = Category::where('company_id', $companyId)
                                ->whereNull('parent_id')
                                ->first();
                        }
                        $cat = Category::create([
                            'name' => $categoryName,
                            'company_id' => $companyId ?: null,
                            'unit_type' => 'unit',
                            'parent_id' => $parentCat ? $parentCat->id : null,
                        ]);
                        $categoryId = $cat->id;
                    }
                }

                // try to infer a discount_type_id from category/company rules
                $inferredDiscountTypeId = null;
                if ($companyId) {
                    $inferredDiscountTypeId = $this->inferDiscountTypeId((int)$companyId, $categoryId);
                }

                $mrpRaw = $get($row, 'mrp');
                $mrp = ($mrpRaw !== null && $mrpRaw !== '') ? $cleanPrice($mrpRaw) : 0;
                if ($mrp === null) $mrp = 0;
                
                $sellRaw = $get($row, 'selling_price_default') ?: $get($row, 'price');
                $sell = ($sellRaw !== null && $sellRaw !== '') ? $cleanPrice($sellRaw) : null;

                // If mrp is not provided but selling price is, use selling price as mrp
                if ($mrp <= 0 && $sell !== null) {
                    $mrp = (float)$sell;
                }

                if ($mrp <= 0 && $categoryId && $companyId) {
                    $catObj = \App\Models\Category::find($categoryId);
                    if ($catObj && $catObj->isLamination()) {
                        $compObj = \App\Models\Company::find($companyId);
                        if ($compObj && $compObj->default_lamination_rate !== null && $compObj->default_lamination_rate > 0) {
                            $mrp = (float)$compObj->default_lamination_rate;
                        }
                    }
                }

                $purchaseRaw = $get($row, 'purchase_price_manual');
                $purchaseManual = ($purchaseRaw !== null && $purchaseRaw !== '') ? $cleanPrice($purchaseRaw) : null;

                $stockQty = $get($row, 'stock_qty');
                $stockQty = ($stockQty !== null && $stockQty !== '') ? (float)$stockQty : 0;

                $lowAlert = $get($row, 'low_stock_alert_qty');
                $lowAlert = ($lowAlert !== null && $lowAlert !== '') ? (float)$lowAlert : null;

                $pricingMode = strtolower((string)($get($row, 'pricing_mode') ?: 'manual'));
                if (!in_array($pricingMode, ['manual','shell'], true)) $pricingMode = 'manual';

                $shellRate = $get($row, 'shell_rate');
                $shellRate = ($shellRate !== null && $shellRate !== '') ? (float)$shellRate : null;

                $designName = $get($row, 'sheet_design') ?: $get($row, 'design') ?: null;
                $designId = null;
                if ($designName !== null && trim($designName) !== '') {
                    $designNameClean = trim($designName);
                    $design = null;

                    if ($companyId) {
                        $design = \App\Models\SheetDesign::findByCompanyCode($companyId, $designNameClean);
                    }

                    if (!$design) {
                        $design = \App\Models\SheetDesign::where('name', $designNameClean)->first();
                    }

                    if (!$design) {
                        $design = \App\Models\SheetDesign::create(['name' => $designNameClean]);
                    }

                    $designId = $design->id;
                }

                try {
                    // Validate data before processing
                    if (empty($name) || !is_string($name)) {
                        throw new \Exception("Product name is required and must be a string");
                    }

                    if (empty($categoryId)) {
                        throw new \Exception("Category is required (could not be resolved from CSV 'category' column or default selection)");
                    }

                    if ($mrp !== null && !is_numeric($mrp)) {
                        throw new \Exception("MRP must be numeric");
                    }

                    if ($sell !== null && !is_numeric($sell)) {
                        throw new \Exception("Selling price must be numeric");
                    }

                    if ($purchaseManual !== null && !is_numeric($purchaseManual)) {
                        throw new \Exception("Purchase price must be numeric");
                    }

                    if ($stockQty !== null && !is_numeric($stockQty)) {
                        throw new \Exception("Stock quantity must be numeric");
                    }

                    $query = Product::query();

                    // Always query by SKU first (it should be unique)
                    $query->where('sku', $sku);

                    // Only filter by company if one is specified
                    if ($companyId) {
                        $query->where('company_id', $companyId);
                    }

                    $existing = $query->first();
                    
                    if (!$existing && !$companyId) {
                        // If no company specified, also check for products with same name but no company
                        // to avoid duplicates
                        $byName = Product::where('name', $name)
                            ->whereNull('company_id')
                            ->where('sku', '!=', $sku)
                            ->first();
                        
                        if ($byName) {
                            $existing = $byName;
                            \Log::info("CSV Import Found by Name", [
                                'old_sku' => $existing->sku,
                                'new_sku' => $sku,
                                'name' => $name,
                             ]);
                        }
                    }

                    $payload = [
                        'name' => $name,
                        'sku' => $sku,
                        'company_id' => $companyId,
                        'category_id' => $categoryId,
                        'sheet_design_id' => $designId,
                        'discount_type_id' => ($inferredDiscountTypeId ?? $defaultDiscountTypeId),
                        'mrp' => $mrp > 0 ? $mrp : null,
                        'selling_price_default' => $sell,
                        'purchase_price_manual' => $purchaseManual,
                        'stock_qty' => $stockQty,
                        'low_stock_alert_qty' => $lowAlert,
                        'pricing_mode' => $pricingMode,
                        'shell_rate' => $shellRate,
                        'width_in' => $widthIn,
                        'length_in' => $lengthIn,
                        'height_in' => $heightIn,
                        'sheet_full_w' => $sheetW,
                        'sheet_full_l' => $sheetL,
                        'is_active' => $defaultIsActive,
                        'pricing_source' => 'import',
                    ];

                    // remove nulls so we don't overwrite existing values accidentally
                    $payload = array_filter($payload, function ($v) {
                        return $v !== null;
                    });

                    if ($existing) {
                        if ($mode === 'create_only') {
                            $skipped++;
                            continue;
                        }
                        $oldCompanyId = $existing->company_id;
                        // if we inferred a company discount type and it differs, update it
                        if (($existing->discount_type_id ?? 0) !== (int)$inferredDiscountTypeId) {
                            $payload['discount_type_id'] = $inferredDiscountTypeId ? (int)$inferredDiscountTypeId : null;
                        }

                        $existing->update($payload);
                        $this->syncActiveShopInventory($existing, $payload);
                        $this->bumpProductGroupsCacheVersionForCompany($oldCompanyId);
                        $this->bumpProductGroupsCacheVersionForCompany($existing->company_id ?? null);
                        $updated++;
                        \Log::info("CSV Import Update", [
                            'product_id' => $existing->id,
                            'sku' => $existing->sku,
                            'name' => $existing->name,
                        ]);
                    } else {
                        // ensure required fields
                        if (!isset($payload['pricing_mode'])) $payload['pricing_mode'] = 'manual';
                        
                        try {
                            $product = Product::create($payload);
                            $this->syncActiveShopInventory($product, $payload);
                            $this->bumpProductGroupsCacheVersionForCompany($product->company_id ?? null);
                            $created++;
                            \Log::info("CSV Import Created", [
                                'product_id' => $product->id,
                                'sku' => $product->sku,
                                'name' => $product->name,
                                'width_in' => $product->width_in,
                                'length_in' => $product->length_in,
                                'height_in' => $product->height_in,
                            ]);
                        } catch (\Throwable $e) {
                            $errors++;
                            \Log::error("CSV Import Create Failed", [
                                'name' => $name,
                                'error' => $e->getMessage(),
                            ]);
                            throw $e;
                        }
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $fileNameOnly = $uploadedFile->getClientOriginalName();
                    $errorDetails[] = "[{$fileNameOnly}] Row {$fileRowsRead} ('{$name}'): " . $e->getMessage();
                }
            }

            fclose($fh);
        }

        // Log error details if any
        if (!empty($errorDetails)) {
            \Log::warning("CSV Import Errors", ['details' => $errorDetails]);
        }

        $message = "Import complete: Created {$created}, Updated {$updated}, Skipped {$skipped}, Errors {$errors}, Total rows read {$totalRowsRead}.";
        if (!empty($errorDetails)) {
            $message .= " See details below.";
            return redirect()->route('mt.products.index')
                ->with('success', $message)
                ->with('import_errors', $errorDetails);
        }

        return redirect()->route('mt.products.index')->with('success', $message);
    }
    public function destroy(Product $product)
    {
        $this->bumpProductGroupsCacheVersionForCompany($product->company_id ?? null);
        $product->delete();
        return redirect()->route('mt.products.index')->with('success', 'Product deleted.');
    }

    private function bumpProductGroupsCacheVersionForCompany(?int $companyId): void
    {
        $companyKey = $companyId === null ? 'null' : (string) $companyId;
        $versionKey = "product_groups_company_version_{$companyKey}";
        Cache::forever($versionKey, (int) Cache::get($versionKey, 0) + 1);
    }

    public function trash(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $products = Product::onlyTrashed()
            ->with(['company','category'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->orderByDesc('deleted_at')
            ->simplePaginate(25)
            ->withQueryString();

        return view('mt.products.trash', compact('products', 'q'));
    }

    public function restore(Product $product)
    {
        $p = Product::onlyTrashed()->findOrFail($product->id);
        $p->restore();
        return redirect()->route('mt.products.trash')->with('success', 'Product restored.');
    }

    public function forceDestroy(Product $product)
    {
        $p = Product::onlyTrashed()->findOrFail($product->id);
        $p->forceDelete();
        return redirect()->route('mt.products.trash')->with('success', 'Product permanently deleted.');
    }

    public function bulkRestore(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer']
        ]);

        $ids = $data['ids'] ?? [];
        if (count($ids) === 0) return redirect()->route('mt.products.trash')->with('error', 'No items selected.');

        DB::beginTransaction();
        try {
            Product::onlyTrashed()->whereIn('id', $ids)->restore();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('mt.products.trash')->with('error', 'Error restoring: ' . $e->getMessage());
        }

        return redirect()->route('mt.products.trash')->with('success', 'Selected products restored.');
    }

    public function bulkForceDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer']
        ]);

        $ids = $data['ids'] ?? [];
        if (count($ids) === 0) return redirect()->route('mt.products.trash')->with('error', 'No items selected.');

        DB::beginTransaction();
        try {
            Product::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('mt.products.trash')->with('error', 'Error deleting: ' . $e->getMessage());
        }

        return redirect()->route('mt.products.trash')->with('success', 'Selected products permanently deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer','exists:products,id']
        ]);

        $ids = $data['ids'] ?? [];
        if (count($ids) === 0) {
            return redirect()->route('mt.products.index')->with('error', 'No products selected.');
        }

        // delete in a transaction using Eloquent destroy() so SoftDeletes are honored
        \DB::beginTransaction();
        try {
            Product::destroy($ids);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect()->route('mt.products.index')->with('error', 'Error deleting products: ' . $e->getMessage());
        }

        return redirect()->route('mt.products.index')->with('success', 'Selected products deleted.');
    }

    public function bulkDestroyByKeyword(Request $request)
    {
        $request->validate([
            'keyword' => ['required', 'string', 'min:3'],
        ]);

        $keyword = trim($request->input('keyword'));

        // Query products matching this name or SKU pattern
        $products = Product::where('name', 'like', "%{$keyword}%")
            ->orWhere('sku', 'like', "%{$keyword}%")
            ->get();

        $count = $products->count();

        if ($count === 0) {
            return redirect()->route('mt.products.index')->with('error', "No products found containing '{$keyword}'.");
        }

        \DB::beginTransaction();
        try {
            // Delete matching products
            Product::destroy($products->pluck('id')->all());
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect()->route('mt.products.index')->with('error', 'Error deleting products: ' . $e->getMessage());
        }

        return redirect()->route('mt.products.index')->with('success', "Successfully deleted {$count} products matching keyword '{$keyword}'.");
    }

    public function countByKeyword(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));
        if (strlen($keyword) < 3) {
            return response()->json(['count' => 0]);
        }

        $count = Product::where('name', 'like', "%{$keyword}%")
            ->orWhere('sku', 'like', "%{$keyword}%")
            ->count();

        return response()->json(['count' => $count]);
    }

    public function bulkClearSizes(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer','exists:products,id']
        ]);

        $ids = $data['ids'] ?? [];
        if (count($ids) === 0) {
            return redirect()->route('mt.products.index')->with('error', 'No products selected.');
        }

        \DB::beginTransaction();
        try {
            Product::whereIn('id', $ids)->update([
                'length_in' => null,
                'width_in' => null,
                'height_in' => null,
            ]);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect()->route('mt.products.index')->with('error', 'Error clearing product sizes: ' . $e->getMessage());
        }

        return redirect()->route('mt.products.index')->with('success', 'Cleared size dimensions for selected products.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required','string','max:255'],
            'sku' => ['nullable','string','max:255'],

            'company_id' => ['nullable','integer','exists:companies,id'],
            'category_id' => ['required','integer','exists:categories,id'],
            'sheet_design_id' => ['nullable','integer','exists:sheet_designs,id'],
            'discount_type_id' => ['nullable','integer','exists:discount_types,id'],

            'mrp' => ['nullable','numeric','min:0'],
            'purchase_price_manual' => ['nullable','numeric','min:0'],
            'selling_price_default' => ['nullable','numeric','min:0'],
            'max_discount_percent' => ['nullable','numeric','min:0','max:100'],
            'sale_price_default' => ['nullable','numeric','min:0'],

            'stock_qty' => ['nullable','numeric'],
            'low_stock_alert_qty' => ['nullable','numeric','min:0'],

            // ✅ pricing_mode exists in your DB and cannot be null
            'pricing_mode' => ['required','string','in:manual,shell'],
            'pricing_source' => ['nullable','string','max:50'],

            // Shell fields (only used when pricing_mode = shell)
            'shell_rate' => ['nullable','numeric','min:0'],
            'mrp_markup' => ['nullable','numeric','min:0'],
            'width_in' => ['nullable','numeric','min:0'],
            'length_in' => ['nullable','numeric','min:0'],
            'height_in' => ['nullable','numeric','min:0'],

            // sheet/slab full size (feet) for cutting
            'sheet_full_w' => ['nullable','numeric','min:0'],
            'sheet_full_l' => ['nullable','numeric','min:0'],
            'custom_sizes' => ['nullable','string'],
            'selected_sizes' => ['nullable','array'],
            'selected_sizes.*' => ['string','max:255'],
        ]);
    }

    private function syncActiveShopInventory(Product $product, array $data): void
    {
        $shopId = ShopContext::activeShopId();
        if (!is_numeric($shopId)) {
            return;
        }

        $stockQtyProvided = array_key_exists('stock_qty', $data);
        $lowAlertProvided = array_key_exists('low_stock_alert_qty', $data);
        $sellProvided = array_key_exists('selling_price_default', $data);

        // Always create ShopProduct for the active shop so product shows in POS
        $shopProduct = ShopProduct::firstOrNew([
            'shop_id' => (int) $shopId,
            'product_id' => (int) $product->id,
        ]);

        if (!$shopProduct->exists) {
            $shopProduct->avg_cost = 0;
            // Initialize with default zero stock for new entries
            $shopProduct->stock_qty = 0;
        }

        if ($stockQtyProvided) {
            $shopProduct->stock_qty = round((float) ($data['stock_qty'] ?? 0), 4);
        }

        if ($lowAlertProvided) {
            $shopProduct->low_stock_alert_qty = round((float) ($data['low_stock_alert_qty'] ?? 0), 4);
        } elseif (!$shopProduct->exists) {
            $shopProduct->low_stock_alert_qty = round((float) ($product->low_stock_alert_qty ?? 0), 4);
        }

        if ($sellProvided) {
            $shopProduct->selling_price = $data['selling_price_default'];
        } elseif (!$shopProduct->exists) {
            $shopProduct->selling_price = $product->selling_price_default;
        }

        $shopProduct->save();
    }

    /**
     * Show bulk price form for newly created products.
     * GET /products/bulk-price?ids=1,2,3&name=ProductName
     */
    public function bulkPrice(Request $request)
    {
        $ids = explode(',', trim((string)$request->get('ids', '')));
        $ids = array_map('intval', array_filter($ids));
        $name = (string)$request->get('name', 'Products');

        if (empty($ids)) {
            return redirect()->route('mt.products.index')->with('error', 'No products specified.');
        }

        $products = Product::whereIn('id', $ids)
            ->orderBy('height_in')
            ->orderBy('length_in')
            ->orderBy('width_in')
            ->orderBy('name')
            ->get(['id', 'name', 'company_id', 'category_id', 'length_in', 'width_in', 'height_in', 'mrp', 'purchase_price_manual', 'selling_price_default']);

        if ($products->isEmpty()) {
            return redirect()->route('mt.products.index')->with('error', 'Products not found.');
        }

        return view('mt.products.bulk_price', compact('products', 'name'));
    }

    /**
     * Save bulk prices for multiple products.
     * POST /products/bulk-price-save
     */
    public function bulkPriceSave(Request $request)
    {
        $data = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*.id' => ['required', 'integer', 'exists:products,id'],
            'prices.*.mrp' => ['nullable', 'numeric', 'min:0'],
            'prices.*.purchase_price_manual' => ['nullable', 'numeric', 'min:0'],
            'prices.*.selling_price_default' => ['nullable', 'numeric', 'min:0'],
        ]);

        $updated = 0;
        foreach ($data['prices'] as $priceData) {
            $product = Product::find($priceData['id']);
            if (!$product) {
                continue;
            }

            $updates = [];
            if ($priceData['mrp'] !== null && $priceData['mrp'] !== '') {
                $updates['mrp'] = (float)$priceData['mrp'];
            }
            if ($priceData['purchase_price_manual'] !== null && $priceData['purchase_price_manual'] !== '') {
                $updates['purchase_price_manual'] = (float)$priceData['purchase_price_manual'];
            }
            if ($priceData['selling_price_default'] !== null && $priceData['selling_price_default'] !== '') {
                $updates['selling_price_default'] = (float)$priceData['selling_price_default'];
            }

            if (!empty($updates)) {
                $product->update($updates);
                $updated++;
            }
        }

        return redirect()->route('mt.products.index')
            ->with('success', "Prices updated for $updated product(s).");
    }

    /**
     * Quick inline update for max_discount_percent and stock_qty.
     * PATCH /products/{product}/quick-update
     */
    public function quickUpdate(Request $request, Product $product)
    {
        $field = $request->input('field');
        
        // Validate field first
        if (!in_array($field, ['max_discount_percent', 'stock_qty'])) {
            return response()->json(['error' => 'Invalid field'], 422);
        }

        // For max_discount_percent, value can be null (to inherit from company)
        if ($field === 'max_discount_percent') {
            $data = $request->validate([
                'field' => ['required', 'string', 'in:max_discount_percent,stock_qty'],
                'value' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);
            $value = $data['value'] !== null ? (float)$data['value'] : null;
            $product->update(['max_discount_percent' => $value]);
            return response()->json(['success' => true, 'value' => $value]);
        }
        
        // For stock_qty, value is required and numeric
        if ($field === 'stock_qty') {
            $data = $request->validate([
                'field' => ['required', 'string', 'in:max_discount_percent,stock_qty'],
                'value' => ['required', 'numeric', 'min:0'],
            ]);
            $value = (float)$data['value'];
            $shopId = ShopContext::activeShopId();
            if (is_numeric($shopId)) {
                $shopProduct = ShopProduct::firstOrCreate(
                    ['product_id' => $product->id, 'shop_id' => $shopId],
                    ['stock_qty' => 0, 'avg_cost' => 0]
                );
                $shopProduct->update(['stock_qty' => $value]);
            } else {
                $product->update(['stock_qty' => $value]);
            }
            return response()->json(['success' => true, 'value' => $value]);
        }
    }

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

    private function inferDiscountTypeId(int $companyId, ?int $categoryId = null): ?int
    {
        $query = \App\Models\DiscountRule::where('scope_type', 'company')
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

            $categoryRule = \App\Models\DiscountRule::where('scope_type', 'category')
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
}
