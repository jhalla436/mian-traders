<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'date',
        'goods_total' => 'float',
        'transport_charges' => 'float',
        'payment_made' => 'float',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
