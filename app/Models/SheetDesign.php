<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SheetDesign extends Model
{
    protected $fillable = [
        'name',
        'finish',
        'color_group',
        'sheet_codes',
    ];

    protected $casts = [
        'sheet_codes' => 'array',
    ];

    /**
     * Get all product variants linked to this design.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the mapped code/number for a specific company.
     */
    public function getCodeForCompany(int $companyId): ?string
    {
        $codes = $this->sheet_codes;
        if (is_array($codes) && isset($codes[$companyId])) {
            return (string)$codes[$companyId];
        }
        return null;
    }

    /**
     * Set the mapped code/number for a specific company.
     */
    public function setCodeForCompany(int $companyId, ?string $code): void
    {
        $codes = $this->sheet_codes ?: [];
        if ($code === null || trim($code) === '') {
            unset($codes[$companyId]);
        } else {
            $codes[$companyId] = trim($code);
        }
        $this->sheet_codes = $codes;
    }

    /**
     * Find a SheetDesign matching the company and its specific code/number.
     */
    public static function findByCompanyCode(int $companyId, string $code): ?self
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        // Try direct LIKE query first for DB-level performance
        $design = self::where('sheet_codes', 'like', '%"' . $companyId . '":"' . $code . '"%')->first();
        if ($design) {
            return $design;
        }

        // Fallback: check array memory in case formatting slightly differs
        return self::all()->first(function ($item) use ($companyId, $code) {
            $codes = $item->sheet_codes;
            return is_array($codes) && isset($codes[$companyId]) && trim((string)$codes[$companyId]) === $code;
        });
    }
}

