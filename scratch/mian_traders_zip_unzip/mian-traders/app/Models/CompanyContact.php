<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyContact extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(CompanyContact::class, 'reports_to_contact_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(CompanyContact::class, 'reports_to_contact_id');
    }
}
