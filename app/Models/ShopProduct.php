<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProduct extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($shopProduct) {
            \Illuminate\Support\Facades\Cache::flush();
            $totalStock = (float) \App\Models\ShopProduct::where('product_id', $shopProduct->product_id)->sum('stock_qty');
            \App\Models\Product::where('id', $shopProduct->product_id)->update([
                'stock_qty' => round($totalStock, 4),
            ]);
        });
        static::deleted(function ($shopProduct) {
            \Illuminate\Support\Facades\Cache::flush();
            $totalStock = (float) \App\Models\ShopProduct::where('product_id', $shopProduct->product_id)->sum('stock_qty');
            \App\Models\Product::where('id', $shopProduct->product_id)->update([
                'stock_qty' => round($totalStock, 4),
            ]);
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
