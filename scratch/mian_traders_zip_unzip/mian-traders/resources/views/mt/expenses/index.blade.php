@extends('mt.layouts.app')

@section('content')
@php
  $from = $from ?? '';
  $to = $to ?? '';
  $total = $total ?? 0;
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Expenses</h2>
    <a href="{{ route('mt.expenses.create') }}" style="padding:10px 12px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;">+ Add Expense</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
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

    <div style="margin-left:auto;padding:10px 12px;border-radius:10px;background:#0f172a;color:#fff;">
      Total: <b>{{ number_format((float)$total,2) }}</b>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Title</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Category</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Vendor</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->expense_date?->format('Y-m-d') }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;">{{ $r->title }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->category ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->vendor ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:800;">{{ number_format((float)$r->amount,2) }}</td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:12px;">No expenses found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">{{ $rows->links() }}</div>
  </div>
@endsection
