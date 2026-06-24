<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CategorySize extends Model
{
    protected static $companyNamesCache = [];
    protected static $categoryNamesCache = [];
    protected $fillable = [
        'category_id',
        'length_in',
        'width_in',
        'height_in',
        'size_type',
    ];

    protected $casts = [
        'length_in' => 'float',
        'width_in' => 'float',
        'height_in' => 'float',
    ];

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if ($key === 'size_type' && (is_null($value) || $value === '')) {
            return 'standard';
        }
        return $value;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get display name for the size (e.g., "72×36×6")
     */
    public function getDisplayName(): string
    {
        // Format numbers without unnecessary decimal places
        $fmt = function ($v) {
            if (is_null($v)) return '';
            // If value is whole number, show as integer
            if (floor($v) == $v) {
                return (int)$v;
            }
            // Otherwise trim trailing zeros
            return rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.');
        };

        $l = $fmt($this->length_in);
        $w = $fmt($this->width_in);
        $h = $fmt($this->height_in);

        // Use lowercase 'x' separators and append a double-quote to match requested pattern
        return "{$l}x{$w}x{$h}\"";
    }

    protected static function booted()
    {
        static::creating(function (CategorySize $size) {
            // If display_order not provided, set it to max(display_order)+1 for the category
            if (empty($size->display_order)) {
                $max = DB::table('category_sizes')
                    ->where('category_id', $size->category_id)
                    ->max('display_order');
                $size->display_order = ($max ? (int)$max : 0) + 1;
            }
        });

        static::created(function (CategorySize $size) {
            $category = $size->category ?: ($size->category_id ? Category::find($size->category_id) : null);
            if ($category) {
                if (!isset(static::$categoryNamesCache[$category->id])) {
                    static::$categoryNamesCache[$category->id] = strtolower($category->name ?? '');
                }
                $catName = static::$categoryNamesCache[$category->id];

                $compName = '';
                if ($category->company_id) {
                    if (!isset(static::$companyNamesCache[$category->company_id])) {
                        $company = Company::find($category->company_id);
                        static::$companyNamesCache[$category->company_id] = $company ? strtolower($company->name ?? '') : '';
                    }
                    $compName = static::$companyNamesCache[$category->company_id];
                }
                
                $isJumbolonUncovered = (str_contains($catName, 'jumbolon') && str_contains($catName, 'uncovered'))
                    || (str_contains($compName, 'jumbolon') && (str_contains($compName, 'uncovered') || str_contains($compName, 'uncoverd')));

                if ($isJumbolonUncovered) {
                    $existingProducts = Product::where('category_id', $category->id)->get();
                    
                    $families = [];
                    foreach ($existingProducts as $p) {
                        $famName = $p->deriveFamilyName();
                        if ($famName !== '' && !isset($families[$famName])) {
                            $families[$famName] = [
                                'company_id' => $p->company_id,
                                'shell_rate' => $p->shell_rate,
                                'mrp_markup' => $p->mrp_markup,
                                'sheet_design_id' => $p->sheet_design_id,
                                'discount_type_id' => $p->discount_type_id,
                                'is_active' => $p->is_active,
                            ];
                        }
                    }

                    foreach ($families as $familyName => $meta) {
                        $sizeDisplayName = $size->getDisplayName();
                        $productName = $familyName . ' ' . $sizeDisplayName;

                        $exists = Product::where('category_id', $category->id)
                            ->where('name', $productName)
                            ->exists();

                        if (!$exists) {
                            $formatSkuDimension = function ($value): string {
                                return rtrim(rtrim(number_format((float)$value, 3, '.', ''), '0'), '.');
                            };
                            $dimParts = [];
                            if ($size->width_in) $dimParts[] = $formatSkuDimension($size->width_in);
                            if ($size->length_in) $dimParts[] = $formatSkuDimension($size->length_in);
                            if ($size->height_in) $dimParts[] = $formatSkuDimension($size->height_in);
                            
                            $baseSku = strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $productName), 0, 6));
                            $sku = $baseSku . '-' . implode('x', $dimParts);

                            $origSku = $sku;
                            $counter = 1;
                            while (Product::where('sku', $sku)->exists()) {
                                $sku = $origSku . '-' . $counter;
                                $counter++;
                            }

                            $newProduct = Product::create([
                                'name' => $productName,
                                'sku' => $sku,
                                'category_id' => $category->id,
                                'company_id' => $meta['company_id'],
                                'pricing_mode' => 'shell',
                                'shell_rate' => $meta['shell_rate'],
                                'mrp_markup' => $meta['mrp_markup'],
                                'width_in' => $size->width_in,
                                'length_in' => $size->length_in,
                                'height_in' => $size->height_in,
                                'sheet_design_id' => $meta['sheet_design_id'],
                                'discount_type_id' => $meta['discount_type_id'],
                                'is_active' => $meta['is_active'],
                                'stock_qty' => 0,
                                'low_stock_alert_qty' => 0,
                            ]);

                            $shops = Shop::all();
                            foreach ($shops as $shop) {
                                ShopProduct::create([
                                    'shop_id' => $shop->id,
                                    'product_id' => $newProduct->id,
                                    'stock_qty' => 0,
                                    'low_stock_alert_qty' => 0,
                                    'selling_price' => null,
                                    'avg_cost' => 0,
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }
}
