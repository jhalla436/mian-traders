<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Str;

class CustomerPaymentService
{
    /**
     * Apply a customer payment (by phone) to oldest unpaid sales first.
     * Returns payment_ref for grouping.
     */
    public function applyByPhone(string $phone, float $amount, ?string $method = null, ?string $note = null): string
    {
        $phone = trim($phone);
        $amount = (float)$amount;

        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be > 0");
        }

        $ref = (string) Str::uuid();
        $remaining = $amount;

        // Oldest unpaid first
        $sales = Sale::query()
            ->where('customer_phone', $phone)
            ->whereIn('status', ['udhar','unpaid','partial']) // keep compatible with your statuses
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->get();

        foreach ($sales as $sale) {
            if ($remaining <= 0) break;

            $balance = (float)$sale->balance_amount;
            if ($balance <= 0) continue;

            $payHere = min($balance, $remaining);

            // record allocation
            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_ref' => $ref,
                'user_id' => auth()->id(),
                'amount' => $payHere,
                'method' => $method,
                'note' => $note,
            ]);

            // update sale totals
            $newPaid = (float)$sale->paid_amount + $payHere;
            $newBal  = max(0, (float)$sale->total_amount - $newPaid);

            $status = $newBal <= 0 ? 'paid' : 'partial';

            $sale->paid_amount = $newPaid;
            $sale->balance_amount = $newBal;
            $sale->status = $status;

            // ✅ Profit realized proportional to payment
            $total = (float)($sale->total_amount ?? 0);
            if ($total > 0) {
                $ratio = min(1, max(0, $newPaid / $total));
                $sale->profit_realized = round((float)($sale->profit_total ?? 0) * $ratio, 2);
            } else {
                $sale->profit_realized = (float)($sale->profit_total ?? 0);
            }

            $sale->save();

            $remaining -= $payHere;
        }

        // If customer paid more than udhar, you can:
        // - keep remaining as "advance" (needs a table)
        // - or just stop (current code stops allocating once all bills paid)
        return $ref;
    }
}
