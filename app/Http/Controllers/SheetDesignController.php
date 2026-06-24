<?php

namespace App\Http\Controllers;

use App\Models\SheetDesign;
use Illuminate\Http\Request;

class SheetDesignController extends Controller
{
    /**
     * Display a listing of sheet designs.
     */
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $sheetDesigns = SheetDesign::query()
            ->withCount('products')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('finish', 'like', "%{$q}%")
                      ->orWhere('color_group', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(30);

        return view('mt.sheet_designs.index', compact('sheetDesigns', 'q'));
    }

    /**
     * Show the form for bulk creating sheet designs and products.
     */
    public function bulkForm()
    {
        $companies = \App\Models\Company::where('group_key', 'hardware')->orderBy('name')->get();
        $categories = \App\Models\Category::where('group_key', 'hardware')->orderBy('name')->get();
        return view('mt.sheet_designs.bulk', compact('companies', 'categories'));
    }

    /**
     * Process bulk sheet designs CSV/pasted data and generate products.
     */
    public function bulkProcess(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sizes' => ['required', 'array'],
            'sizes.*' => ['required', 'string'],
            'custom_sizes' => ['nullable', 'string'],
            'csv_file' => ['nullable', 'file', 'mimes:csv,txt'],
            'pasted_data' => ['nullable', 'string'],
        ]);

        $csvContent = '';
        if ($request->hasFile('csv_file')) {
            $csvContent = file_get_contents($request->file('csv_file')->getRealPath());
        } elseif ($request->filled('pasted_data')) {
            $csvContent = $request->input('pasted_data');
        }

        if (trim($csvContent) === '') {
            return redirect()->back()->withErrors(['csv_file' => 'Please upload a CSV file or paste data in the textarea.']);
        }

        // Target Sizes
        $targetSizes = [];
        $rawSizes = array_merge(
            $request->input('sizes', []),
            array_filter(array_map('trim', explode(',', $request->input('custom_sizes', ''))))
        );

        foreach ($rawSizes as $rawSize) {
            $rawSize = trim($rawSize);
            if (preg_match('/^(\d+(?:\.\d+)?)\s*[\*xX×]\s*(\d+(?:\.\d+)?)$/', $rawSize, $matches)) {
                $part1 = (float)$matches[1];
                $part2 = (float)$matches[2];
                
                // Smart heuristic: if both dimensions are <= 12, treat as feet. Otherwise treat as inches.
                if ($part1 <= 12 && $part2 <= 12) {
                    $lengthIn = $part1 * 12;
                    $widthIn = $part2 * 12;
                } else {
                    $lengthIn = $part1;
                    $widthIn = $part2;
                }
                
                $targetSizes[] = [
                    'length_in' => $lengthIn,
                    'width_in' => $widthIn,
                    'label' => $rawSize,
                ];
            }
        }

        if (empty($targetSizes)) {
            return redirect()->back()->withErrors(['sizes' => 'At least one valid size (e.g. 8x4) must be selected or specified.']);
        }

        // Parse CSV/pasted lines
        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
        if (empty($lines)) {
            return redirect()->back()->withErrors(['pasted_data' => 'No data found to process.']);
        }

        $headerLine = array_shift($lines);
        $separator = str_contains($headerLine, "\t") ? "\t" : ",";
        $headers = array_map('trim', str_getcsv($headerLine, $separator));

        $headerMap = [];
        foreach ($headers as $index => $colName) {
            $headerMap[strtolower(trim($colName))] = $index;
        }

        // Map header columns to active hardware companies
        $activeCompanies = \App\Models\Company::where('group_key', 'hardware')->get();
        $companyColumns = [];
        foreach ($activeCompanies as $company) {
            $compNameLower = strtolower(trim($company->name));
            $shortCompNameLower = str_ireplace([' lamination', ' board', ' sheets', ' sheet'], '', $compNameLower);
            
            foreach ($headers as $index => $colName) {
                $colNameClean = strtolower(trim($colName));
                $shortColNameClean = str_ireplace([' lamination', ' board', ' sheets', ' sheet'], '', $colNameClean);
                
                if ($colNameClean === $compNameLower || $shortColNameClean === $shortCompNameLower) {
                    $companyColumns[$company->id] = $index;
                    break;
                }
            }
        }

        $createdDesignsCount = 0;
        $createdProductsCount = 0;

        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $row = str_getcsv($line, $separator);

            $designIdx = $headerMap['design_name'] ?? $headerMap['name'] ?? null;
            if ($designIdx === null || !isset($row[$designIdx])) continue;
            $designName = trim($row[$designIdx]);
            if ($designName === '') continue;

            $finishIdx = $headerMap['finish'] ?? null;
            $finish = ($finishIdx !== null && isset($row[$finishIdx]) && trim($row[$finishIdx]) !== '') ? trim($row[$finishIdx]) : null;

            $groupIdx = $headerMap['color_group'] ?? $headerMap['group'] ?? null;
            $colorGroup = ($groupIdx !== null && isset($row[$groupIdx]) && trim($row[$groupIdx]) !== '') ? trim($row[$groupIdx]) : null;

            // Find or create SheetDesign
            $sheetDesign = \App\Models\SheetDesign::where('name', $designName)
                ->where('finish', $finish)
                ->where('color_group', $colorGroup)
                ->first();

            if (!$sheetDesign) {
                $sheetDesign = \App\Models\SheetDesign::create([
                    'name' => $designName,
                    'finish' => $finish,
                    'color_group' => $colorGroup,
                ]);
                $createdDesignsCount++;
            }

            // Gather mapped codes
            $codes = $sheetDesign->sheet_codes ?: [];
            foreach ($companyColumns as $companyId => $colIdx) {
                if (isset($row[$colIdx])) {
                    $code = trim($row[$colIdx]);
                    if ($code !== '') {
                        $codes[$companyId] = $code;
                    } else {
                        unset($codes[$companyId]);
                    }
                }
            }
            $sheetDesign->sheet_codes = $codes;
            $sheetDesign->save();

            // Create products for each mapped company
            foreach ($codes as $companyId => $code) {
                $company = \App\Models\Company::find($companyId);
                if (!$company) continue;

                $cleanCompName = trim(str_ireplace(['lamination', 'board', 'sheets', 'sheet'], '', $company->name));
                $companyPrefix = strtoupper(preg_replace('/[^a-zA-Z0-9-]/', '', $cleanCompName));
                if ($companyPrefix === '') $companyPrefix = 'COMP';

                $defaultMrp = (float)($company->default_lamination_rate ?? 0);

                foreach ($targetSizes as $sizeMeta) {
                    $lengthIn = $sizeMeta['length_in'];
                    $widthIn = $sizeMeta['width_in'];
                    $sizeLabel = $sizeMeta['label'];

                    $existingProduct = \App\Models\Product::where('company_id', $companyId)
                        ->where('category_id', $request->category_id)
                        ->where('sheet_design_id', $sheetDesign->id)
                        ->where('length_in', $lengthIn)
                        ->where('width_in', $widthIn)
                        ->first();

                    if (!$existingProduct) {
                        $finishPart = $finish !== null && $finish !== '' ? " ($finish)" : '';
                        $productName = "{$company->name} {$designName}{$finishPart} {$sizeLabel}";
                        
                        $sku = "{$companyPrefix}-{$code}-{$sizeLabel}";
                        $baseSku = $sku;
                        $counter = 1;
                        while (\App\Models\Product::where('sku', $sku)->exists()) {
                            $sku = "{$baseSku}-{$counter}";
                            $counter++;
                        }

                        $product = \App\Models\Product::create([
                            'name' => $productName,
                            'sku' => $sku,
                            'company_id' => $companyId,
                            'category_id' => $request->category_id,
                            'sheet_design_id' => $sheetDesign->id,
                            'length_in' => $lengthIn,
                            'width_in' => $widthIn,
                            'mrp' => $defaultMrp,
                            'pricing_mode' => 'manual',
                            'is_active' => true,
                            'stock_qty' => 0,
                        ]);

                        // Initialize shop product inventory
                        $shopId = \App\Support\ShopContext::activeShopId();
                        if ($shopId) {
                            \App\Models\ShopProduct::firstOrCreate([
                                'shop_id' => $shopId,
                                'product_id' => $product->id,
                            ], [
                                'stock_qty' => 0,
                                'low_stock_alert_qty' => 0,
                                'selling_price' => $product->mrp,
                                'avg_cost' => 0,
                            ]);
                        }

                        $createdProductsCount++;
                    }
                }
            }
        }

        return redirect()->route('mt.sheet_designs.index')
            ->with('success', "Bulk creation complete. Added {$createdDesignsCount} new designs and generated {$createdProductsCount} new products across companies.");
    }

    /**
     * Show the form for creating a new sheet design.
     */
     public function create()
     {
         $sheetDesign = new SheetDesign();
         $companies = \App\Models\Company::where('group_key', 'hardware')->orderBy('name')->get();
         return view('mt.sheet_designs.create', compact('sheetDesign', 'companies'));
     }
 
     /**
      * Store a newly created sheet design in storage.
      */
     public function store(Request $request)
     {
         $data = $request->validate([
             'name' => ['required', 'string', 'max:255'],
             'finish' => ['nullable', 'string', 'max:255'],
             'color_group' => ['nullable', 'string', 'max:255'],
             'sheet_codes' => ['nullable', 'array'],
         ]);
 
         if (isset($data['sheet_codes']) && is_array($data['sheet_codes'])) {
             $data['sheet_codes'] = array_filter(
                 array_map('trim', $data['sheet_codes']),
                 fn($val) => $val !== ''
             );
         }
 
         SheetDesign::create($data);
 
         return redirect()->route('mt.sheet_designs.index')
             ->with('success', 'Sheet design created successfully.');
     }
 
     /**
      * Show the form for editing the specified sheet design.
      */
     public function edit(SheetDesign $sheetDesign)
     {
         $companies = \App\Models\Company::where('group_key', 'hardware')->orderBy('name')->get();
         return view('mt.sheet_designs.edit', compact('sheetDesign', 'companies'));
     }
 
     /**
      * Update the specified sheet design in storage.
      */
     public function update(Request $request, SheetDesign $sheetDesign)
     {
         $data = $request->validate([
             'name' => ['required', 'string', 'max:255'],
             'finish' => ['nullable', 'string', 'max:255'],
             'color_group' => ['nullable', 'string', 'max:255'],
             'sheet_codes' => ['nullable', 'array'],
         ]);
 
         if (isset($data['sheet_codes']) && is_array($data['sheet_codes'])) {
             $data['sheet_codes'] = array_filter(
                 array_map('trim', $data['sheet_codes']),
                 fn($val) => $val !== ''
             );
         }
 
         $sheetDesign->update($data);
 
         return redirect()->route('mt.sheet_designs.index')
             ->with('success', 'Sheet design updated successfully.');
     }

    /**
     * Remove the specified sheet design from storage.
     */
    public function destroy(SheetDesign $sheetDesign)
    {
        $sheetDesign->delete();

        return redirect()->route('mt.sheet_designs.index')
            ->with('success', 'Sheet design deleted successfully.');
    }
}
