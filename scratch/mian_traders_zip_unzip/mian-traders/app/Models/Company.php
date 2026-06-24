<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Multiple contacts (CEO, Sales Head, Manager, Coordinator etc.)
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class)
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
