@extends('mt.layouts.app')

@section('content')
@php
  $companyId = $companyId ?? '';
  $from = $from ?? '';
  $to = $to ?? '';
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Purchases (Stock In)</h2>
    <a href="{{ route('mt.purchases.create') }}" style="padding:10px 12px;border:0;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;">+ New Purchase</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Company</div>
        <select name="company_id" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:220px;">
          <option value="">All</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected((string)$companyId === (string)$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

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
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Company/Supplier</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Invoice</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Goods</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Transport</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->purchase_date?->format('Y-m-d') }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:700;">{{ $r->company?->name ?? ($r->supplier_name ?? '-') }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->invoice_no ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$r->goods_total,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$r->transport_charges,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$r->payment_made,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <a href="{{ route('mt.purchases.show', $r) }}" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Open</a>
              @if($r->company_id)
                <a href="{{ route('mt.company_ledger.index', $r->company_id) }}" style="padding:6px 10px;border-radius:8px;background:#0f766e;color:#fff;text-decoration:none;">Ledger</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="padding:12px;">No purchases found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $rows->links() }}
    </div>
  </div>
@endsection
