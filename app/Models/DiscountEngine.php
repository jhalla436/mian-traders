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
        $pricingSource = strtolower((string)($product->pricing_source ?? ''));

        if (!empty($product->purchase_price_manual) && (float)$product->purchase_price_manual > 0 && ($pricingSource === '' || $pricingSource === 'manual')) {
            return (float)$product->purchase_price_manual;
        }

        $typeId = (int)($product->discount_type_id ?? 0);
        if ($typeId <= 0) {
            return !empty($product->purchase_price_manual) && (float)$product->purchase_price_manual > 0
                ? (float)$product->purchase_price_manual
                : $mrp;
        }

        $categoryIds = [];
        if ($product->category_id) {
            $catId = (int)$product->category_id;
            while ($catId > 0) {
                $categoryIds[] = $catId;
                $category = Category::find($catId);
                $catId = $category && $category->parent_id ? (int)$category->parent_id : 0;
            }
        }

        $rule = DiscountRule::query()
            ->where('discount_type_id', $typeId)
            ->where(function ($q) use ($product, $categoryIds) {
                $q->where(function ($qq) use ($product) {
                    $qq->where('scope_type', 'product')->where('scope_id', $product->id);
                })
                ->orWhere(function ($qq) use ($categoryIds) {
                    if (!empty($categoryIds)) {
                        $qq->where('scope_type', 'category')->whereIn('scope_id', $categoryIds);
                    }
                })
                ->orWhere(function ($qq) use ($product) {
                    if ($product->company_id) {
                        $qq->where('scope_type', 'company')->where('scope_id', $product->company_id);
                    }
                });
            })
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $query->orderByRaw("CASE scope_type WHEN 'product' THEN 1 WHEN 'category' THEN 2 WHEN 'company' THEN 3 ELSE 4 END");
            if (!empty($categoryIds)) {
                $cases = [];
                foreach ($categoryIds as $index => $id) {
                    $cases[] = "WHEN scope_id = " . (int)$id . " THEN " . ($index + 1);
                }
                $query->orderByRaw("CASE " . implode(' ', $cases) . " ELSE " . (count($categoryIds) + 1) . " END");
            } else {
                $query->orderBy('id');
            }
        } else {
            $query->orderByRaw("FIELD(scope_type,'product','category','company')")
                ->orderByRaw(!empty($categoryIds) ? "FIELD(scope_id," . implode(',', array_map('intval', $categoryIds)) . ")" : "id");
        }

        $rule = $query->first();

        if (!$rule) {
            return !empty($product->purchase_price_manual) && (float)$product->purchase_price_manual > 0
                ? (float)$product->purchase_price_manual
                : $mrp;
        }

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
