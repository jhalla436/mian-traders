<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyOrderItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'qty' => 'float',
        'unit_cost' => 'float',
        'line_total' => 'float',
        'original_discount' => 'float',
        'extra_discount' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(CompanyOrder::class, 'company_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
