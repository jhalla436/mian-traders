<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLedgerEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'float',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companyOrder()
    {
        return $this->belongsTo(CompanyOrder::class, 'ref_id')->where('ref_type', 'company_order');
    }

    /**
     * debit increases payable balance, credit decreases payable balance.
     */
    public function signedAmount(): float
    {
        $a = (float)($this->amount ?? 0);
        return $this->direction === 'credit' ? -$a : $a;
    }
}
