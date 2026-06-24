<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'order_date' => 'date',
        'goods_total' => 'float',
        'original_discount' => 'float',
        'extra_discount' => 'float',
        'paid_amount' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompanyOrderItem::class)->orderBy('id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receivedPurchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'received_purchase_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(CompanyLedgerEntry::class, 'ref_id')
            ->where('ref_type', 'company_order')
            ->orderBy('entry_date')
            ->orderBy('id');
    }

    public function getBalanceAttribute(): float
    {
        return round($this->goods_total - $this->paid_amount, 2);
    }

    public function updatePaymentStatus(): void
    {
        $paid = (float)$this->ledgerEntries()
            ->where('entry_type', 'payment')
            ->where('direction', 'credit')
            ->sum('amount');

        $this->paid_amount = round($paid, 2);

        if ($this->paid_amount >= $this->goods_total) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }
}
