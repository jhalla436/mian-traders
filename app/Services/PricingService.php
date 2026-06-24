<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DiscountRule;

class PricingService
{
    public static function defaultSellPrice(Product $p): float
    {
        $sell = $p->selling_price_default ?? $p->sale_price_default ?? $p->mrp ?? 0;
        if ((float)$sell > 0) {
            return round((float)$sell, 2);
        }

        // 2) Jumbolon uncovered shell formula (pricing_mode = shell)
        if (($p->pricing_mode ?? '') === 'shell') {
            $w = (float)($p->width_in ?? 0);
            $l = (float)($p->length_in ?? 0);
            $h = (float)($p->height_in ?? 0);
            $rate = (float)($p->shell_rate ?? 0);

            if ($w > 0 && $l > 0 && $h > 0 && $rate > 0) {
                $cft = ($w * $l * $h) / 144.0;
                return round($cft * $rate, 2);
            }
        }

        return round((float)$sell, 2);
    }

    public static function purchasePrice(Product $p): float
    {
        $pricingSource = strtolower((string)($p->pricing_source ?? ''));

        // 1) Manual purchase
        if (!empty($p->purchase_price_manual) && (float)$p->purchase_price_manual > 0 && ($pricingSource === '' || $pricingSource === 'manual')) {
            return round((float)$p->purchase_price_manual, 2);
        }

        // 2) Jumbolon uncovered shell formula (pricing_mode = shell)
        // Formula: length*width*height / 144 * shell_rate
        if (($p->pricing_mode ?? '') === 'shell') {
            $w = (float)($p->width_in ?? 0);
            $l = (float)($p->length_in ?? 0);
            $h = (float)($p->height_in ?? 0);
            $rate = (float)($p->shell_rate ?? 0);

            if ($w > 0 && $l > 0 && $h > 0 && $rate > 0) {
                $cft = ($w * $l * $h) / 144.0;
                return round($cft * $rate, 2);
            }
        }

        // 3) Discount Rules engine (product > category > company)
        $mrp = (float)($p->mrp ?? 0);
        if ($mrp <= 0) return 0;

        $discountTypeId = (int)($p->discount_type_id ?? 0);
        if ($discountTypeId <= 0) return 0;

        $rule =
            DiscountRule::where('discount_type_id', $discountTypeId)
                ->where('scope_type', 'product')
                ->where('scope_id', $p->id)
                ->first();

        if (!$rule && !empty($p->category_id)) {
            $catId = (int)$p->category_id;
            while ($catId > 0) {
                $rule = DiscountRule::where('discount_type_id', $discountTypeId)
                    ->where('scope_type', 'category')
                    ->where('scope_id', $catId)
                    ->first();
                if ($rule) {
                    break;
                }
                // Move to parent category
                $category = \App\Models\Category::find($catId);
                $catId = $category && $category->parent_id ? (int)$category->parent_id : 0;
            }
        }

        if (!$rule && !empty($p->company_id)) {
            $rule =
                DiscountRule::where('discount_type_id', $discountTypeId)
                    ->where('scope_type', 'company')
                    ->where('scope_id', $p->company_id)
                    ->first();
        }

        if (!$rule) {
            if (!empty($p->purchase_price_manual) && (float)$p->purchase_price_manual > 0) {
                return round((float)$p->purchase_price_manual, 2);
            }

            return 0;
        }

        // Fixed purchase price override
        if (!empty($rule->fixed_purchase_price) && (float)$rule->fixed_purchase_price > 0) {
            return round((float)$rule->fixed_purchase_price, 2);
        }

        // Percent discounts (sequential)
        $p1 = (float)($rule->percent_1 ?? 0);
        $p2 = (float)($rule->percent_2 ?? 0);

        $price = $mrp;

        if ($p1 > 0) $price = $price * (1 - ($p1 / 100));
        if ($p2 > 0) $price = $price * (1 - ($p2 / 100));

        return round($price, 2);
    }
}
