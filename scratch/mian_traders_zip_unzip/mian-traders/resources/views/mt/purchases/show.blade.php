@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <div style="font-size:12px;color:#6b7280;">Purchase #{{ $purchase->id }}</div>
      <h2 style="margin:0;">{{ $purchase->company?->name ?? ($purchase->supplier_name ?? 'Supplier') }}</h2>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="{{ route('mt.purchases.index') }}" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
      @if($purchase->company_id)
        <a href="{{ route('mt.company_ledger.index', $purchase->company_id) }}" style="padding:10px 12px;border-radius:10px;background:#0f766e;color:#fff;text-decoration:none;">Open Company Ledger</a>
      @endif
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;gap:14px;flex-wrap:wrap;">
    <div><div style="font-size:12px;color:#6b7280;">Date</div><div style="font-weight:800;">{{ $purchase->purchase_date?->format('Y-m-d') }}</div></div>
    <div><div style="font-size:12px;color:#6b7280;">Invoice #</div><div style="font-weight:800;">{{ $purchase->invoice_no ?? '-' }}</div></div>
    <div><div style="font-size:12px;color:#6b7280;">Goods Total</div><div style="font-weight:800;">{{ number_format((float)$purchase->goods_total,2) }}</div></div>
    <div><div style="font-size:12px;color:#6b7280;">Transport</div><div style="font-weight:800;">{{ number_format((float)$purchase->transport_charges,2) }}</div></div>
    <div><div style="font-size:12px;color:#6b7280;">Paid</div><div style="font-weight:800;">{{ number_format((float)$purchase->payment_made,2) }}</div></div>
  </div>

  @if($purchase->note)
    <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
      <div style="font-size:12px;color:#6b7280;">Note</div>
      <div style="font-weight:700;">{{ $purchase->note }}</div>
    </div>
  @endif

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <h3 style="margin:0 0 10px 0;">Items</h3>
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Unit Cost</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchase->items as $it)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:700;">{{ $it->product?->name ?? ('#'.$it->product_id) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->qty,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$it->unit_cost,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:800;">{{ number_format((float)$it->line_total,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
