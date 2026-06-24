<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'max_discount_percent' => 'float',
        'default_original_discount' => 'float',
        'default_extra_discount' => 'float',
        'default_lamination_rate' => 'float',
    ];

    protected static function booted()
    {
        static::saved(function (Company $company) {
            $company->bumpProductGroupsCacheVersion();
        });
        static::deleted(function (Company $company) {
            $company->bumpProductGroupsCacheVersion();
        });
    }

    public function bumpProductGroupsCacheVersion(): void
    {
        $versionKey = "product_groups_company_version_{$this->id}";
        Cache::forever($versionKey, (int) Cache::get($versionKey, 0) + 1);
    }

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

    /**
     * Multiple brochures / catalogs for this company.
     */
    public function brochures(): HasMany
    {
        return $this->hasMany(CompanyBrochure::class);
    }
}
