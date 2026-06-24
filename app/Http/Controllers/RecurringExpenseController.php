<?php

namespace App\Http\Controllers;

use App\Models\RecurringExpense;
use Illuminate\Http\Request;

class RecurringExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index()
    {
        $rows = RecurringExpense::query()
            ->orderBy('is_active', 'desc')
            ->orderBy('day_of_month')
            ->orderBy('title')
            ->get();

        return view('mt.recurring_expenses.index', compact('rows'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'category' => ['nullable','string','max:80'],
            'vendor' => ['nullable','string','max:160'],
            'amount' => ['required','numeric','min:0'],
            'day_of_month' => ['required','integer','min:1','max:28'],
            'note' => ['nullable','string','max:255'],
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['user_id'] = auth()->id();
        RecurringExpense::create($data);

        return back()->with('success', 'Recurring expense saved. It will auto-add on that day each month when you open Dashboard.');
    }

    public function update(Request $request, RecurringExpense $recurringExpense)
    {
        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'category' => ['nullable','string','max:80'],
            'vendor' => ['nullable','string','max:160'],
            'amount' => ['required','numeric','min:0'],
            'day_of_month' => ['required','integer','min:1','max:28'],
            'note' => ['nullable','string','max:255'],
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $recurringExpense->update($data);

        return back()->with('success', 'Recurring expense updated.');
    }

    public function destroy(RecurringExpense $recurringExpense)
    {
        $recurringExpense->delete();
        return back()->with('success', 'Recurring expense deleted.');
    }
}
