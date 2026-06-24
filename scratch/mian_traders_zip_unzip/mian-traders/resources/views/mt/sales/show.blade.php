@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <div>
      <h2 style="margin:0;">Sale #{{ $sale->id }}</h2>
      <div style="color:#6b7280;font-size:12px;margin-top:4px;">
        {{ $sale->status ?? '-' }} / {{ $sale->sale_type ?? '-' }}
        @if($sale->due_date) | Due: {{ $sale->due_date }} @endif
      </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('mt.sales.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
      @if($sale->customer_phone)
        <a href="{{ route('mt.udhar.show', $sale->customer_phone) }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Customer Udhar</a>
      @endif
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:14px;align-items:start;">

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Customer</h3>
      <div><b>Name:</b> {{ $sale->customer_name ?? '-' }}</div>
      @php $wa = \App\Support\WhatsApp::url($sale->customer_phone); @endphp
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <b>Phone:</b>
        <span>{{ $sale->customer_phone ?? '-' }}</span>
        @if($wa)
          <a href="{{ $wa }}" target="_blank" rel="noopener"
             style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
        @endif
      </div>
      <div style="margin-top:10px;">
        <b>Note:</b> {{ $sale->note ?? '-' }}
      </div>

      <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">

      <h3 style="margin-top:0;">Items</h3>
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Sell</th>

            {{-- ✅ hide Purchase column for cashier --}}
            @if($canSeeCost)
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Purchase</th>
            @endif

            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->items as $it)
            <tr>
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $it->product_name }}</td>
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->qty,2) }}</td>
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->price,2) }}</td>

              @if($canSeeCost)
                <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->purchase_price,2) }}</td>
              @endif

              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->line_total,2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Summary</h3>

      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Total</span>
        <b>{{ number_format((float)$sale->total_amount,2) }}</b>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Paid</span>
        <b>{{ number_format((float)$sale->paid_amount,2) }}</b>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Balance</span>
        <b style="color:#b91c1c;">{{ number_format((float)$sale->balance_amount,2) }}</b>
      </div>

      {{-- ✅ hide profit blocks for cashier --}}
      @if($canSeeCost)
        <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">

        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span>Profit Total</span>
          <b>{{ number_format((float)$sale->profit_total,2) }}</b>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span>Profit Realized</span>
          <b>{{ number_format((float)$sale->profit_realized,2) }}</b>
        </div>
      @endif

      <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">

      <h3 style="margin-top:0;">Add Payment (This Bill)</h3>
      @if((float)$sale->balance_amount <= 0)
        <div style="background:#dcfce7;padding:10px;border-radius:10px;">This bill is fully paid.</div>
      @else
        <form method="POST" action="{{ route('mt.payments.store') }}" style="display:grid;gap:10px;">
          @csrf
          <input type="hidden" name="sale_id" value="{{ $sale->id }}">
          <input name="amount" type="number" step="0.01" placeholder="Payment amount"
                 style="padding:10px;border:1px solid #ddd;border-radius:10px;">
          <input name="note" placeholder="Note (optional)"
                 style="padding:10px;border:1px solid #ddd;border-radius:10px;">
          <button style="padding:12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
            Save Payment
          </button>
        </form>
      @endif

      <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">

      <h3 style="margin-top:0;">Payments</h3>
      @forelse($sale->payments as $pay)
        <div style="border:1px solid #eee;padding:10px;border-radius:10px;margin-bottom:8px;">
          <div style="display:flex;justify-content:space-between;">
            <b>{{ number_format((float)$pay->amount,2) }}</b>
            <span style="color:#6b7280;font-size:12px;">{{ $pay->created_at }}</span>
          </div>
          <div style="color:#6b7280;font-size:12px;margin-top:4px;">
            {{ $pay->note ?? '-' }}
          </div>
        </div>
      @empty
        <div style="color:#6b7280;">No payments yet.</div>
      @endforelse
    </div>

  </div>
@endsection
