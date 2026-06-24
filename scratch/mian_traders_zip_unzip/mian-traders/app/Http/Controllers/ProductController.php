<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\DiscountType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $products = Product::query()
            ->with(['company','category'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%")
                       ->orWhere('id', $q);
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('mt.products.index', compact('products', 'q'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $discountTypes = DiscountType::orderBy('name')->get();

        $product = new Product();
        // ✅ default so pricing_mode never null
        $product->pricing_mode = 'manual';

        return view('mt.products.create', compact('product', 'categories', 'companies', 'discountTypes'));
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

        Product::create($data);

        return redirect()->route('mt.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $discountTypes = DiscountType::orderBy('name')->get();

        // ✅ ensure not null
        if (empty($product->pricing_mode)) {
            $product->pricing_mode = 'manual';
        }

        return view('mt.products.edit', compact('product', 'categories', 'companies', 'discountTypes'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

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

        $product->update($data);

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
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'mode' => ['nullable', 'string', 'in:upsert,create_only'],
            'default_company_id' => ['nullable', 'integer'],
            'default_category_id' => ['nullable', 'integer'],
            'default_discount_type_id' => ['nullable', 'integer'],
            'default_is_active' => ['nullable', 'in:1,0'],
        ]);

        $mode = (string)($data['mode'] ?? 'upsert');
        $defaultCompanyId = $data['default_company_id'] ?? null;
        $defaultCategoryId = $data['default_category_id'] ?? null;
        $defaultDiscountTypeId = $data['default_discount_type_id'] ?? null;
        $defaultIsActive = isset($data['default_is_active']) ? (int)$data['default_is_active'] : 1;

        $file = $request->file('file');
        $path = $file->getRealPath();

        $fh = fopen($path, 'r');
        if (!$fh) {
            return back()->with('error', 'Unable to read file.');
        }

        $header = fgetcsv($fh);
        if (!$header) {
            fclose($fh);
            return back()->with('error', 'CSV header row missing.');
        }

        $headerMap = [];
        foreach ($header as $i => $h) {
            $key = strtolower(trim((string)$h));
            $headerMap[$key] = $i;
        }

        $get = function(array $row, string $key) use ($headerMap) {
            $k = strtolower(trim($key));
            if (!isset($headerMap[$k])) return null;
            $idx = $headerMap[$k];
            return isset($row[$idx]) ? trim((string)$row[$idx]) : null;
        };

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        while (($row = fgetcsv($fh)) !== false) {
            // skip empty rows
            $name = $get($row, 'name');
            if ($name === null || $name === '') {
                continue;
            }

            $sku = $get($row, 'sku') ?: null;

            // company/category (optional columns)
            $companyName = $get($row, 'company') ?: $get($row, 'company_name');
            $categoryName = $get($row, 'category') ?: $get($row, 'category_name');

            $companyId = $defaultCompanyId;
            if ($companyName) {
                $co = Company::where('name', $companyName)->first();
                if ($co) $companyId = $co->id;
            }

            $categoryId = $defaultCategoryId;
            if ($categoryName) {
                $cat = Category::where('name', $categoryName)->first();
                if ($cat) $categoryId = $cat->id;
            }

            $mrp = (float)($get($row, 'mrp') ?: 0);
            $sell = $get($row, 'selling_price_default');
            $sell = ($sell !== null && $sell !== '') ? (float)$sell : null;

            $purchaseManual = $get($row, 'purchase_price_manual');
            $purchaseManual = ($purchaseManual !== null && $purchaseManual !== '') ? (float)$purchaseManual : null;

            $stockQty = $get($row, 'stock_qty');
            $stockQty = ($stockQty !== null && $stockQty !== '') ? (float)$stockQty : 0;

            $lowAlert = $get($row, 'low_stock_alert_qty');
            $lowAlert = ($lowAlert !== null && $lowAlert !== '') ? (float)$lowAlert : null;

            $pricingMode = strtolower((string)($get($row, 'pricing_mode') ?: 'manual'));
            if (!in_array($pricingMode, ['manual','shell'], true)) $pricingMode = 'manual';

            $shellRate = $get($row, 'shell_rate');
            $shellRate = ($shellRate !== null && $shellRate !== '') ? (float)$shellRate : null;

            $widthIn = $get($row, 'width_in');
            $widthIn = ($widthIn !== null && $widthIn !== '') ? (float)$widthIn : null;

            $lengthIn = $get($row, 'length_in');
            $lengthIn = ($lengthIn !== null && $lengthIn !== '') ? (float)$lengthIn : null;

            $heightIn = $get($row, 'height_in');
            $heightIn = ($heightIn !== null && $heightIn !== '') ? (float)$heightIn : null;

            $sheetW = $get($row, 'sheet_full_w');
            $sheetW = ($sheetW !== null && $sheetW !== '') ? (float)$sheetW : null;

            $sheetL = $get($row, 'sheet_full_l');
            $sheetL = ($sheetL !== null && $sheetL !== '') ? (float)$sheetL : null;

            try {
                $query = Product::query();

                if ($sku) {
                    $query->where('sku', $sku);
                } else {
                    $query->where('name', $name);
                }

                if ($companyId) {
                    $query->where('company_id', $companyId);
                }

                $existing = $query->first();

                $payload = [
                    'name' => $name,
                    'sku' => $sku,
                    'company_id' => $companyId,
                    'category_id' => $categoryId,
                    'discount_type_id' => $defaultDiscountTypeId,
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
                    $existing->update($payload);
                    $updated++;
                } else {
                    // ensure required fields
                    if (!isset($payload['pricing_mode'])) $payload['pricing_mode'] = 'manual';
                    Product::create($payload);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors++;
            }
        }

        fclose($fh);

        return redirect()->route('mt.products.index')->with('success', "Import complete: Created {$created}, Updated {$updated}, Skipped {$skipped}, Errors {$errors}.");
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('mt.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required','string','max:255'],
            'sku' => ['nullable','string','max:255'],

            'company_id' => ['nullable','integer','exists:companies,id'],
            'category_id' => ['required','integer','exists:categories,id'],
            'discount_type_id' => ['nullable','integer','exists:discount_types,id'],

            'mrp' => ['nullable','numeric','min:0'],
            'purchase_price_manual' => ['nullable','numeric','min:0'],
            'selling_price_default' => ['nullable','numeric','min:0'],
            'sale_price_default' => ['nullable','numeric','min:0'],

            'stock_qty' => ['nullable','numeric'],
            'low_stock_alert_qty' => ['nullable','numeric','min:0'],

            // ✅ pricing_mode exists in your DB and cannot be null
            'pricing_mode' => ['required','string','in:manual,shell'],
            'pricing_source' => ['nullable','string','max:50'],

            // Shell fields (only used when pricing_mode = shell)
            'shell_rate' => ['nullable','numeric','min:0'],
            'width_in' => ['nullable','numeric','min:0'],
            'length_in' => ['nullable','numeric','min:0'],
            'height_in' => ['nullable','numeric','min:0'],

            // sheet/slab full size (feet) for cutting
            'sheet_full_w' => ['nullable','numeric','min:0'],
            'sheet_full_l' => ['nullable','numeric','min:0'],
        ]);
    }
}