@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Order #{{ $order->id }}</h2>
      <div style="color:#6b7280;font-size:12px;">Company: <b>{{ $order->company?->name }}</b> • Date: {{ optional($order->order_date)->format('Y-m-d') }} • Status: <b>{{ strtoupper($order->status) }}</b></div>
    </div>
    <a href="{{ route('mt.company_orders.index') }}" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;border-radius:10px;overflow:hidden;margin-bottom:12px;">
    <div style="padding:12px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Goods Total</div>
        <div style="font-size:20px;font-weight:900;">{{ number_format((float)$order->goods_total, 2) }}</div>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Note</div>
        <div>{{ $order->note ?: '-' }}</div>
      </div>
    </div>

    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Product</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Qty</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Unit Cost</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Line Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $it)
          <tr>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $it->product?->name }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;">{{ rtrim(rtrim(number_format((float)$it->qty, 3), '0'), '.') }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;">{{ number_format((float)$it->unit_cost, 2) }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:900;">{{ number_format((float)$it->line_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($order->status === 'open')
    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Receive Order (Stock In)</h3>
      <form method="POST" action="{{ route('mt.company_orders.receive', $order) }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
        @csrf
        <div>
          <div style="font-size:12px;color:#6b7280;">Purchase Date</div>
          <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div>
          <div style="font-size:12px;color:#6b7280;">Transport Charges (we paid)</div>
          <input type="number" step="0.01" name="transport_charges" value="0" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div>
          <div style="font-size:12px;color:#6b7280;">Payment made to company</div>
          <input type="number" step="0.01" name="payment_made" value="0" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div style="flex:1;min-width:260px;">
          <div style="font-size:12px;color:#6b7280;">Note</div>
          <input name="note" placeholder="Optional" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;">
        </div>
        <button style="padding:12px 14px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:900;">Receive & Stock In</button>
      </form>
      <div style="font-size:12px;color:#6b7280;margin-top:8px;">Receiving will: <b>add stock</b>, create a <b>Purchase</b>, and update <b>Company Ledger</b> (reverse order estimate + actual purchase + transport/payment).</div>
    </div>
  @else
    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="font-weight:900;">This order is {{ strtoupper($order->status) }}.</div>
      @if($order->receivedPurchase)
        <div style="margin-top:6px;">Linked Purchase: <a href="{{ route('mt.purchases.show', $order->receivedPurchase) }}">Purchase #{{ $order->received_purchase_id }}</a></div>
      @endif
    </div>
  @endif
@endsection
