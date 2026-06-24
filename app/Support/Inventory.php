<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

class Inventory
{
    public static function getOrCreate(int $shopId, int $productId): ShopProduct
    {
        $product = Product::find($productId);
        $initialStock = ($shopId === 1 && $product) ? (float) ($product->stock_qty ?? 0) : 0;

        return ShopProduct::firstOrCreate(
            ['shop_id' => $shopId, 'product_id' => $productId],
            [
                'stock_qty' => round($initialStock, 4),
                'avg_cost' => 0,
                'selling_price' => $product?->selling_price_default,
                'low_stock_alert_qty' => round((float) ($product?->low_stock_alert_qty ?? 0), 4),
            ]
        );
    }

    /**
     * Increase stock and update weighted avg cost.
     */
    public static function increment(int $shopId, int $productId, float $qty, float $unitCost): void
    {
        if ($qty <= 0) return;

        DB::transaction(function () use ($shopId, $productId, $qty, $unitCost) {
            $sp = ShopProduct::where('shop_id', $shopId)->where('product_id', $productId)->lockForUpdate()->first();
            if (!$sp) {
                $sp = self::getOrCreate($shopId, $productId);
                $sp = ShopProduct::where('id', $sp->id)->lockForUpdate()->first();
            }

            self::bootstrapFromLegacyProduct($sp, $productId);

            $oldQty = (float)$sp->stock_qty;
            $oldCost = (float)$sp->avg_cost;
            $newQty = $oldQty + $qty;

            $newCost = $oldCost;
            if ($newQty > 0) {
                $newCost = (($oldQty * $oldCost) + ($qty * $unitCost)) / $newQty;
            }

            $sp->stock_qty = round($newQty, 4);
            $sp->avg_cost = round($newCost, 4);
            $sp->save();

            self::syncLegacyProductStock($productId, (float) $sp->stock_qty);
        });
    }

    public static function decrement(int $shopId, int $productId, float $qty): void
    {
        if ($qty <= 0) return;

        DB::transaction(function () use ($shopId, $productId, $qty) {
            $sp = ShopProduct::where('shop_id', $shopId)->where('product_id', $productId)->lockForUpdate()->first();
            if (!$sp) {
                $sp = self::getOrCreate($shopId, $productId);
                $sp = ShopProduct::where('id', $sp->id)->lockForUpdate()->first();
            }

            self::bootstrapFromLegacyProduct($sp, $productId);

            $sp->stock_qty = round(((float)$sp->stock_qty) - $qty, 4);
            $sp->save();

            self::syncLegacyProductStock($productId, (float) $sp->stock_qty);
        });
    }

    public static function set(int $shopId, int $productId, float $qty): void
    {
        DB::transaction(function () use ($shopId, $productId, $qty) {
            $sp = ShopProduct::where('shop_id', $shopId)->where('product_id', $productId)->lockForUpdate()->first();
            if (!$sp) {
                $sp = self::getOrCreate($shopId, $productId);
                $sp = ShopProduct::where('id', $sp->id)->lockForUpdate()->first();
            }

            self::bootstrapFromLegacyProduct($sp, $productId);

            $sp->stock_qty = round($qty, 4);
            $sp->save();

            self::syncLegacyProductStock($productId, (float) $sp->stock_qty);
        });
    }

    private static function syncLegacyProductStock(int $productId, float $qty): void
    {
        $totalStock = (float) ShopProduct::where('product_id', $productId)->sum('stock_qty');
        Product::where('id', $productId)->update([
            'stock_qty' => round($totalStock, 4),
        ]);
    }

    private static function bootstrapFromLegacyProduct(ShopProduct $sp, int $productId): void
    {
        if ($sp->shop_id !== 1) {
            return;
        }

        $legacyStock = (float) Product::where('id', $productId)->value('stock_qty');

        if ((float) $sp->stock_qty > 0 || $legacyStock <= 0) {
            return;
        }

        $sp->stock_qty = round($legacyStock, 4);
        $sp->save();
    }
}
