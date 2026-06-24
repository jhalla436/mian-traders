<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLedgerEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyLedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request, Company $company)
    {
        $from = trim((string)$request->get('from', ''));
        $to = trim((string)$request->get('to', ''));

        $q = CompanyLedgerEntry::query()->where('company_id', $company->id);

        if ($from !== '') {
            $q->where('entry_date', '>=', $from);
        }
        if ($to !== '') {
            $q->where('entry_date', '<=', $to);
        }

        $rows = $q->orderByDesc('entry_date')->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        $debitTotal = (float) CompanyLedgerEntry::where('company_id', $company->id)
            ->where('direction', 'debit')
            ->sum('amount');

        $creditTotal = (float) CompanyLedgerEntry::where('company_id', $company->id)
            ->where('direction', 'credit')
            ->sum('amount');

        $balance = $debitTotal - $creditTotal; // payable (+) / credit (-)

        return view('mt.companies.ledger.index', compact('company','rows','from','to','debitTotal','creditTotal','balance'));
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'entry_date' => ['required','date'],
            'entry_type' => ['required','string','max:40'],
            'direction' => ['required','in:debit,credit'],
            'amount' => ['required','numeric','min:0'],
            'description' => ['nullable','string','max:255'],
        ]);

        $data['company_id'] = $company->id;
        $data['user_id'] = auth()->id();

        CompanyLedgerEntry::create($data);

        return back()->with('success', 'Ledger entry added.');
    }

    public function destroy(Company $company, CompanyLedgerEntry $entry)
    {
        if ((int)$entry->company_id !== (int)$company->id) {
            abort(404);
        }
        $entry->delete();
        return back()->with('success', 'Ledger entry deleted.');
    }
}
