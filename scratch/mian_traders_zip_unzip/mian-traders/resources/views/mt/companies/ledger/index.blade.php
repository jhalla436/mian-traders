@extends('mt.layouts.app')

@section('content')
@php
  $from = $from ?? '';
  $to = $to ?? '';
  $balance = $balance ?? 0;
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <div style="font-size:12px;color:#6b7280;">Company Ledger</div>
      <h2 style="margin:0;">{{ $company->name }}</h2>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <a href="{{ route('mt.companies.index') }}" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
      <div style="padding:10px 12px;border-radius:10px;background:#0f172a;color:#fff;">
        Balance: <b>{{ number_format((float)$balance,2) }}</b>
        <span style="font-size:12px;color:#cbd5e1;">(Debit - Credit)</span>
      </div>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;gap:14px;flex-wrap:wrap;align-items:end;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      <div>
        <div style="font-size:12px;color:#6b7280;">From</div>
        <input type="date" name="from" value="{{ $from }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">To</div>
        <input type="date" name="to" value="{{ $to }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Filter</button>
    </form>

    <div style="margin-left:auto;display:flex;gap:10px;flex-wrap:wrap;">
      <a href="{{ route('mt.purchases.create', ['company_id'=>$company->id]) }}" style="padding:10px 12px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;">+ Stock In (Purchase)</a>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <h3 style="margin:0 0 10px 0;">Add Entry</h3>
    <form method="POST" action="{{ route('mt.company_ledger.store', $company) }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      @csrf
      <div>
        <div style="font-size:12px;color:#6b7280;">Date</div>
        <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Type</div>
        <select name="entry_type" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="payment">Payment</option>
          <option value="transport">Transport</option>
          <option value="adjustment">Adjustment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Direction</div>
        <select name="direction" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="credit">Credit (reduces payable)</option>
          <option value="debit">Debit (increases payable)</option>
        </select>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Amount</div>
        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:160px;text-align:right;">
      </div>
      <div style="flex:1;min-width:220px;">
        <div style="font-size:12px;color:#6b7280;">Description</div>
        <input name="description" value="{{ old('description') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Optional">
      </div>
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#0f766e;color:#fff;">Save</button>
    </form>
    @if($errors->any())
      <div style="margin-top:10px;color:#dc2626;font-size:12px;">
        {{ implode(' | ', $errors->all()) }}
      </div>
    @endif
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:10px;">
      <div style="padding:10px 12px;border-radius:10px;background:#f1f5f9;">
        Total Debit: <b>{{ number_format((float)$debitTotal,2) }}</b>
      </div>
      <div style="padding:10px 12px;border-radius:10px;background:#f1f5f9;">
        Total Credit: <b>{{ number_format((float)$creditTotal,2) }}</b>
      </div>
    </div>

    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Type</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Description</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Debit</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Credit</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->entry_date?->format('Y-m-d') }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;">{{ $r->entry_type }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->description ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ $r->direction==='debit' ? number_format((float)$r->amount,2) : '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ $r->direction==='credit' ? number_format((float)$r->amount,2) : '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <form method="POST" action="{{ route('mt.company_ledger.destroy', [$company, $r]) }}" onsubmit="return confirm('Delete this ledger entry?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button style="padding:6px 10px;border-radius:8px;background:#ef4444;color:#fff;border:0;">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:12px;">No ledger entries.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $rows->links() }}
    </div>
  </div>
@endsection
