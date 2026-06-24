<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'order_date' => 'date',
        'goods_total' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompanyOrderItem::class)->orderBy('id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receivedPurchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'received_purchase_id');
    }
}
