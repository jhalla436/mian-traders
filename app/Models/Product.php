<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected static $isSyncingShellRate = false;
    protected static $companyNamesCache = [];
    protected static $categoryNamesCache = [];

    protected $fillable = [
        'pricing_mode',
        'pricing_source',
        'shell_rate',
        'mrp_markup',
        'width_in',
        'length_in',
        'height_in',
        'sheet_full_w',
        'sheet_full_l',

        'company_id',
        'category_id',
        'sheet_design_id',
        'discount_type_id',

        'name',
        'sku',
        'mrp',
        'purchase_price_manual',
        'selling_price_default',
        'max_discount_percent',
        'sale_price_default',

        'is_active',
        'stock_qty',
        'low_stock_alert_qty',

        'is_variant_parent',
        'is_split_parent',
        'split_total_parts',

        'image_path',
    ];

    public function effectiveMaxDiscountPercent(): float
    {
        if ($this->max_discount_percent !== null) {
            return (float)$this->max_discount_percent;
        }
        return (float)($this->company?->max_discount_percent ?? 0);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shopProducts()
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function sheetDesign()
    {
        return $this->belongsTo(SheetDesign::class);
    }

    public function isJumbolonUncovered(): bool
    {
        $catName = '';
        if ($this->category_id) {
            if (!isset(static::$categoryNamesCache[$this->category_id])) {
                $category = $this->category ?: Category::find($this->category_id);
                static::$categoryNamesCache[$this->category_id] = $category ? strtolower($category->name ?? '') : '';
            }
            $catName = static::$categoryNamesCache[$this->category_id];
        }

        if (str_contains($catName, 'jumbolon') && str_contains($catName, 'uncovered')) {
            return true;
        }

        $compName = '';
        if ($this->company_id) {
            if (!isset(static::$companyNamesCache[$this->company_id])) {
                $company = $this->company ?: Company::find($this->company_id);
                static::$companyNamesCache[$this->company_id] = $company ? strtolower($company->name ?? '') : '';
            }
            $compName = static::$companyNamesCache[$this->company_id];
        }

        if (str_contains($compName, 'jumbolon') && (str_contains($compName, 'uncovered') || str_contains($compName, 'uncoverd'))) {
            return true;
        }

        return false;
    }

    public function deriveFamilyName(): string
    {
        $trimmed = trim((string) $this->name);
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

    protected static function booted()
    {
        static::saving(function (Product $product) {
            \Illuminate\Support\Facades\Cache::flush();
            if ($product->isJumbolonUncovered()) {
                $product->pricing_mode = 'shell';

                // Automatically calculate MRP based on mrp_markup
                // If mrp_markup is set, use it. Otherwise, default to 70.00 if it is a 'Hard' family.
                $markup = null;
                if (!is_null($product->mrp_markup) && $product->mrp_markup !== '') {
                    $markup = (float)$product->mrp_markup;
                } else {
                    $familyName = strtolower($product->deriveFamilyName());
                    if ($familyName === 'hard' || str_starts_with($familyName, 'hard ')) {
                        $markup = 70.00;
                        $product->mrp_markup = 70.00; // Save the default value to DB so it shows in forms
                    }
                }

                if ($markup !== null) {
                    $w = (float)($product->width_in ?? 0);
                    $l = (float)($product->length_in ?? 0);
                    $h = (float)($product->height_in ?? 0);
                    $rate = (float)($product->shell_rate ?? 0);

                    if ($w > 0 && $l > 0 && $h > 0 && $rate > 0) {
                        $cft = ($w * $l * $h) / 144.0;
                        $purchasePrice = round($cft * $rate, 2);
                        $product->mrp = $purchasePrice + $markup;
                    }
                }
            }
        });

        static::saved(function (Product $product) {
            \Illuminate\Support\Facades\Cache::flush();
            if (static::$isSyncingShellRate) {
                return;
            }

            if ($product->isJumbolonUncovered()) {
                if ($product->wasChanged('shell_rate') || $product->wasChanged('mrp_markup') || $product->wasRecentlyCreated) {
                    $familyName = $product->deriveFamilyName();
                    if ($familyName !== '') {
                        static::$isSyncingShellRate = true;
                        try {
                            $siblings = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->get();

                            foreach ($siblings as $sibling) {
                                if ($sibling->deriveFamilyName() === $familyName) {
                                    $siblingChanged = false;
                                    if ((float)$sibling->shell_rate !== (float)$product->shell_rate) {
                                        $sibling->shell_rate = $product->shell_rate;
                                        $siblingChanged = true;
                                    }
                                    if ($sibling->mrp_markup !== $product->mrp_markup) {
                                        $sibling->mrp_markup = $product->mrp_markup;
                                        $siblingChanged = true;
                                    }
                                    if ($sibling->pricing_mode !== 'shell') {
                                        $sibling->pricing_mode = 'shell';
                                        $siblingChanged = true;
                                    }
                                    if ($siblingChanged) {
                                        $sibling->save();
                                    }
                                }
                            }
                        } finally {
                            static::$isSyncingShellRate = false;
                        }
                    }
                }
            }
        });

        static::deleted(function (Product $product) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }
}
