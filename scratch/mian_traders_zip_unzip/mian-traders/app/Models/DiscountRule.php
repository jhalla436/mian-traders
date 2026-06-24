<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    protected $fillable = [
        'scope_type',
        'scope_id',
        'discount_type_id',
        'rule_type',
        'percent_1',
        'percent_2',
        'fixed_purchase_price',
        'note',
    ];

    protected $casts = [
        'percent_1' => 'float',
        'percent_2' => 'float',
        'fixed_purchase_price' => 'float',
    ];

    public function discountType()
    {
        return $this->belongsTo(DiscountType::class);
    }
}
