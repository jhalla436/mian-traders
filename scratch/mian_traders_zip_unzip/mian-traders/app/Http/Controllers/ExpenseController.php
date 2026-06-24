<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $from = trim((string)$request->get('from', ''));
        $to = trim((string)$request->get('to', ''));

        $q = Expense::query();
        if ($from !== '') {
            $q->where('expense_date', '>=', $from);
        }
        if ($to !== '') {
            $q->where('expense_date', '<=', $to);
        }

        $rows = $q->orderByDesc('expense_date')->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        $total = (float) (clone $q)->sum('amount');

        return view('mt.expenses.index', compact('rows','from','to','total'));
    }

    public function create()
    {
        $today = Carbon::today()->toDateString();
        return view('mt.expenses.create', compact('today'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_date' => ['required','date'],
            'title' => ['required','string','max:160'],
            'category' => ['nullable','string','max:80'],
            'vendor' => ['nullable','string','max:160'],
            'amount' => ['required','numeric','min:0'],
            'payment_method' => ['nullable','string','max:40'],
            'note' => ['nullable','string','max:255'],
        ]);

        $data['user_id'] = auth()->id();
        Expense::create($data);

        return redirect()->route('mt.expenses.index')->with('success', 'Expense added.');
    }
}
