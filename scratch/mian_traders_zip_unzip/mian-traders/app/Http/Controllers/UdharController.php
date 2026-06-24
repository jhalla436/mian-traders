<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UdharController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $rows = Sale::query()
            ->selectRaw('
                customer_phone,
                MAX(customer_name) as customer_name,
                SUM(total_amount) as total_sales,
                SUM(COALESCE(profit_total, 0)) as total_profit,
                SUM(COALESCE(profit_realized, 0)) as total_profit_realized,
                SUM(COALESCE(balance_amount, 0)) as total_udhar
            ')
            ->whereNotNull('customer_phone')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('customer_phone', 'like', "%{$q}%")
                       ->orWhere('customer_name', 'like', "%{$q}%");
                });
            })
            ->groupBy('customer_phone')
            ->havingRaw('SUM(COALESCE(balance_amount,0)) > 0')
            ->orderByDesc(DB::raw('SUM(COALESCE(balance_amount,0))'))
            ->paginate(25)
            ->withQueryString();

        return view('mt.udhar.index', compact('rows', 'q'));
    }

    public function show(string $phone)
    {
        $sales = Sale::query()
            ->where('customer_phone', $phone)
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->get();

        $totals = [
            'total' => (float) Sale::where('customer_phone', $phone)->sum('total_amount'),
            'paid' => (float) Sale::where('customer_phone', $phone)->sum('paid_amount'),
            'balance' => (float) Sale::where('customer_phone', $phone)->where('balance_amount','>',0)->sum('balance_amount'),
        ];

        return view('mt.udhar.show', compact('phone', 'sales', 'totals'));
    }

    /**
     * ✅ Pay total amount (not by bill) and distribute FIFO to oldest bills
     */
    public function payTotal(Request $request, string $phone)
    {
        $data = $request->validate([
            'amount' => ['required','numeric','min:0.01'],
            'note' => ['nullable','string','max:255'],
        ]);

        $amountLeft = (float)$data['amount'];

        $sales = Sale::query()
            ->where('customer_phone', $phone)
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get();

        if ($sales->count() === 0) {
            return redirect()->route('mt.udhar.show', $phone)->with('error', 'No outstanding bills for this customer.');
        }

        DB::transaction(function () use ($sales, &$amountLeft, $data) {
            foreach ($sales as $sale) {
                if ($amountLeft <= 0) break;

                $total = (float)$sale->total_amount;
                $paid = (float)$sale->paid_amount;
                $bal  = (float)$sale->balance_amount;

                $payNow = min($bal, $amountLeft);
                $payNow = round($payNow, 2);

                $newPaid = round($paid + $payNow, 2);
                $newBal  = round(max($total - $newPaid, 0), 2);

                $sale->paid_amount = $newPaid;
                $sale->balance_amount = $newBal;
                $sale->status = ($newBal <= 0 ? 'paid' : 'partial');
                $sale->sale_type = ($newBal > 0 ? 'udhar' : 'cash');

                // realized profit by ratio
                $profitTotal = (float)($sale->profit_total ?? 0);
                $sale->profit_realized = ($total > 0 ? round($profitTotal * ($newPaid / $total), 2) : 0);

                $sale->save();

                if (class_exists(SalePayment::class)) {
                    SalePayment::create([
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'amount' => $payNow,
                        'method' => null,
                        'note' => $data['note'] ?? null,
                    ]);
                }

                $amountLeft = round($amountLeft - $payNow, 2);
            }
        });

        return redirect()->route('mt.udhar.show', $phone)->with('success', 'Payment added successfully.');
    }

    /**
     * Optional: pay a single bill
     */
    public function payBill(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'amount' => ['required','numeric','min:0.01'],
            'method' => ['nullable','string','max:50'],
            'note' => ['nullable','string','max:255'],
        ]);

        $amount = (float)$data['amount'];

        DB::transaction(function () use ($sale, $amount, $data) {
            $sale->refresh();

            $total = (float)$sale->total_amount;
            $paid  = (float)$sale->paid_amount;
            $bal   = (float)$sale->balance_amount;

            $payNow = min($bal, $amount);
            $newPaid = round($paid + $payNow, 2);
            $newBal  = round(max($total - $newPaid, 0), 2);

            $sale->paid_amount = $newPaid;
            $sale->balance_amount = $newBal;
            $sale->status = ($newBal <= 0 ? 'paid' : 'partial');
            $sale->sale_type = ($newBal > 0 ? 'udhar' : 'cash');

            $profitTotal = (float)($sale->profit_total ?? 0);
            $sale->profit_realized = ($total > 0 ? round($profitTotal * ($newPaid / $total), 2) : 0);

            $sale->save();

            if (class_exists(SalePayment::class)) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'amount' => $payNow,
                    'method' => $data['method'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('mt.udhar.show', $sale->customer_phone)->with('success', 'Payment saved.');
    }
}
