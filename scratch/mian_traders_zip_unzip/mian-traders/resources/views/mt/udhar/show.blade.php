@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
  <div>
    @php $wa = \App\Support\WhatsApp::url($phone); @endphp
    <h2 style="margin:0;">Customer Ledger</h2>
    <div style="color:#6b7280;font-size:12px;margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <span>Phone: <b>{{ $phone }}</b></span>
      @if($wa)
        <a href="{{ $wa }}" target="_blank" rel="noopener"
           style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
      @endif
      <span style="margin-left:6px;">All udhar bills merged by phone. Payments here are added in TOTAL (not per bill).</span>
    </div>
  </div>

  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <a href="{{ route('mt.udhar.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
    <a href="{{ route('mt.sales.index') }}" style="padding:8px 12px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;">Sales</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:12px;align-items:start;">

  {{-- LEFT: Bills --}}
  <div style="background:#fff;padding:14px;border-radius:10px;">
    <h3 style="margin-top:0;">Bills (Unpaid/Partial)</h3>

    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Bill #</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Balance</th>

          {{-- ✅ hide profit per bill from cashier --}}
          @if($canSeeCost)
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Profit</th>
          @endif

          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">#{{ $s->id }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$s->total_amount,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$s->paid_amount,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;color:#b91c1c;font-weight:800;">{{ number_format((float)$s->balance_amount,2) }}</td>

            @if($canSeeCost)
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">
                {{ number_format((float)($s->profit_total ?? 0),2) }}
              </td>
            @endif

            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <a href="{{ route('mt.sales.show', $s) }}" style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Open</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="{{ $canSeeCost ? 6 : 5 }}" style="padding:12px;color:#6b7280;">No udhar bills for this phone.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- RIGHT: Totals + Pay Total --}}
  <div style="background:#fff;padding:14px;border-radius:10px;">
    <h3 style="margin-top:0;">Totals</h3>

    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
      <span>Total Sales (All Time)</span>
      <b>{{ number_format((float)($totalSalesAll ?? 0),2) }}</b>
    </div>

    {{-- ✅ hide profit totals from cashier --}}
    @if($canSeeCost)
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Total Profit (All Time)</span>
        <b>{{ number_format((float)($totalProfitAll ?? 0),2) }}</b>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Realized Profit</span>
        <b>{{ number_format((float)($totalProfitRealizedAll ?? 0),2) }}</b>
      </div>
    @endif

    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
      <span>Current Udhar (Total Balance)</span>
      <b style="color:#b91c1c;">{{ number_format((float)($totalUdhar ?? 0),2) }}</b>
    </div>

    <hr style="margin:12px 0;border:0;border-top:1px solid #eee;">

    <h3 style="margin-top:0;">Add Payment (TOTAL)</h3>

    @if((float)($totalUdhar ?? 0) <= 0)
      <div style="background:#dcfce7;padding:10px;border-radius:10px;">This customer has no udhar remaining.</div>
    @else
      <form method="POST" action="{{ route('mt.udhar.pay_total', $phone) }}" style="display:grid;gap:10px;">
        @csrf

        <input name="amount" type="number" step="0.01" min="0" placeholder="Payment amount"
               style="padding:10px;border:1px solid #ddd;border-radius:10px;">

        <input name="note" placeholder="Note (optional)"
               style="padding:10px;border:1px solid #ddd;border-radius:10px;">

        <button style="padding:12px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
          Add Payment
        </button>
      </form>

      <div style="margin-top:8px;color:#6b7280;font-size:12px;">
        Payment will reduce the customer’s TOTAL udhar across all bills.
      </div>
    @endif
  </div>

</div>
@endsection
