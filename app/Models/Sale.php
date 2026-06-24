<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'user_id',
        'customer_id',

        'customer_name',
        'customer_phone',
        'customer_phone2',
        'customer_phone3',
        'customer_address',
        'customer_cnic',

        'total_amount',
        'subtotal_amount',
        'item_discount_total',
        'overall_discount_amount',
        'profit_total',

        'paid_amount',
        'received_amount',
        'change_returned',
        'balance_amount',

        'realized_profit',
        'profit_realized',
        'unrealized_profit',

        'note',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Profit Realization (Option B: proportional)
    |--------------------------------------------------------------------------
    | realized_profit   = profit_total * (paid_amount / total_amount)
    | unrealized_profit = profit_total - realized_profit
    */

    public function applyPayment(float $amount = 0): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $total     = (float) ($this->total_amount ?? 0);
        $profit    = (float) ($this->profit_total ?? 0);

        $balance = max(0, $total - $totalPaid);

        $this->paid_amount    = $totalPaid;
        $this->balance_amount = $balance;

        // proportional realization
        if ($total > 0 && $profit != 0.0) {
            $ratio = $totalPaid / $total;
            if ($ratio < 0) $ratio = 0;
            if ($ratio > 1) $ratio = 1;

            $realized = round($profit * $ratio, 2);
            $unreal   = round($profit - $realized, 2);

            $this->realized_profit   = $realized;
            $this->profit_realized   = $realized;
            $this->unrealized_profit = $unreal;
        } else {
            $this->realized_profit   = 0;
            $this->profit_realized   = 0;
            $this->unrealized_profit = $profit;
        }

        $this->save();
    }
}
