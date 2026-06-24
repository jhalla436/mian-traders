@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div class="page-header">
  <div>
    @php $wa = \App\Support\WhatsApp::url($phone); @endphp
    <h2 class="page-title">Customer Ledger</h2>
    <div class="page-subtitle flex gap-8 items-center flex-wrap">
      <span>Phone: <b>{{ $phone }}</b></span>
      @if($wa)
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="badge-wa">WA</a>
      @endif
      <span>All udhar bills merged by phone. Payments here are added in TOTAL (not per bill).</span>
    </div>
  </div>

  <div class="page-actions">
    <a href="{{ route('mt.udhar.index') }}" class="btn btn-secondary btn-sm">← Back</a>
    <a href="{{ route('mt.sales.index') }}" class="btn btn-outline btn-sm">Sales</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:16px;align-items:start;">

  {{-- LEFT: Bills --}}
  <div class="table-card">
    <div style="padding:16px 20px 0;">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Bills (Unpaid/Partial)</h3>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Bill #</th>
          <th class="text-right">Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          @if($canSeeCost)
            <th class="text-right">Profit</th>
          @endif
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr>
            <td class="font-bold">#{{ $s->id }}</td>
            <td class="text-right">{{ number_format((float)$s->total_amount,2) }}</td>
            <td class="text-right">{{ number_format((float)$s->paid_amount,2) }}</td>
            <td class="text-right text-danger font-bold">{{ number_format((float)$s->balance_amount,2) }}</td>
            @if($canSeeCost)
              <td class="text-right">{{ number_format((float)($s->profit_total ?? 0),2) }}</td>
            @endif
            <td>
              <a href="{{ route('mt.sales.show', $s) }}" class="btn btn-primary btn-xs">Open</a>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="{{ $canSeeCost ? 6 : 5 }}">No udhar bills for this phone.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- RIGHT: Totals + Pay Total --}}
  <div class="card">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Totals</h3>

    <div class="flex justify-between" style="margin-bottom:8px;">
      <span>Total Sales (All Time)</span>
      <b>{{ number_format((float)($totalSalesAll ?? 0),2) }}</b>
    </div>

    @if($canSeeCost)
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Total Profit (All Time)</span>
        <b class="text-success">{{ number_format((float)($totalProfitAll ?? 0),2) }}</b>
      </div>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Realized Profit</span>
        <b class="text-success">{{ number_format((float)($totalProfitRealizedAll ?? 0),2) }}</b>
      </div>
    @endif

    <div class="flex justify-between" style="margin-bottom:8px;">
      <span>Current Udhar (Total Balance)</span>
      <b class="text-danger">{{ number_format((float)($totalUdhar ?? 0),2) }}</b>
    </div>

    <hr class="separator">

    <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Add Payment (TOTAL)</h3>

    @if((float)($totalUdhar ?? 0) <= 0)
      <div class="badge badge-success" style="padding:10px 14px;font-size:13px;">This customer has no udhar remaining.</div>
    @else
      <form method="POST" action="{{ route('mt.udhar.pay_total', $phone) }}" class="form-stack">
        @csrf
        <input name="amount" type="number" step="0.01" min="0" placeholder="Payment amount" class="mt-input">
        <select name="method" class="mt-select">
          @foreach(\App\Support\PaymentMethod::options() as $methodKey => $methodLabel)
            <option value="{{ $methodKey }}" @selected($methodKey === \App\Support\PaymentMethod::HARD_CASH)>{{ $methodLabel }}</option>
          @endforeach
        </select>
        <input name="note" placeholder="Note (optional)" class="mt-input">
        <button class="btn btn-success">Add Payment</button>
      </form>
      <div class="text-xs text-muted" style="margin-top:8px;">
        Payment will reduce the customer's TOTAL udhar across all bills.
      </div>
    @endif
  </div>

</div>
@endsection
