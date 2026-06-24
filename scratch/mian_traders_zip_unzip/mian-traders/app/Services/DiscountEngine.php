<?php

namespace App\Services;

use App\Models\DiscountRule;
use App\Models\Product;

class DiscountEngine
{
    /**
     * Calculate purchase price from Discount Rules.
     * Priority:
     * 1) Product rule
     * 2) Category rule
     * 3) Company rule
     */
    public function purchaseFromRules(Product $product, float $mrp): float
    {
        $discountTypeId = $product->discount_type_id;

        if (!$discountTypeId || $mrp <= 0) {
            return $mrp;
        }

        $rule =
            DiscountRule::where('discount_type_id', $discountTypeId)
                ->where('scope_type', 'product')
                ->where('scope_id', $product->id)
                ->first()

            ?? DiscountRule::where('discount_type_id', $discountTypeId)
                ->where('scope_type', 'category')
                ->where('scope_id', $product->category_id)
                ->first()

            ?? DiscountRule::where('discount_type_id', $discountTypeId)
                ->where('scope_type', 'company')
                ->where('scope_id', $product->company_id)
                ->first();

        if (!$rule) return $mrp;

        // Rule types
        if ($rule->rule_type === 'none') {
            return $mrp;
        }

        if ($rule->rule_type === 'fixed_purchase') {
            return (float)($rule->fixed_purchase_price ?? $mrp);
        }

        if ($rule->rule_type === 'percent_once') {
            $p1 = (float)($rule->percent_1 ?? 0);
            return $mrp - (($mrp * $p1) / 100);
        }

        if ($rule->rule_type === 'percent_twostep') {
            $p1 = (float)($rule->percent_1 ?? 0);
            $p2 = (float)($rule->percent_2 ?? 0);

            $after1 = $mrp - (($mrp * $p1) / 100);
            $after2 = $after1 - (($after1 * $p2) / 100);

            return $after2;
        }

        return $mrp;
    }
}
