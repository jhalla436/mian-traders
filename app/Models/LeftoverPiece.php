<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeftoverPiece extends Model
{
    protected $fillable = [
        'product_id',
        'width_ft',
        'length_ft',
        'qty',
        'is_active',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
