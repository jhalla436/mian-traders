<?php

namespace App\Models;

class DiscountEngine
{
    /**
     * Calculates purchase price from DiscountRules.
     * Priority (most specific wins):
     * 1) Product rule
     * 2) Category rule
     * 3) Company rule
     */
    public function purchaseFromRules(Product $product, float $mrp): float
    {
        $typeId = (int)($product->discount_type_id ?? 0);
        if ($typeId <= 0) return $mrp;

        $rule = DiscountRule::query()
            ->where('discount_type_id', $typeId)
            ->where(function ($q) use ($product) {
                $q->where(function ($qq) use ($product) {
                    $qq->where('scope_type', 'product')->where('scope_id', $product->id);
                })
                ->orWhere(function ($qq) use ($product) {
                    if ($product->category_id) {
                        $qq->where('scope_type', 'category')->where('scope_id', $product->category_id);
                    }
                })
                ->orWhere(function ($qq) use ($product) {
                    if ($product->company_id) {
                        $qq->where('scope_type', 'company')->where('scope_id', $product->company_id);
                    }
                });
            })
            ->orderByRaw("FIELD(scope_type,'product','category','company')")
            ->first();

        if (!$rule) return $mrp;

        // rule_type handling
        if ($rule->rule_type === 'fixed_purchase') {
            return (float)($rule->fixed_purchase_price ?? $mrp);
        }

        if ($rule->rule_type === 'percent_twostep') {
            $p1 = (float)($rule->percent_1 ?? 0);
            $p2 = (float)($rule->percent_2 ?? 0);
            $after1 = $mrp - ($mrp * $p1 / 100);
            $after2 = $after1 - ($after1 * $p2 / 100);
            return (float)$after2;
        }

        if ($rule->rule_type === 'percent_once') {
            $p1 = (float)($rule->percent_1 ?? 0);
            return (float)($mrp - ($mrp * $p1 / 100));
        }

        // none
        return $mrp;
    }
}
