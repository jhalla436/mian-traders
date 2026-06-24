<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecurringExpense extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'amount' => 'float',
        'day_of_month' => 'integer',
    ];

    /**
     * Idempotent: generates each recurring expense at most once per month.
     * We run this from DashboardController, so the user doesn't need cron.
     */
    public static function generateDueForDate(Carbon $date): int
    {
        $monthKey = $date->format('Y-m');

        $due = self::query()
            ->where('is_active', 1)
            ->where('day_of_month', (int)$date->day)
            ->get();

        $created = 0;

        DB::transaction(function () use ($due, $monthKey, $date, &$created) {
            foreach ($due as $r) {
                if ((string)($r->last_generated_month ?? '') === $monthKey) {
                    continue;
                }

                Expense::create([
                    'shop_id' => $r->shop_id ?? null,
                    'expense_date' => $date->toDateString(),
                    'title' => $r->title,
                    'category' => $r->category,
                    'vendor' => $r->vendor,
                    'amount' => (float)$r->amount,
                    'payment_method' => null,
                    'note' => $r->note,
                    'user_id' => auth()->id(),
                ]);

                $r->last_generated_month = $monthKey;
                $r->save();
                $created++;
            }
        });

        return $created;
    }
}
