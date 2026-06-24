<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    /**
     * Find customer by ANY phone column.
     * Works if your table has:
     * phone, phone_primary, phone_alt_1, phone_alt_2
     */
    public static function findByAnyPhone(string $phone): ?self
    {
        $phone = trim($phone);
        if ($phone === '') return null;

        return self::query()
            ->where('phone', $phone)
            ->orWhere('phone_primary', $phone)
            ->orWhere('phone_alt_1', $phone)
            ->orWhere('phone_alt_2', $phone)
            ->first();
    }
}
