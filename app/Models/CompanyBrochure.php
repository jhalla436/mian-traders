<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBrochure extends Model
{
    protected $fillable = [
        'company_id',
        'file_path',
        'file_name',
        'mime_type',
    ];

    /**
     * Get the company that owns the brochure.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
