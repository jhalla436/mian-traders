<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'pricing_mode',
        'pricing_source',
        'shell_rate',
        'width_in',
        'length_in',
        'height_in',
        'sheet_full_w',
        'sheet_full_l',

        'company_id',
        'category_id',
        'discount_type_id',

        'name',
        'sku',
        'mrp',
        'purchase_price_manual',
        'selling_price_default',
        'sale_price_default',

        'is_active',
        'stock_qty',
        'low_stock_alert_qty',

        'is_variant_parent',
        'is_split_parent',
        'split_total_parts',

        'image_path',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
