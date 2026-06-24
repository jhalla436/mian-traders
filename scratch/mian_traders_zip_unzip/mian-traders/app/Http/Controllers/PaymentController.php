<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'note'    => ['nullable', 'string', 'max:255'],
        ]);

        $sale = Sale::findOrFail($data['sale_id']);

        $amount  = (float) $data['amount'];
        $balance = (float) $sale->balance_amount;

        // Prevent overpayment (you can remove this if you want to allow advance)
        if ($balance > 0 && $amount > $balance) {
            return back()
                ->withErrors(['amount' => 'Payment is greater than remaining balance.'])
                ->withInput();
        }

        Payment::create([
            'sale_id' => $sale->id,
            'user_id' => auth()->id(),
            'amount'  => $amount,
            'note'    => $data['note'] ?? null,
        ]);

        // Update totals on the sale
        $sale->applyPayment($amount);

        return redirect()
            ->route('mt.sales.show', $sale->id)
            ->with('success', 'Payment saved.');
    }
}
