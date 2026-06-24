<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyGroup extends Model
{
    protected $guarded = [];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'group_key', 'key');
    }
}
