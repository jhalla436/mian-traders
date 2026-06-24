@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-subtitle">Purchase #{{ $purchase->id }}</div>
      <h2 class="page-title">{{ $purchase->company?->name ?? ($purchase->supplier_name ?? 'Supplier') }}</h2>
    </div>
    <div class="page-actions">
      <a href="{{ route('mt.purchases.index') }}" class="btn btn-secondary btn-sm">← Back</a>
      @if($purchase->company_id)
        <a href="{{ route('mt.company_ledger.index', $purchase->company_id) }}" class="btn btn-teal btn-sm">Open Ledger</a>
      @endif
    </div>
  </div>

  <div class="filter-bar" style="gap:24px;">
    <div>
      <div class="filter-label">Date</div>
      <div class="font-bold">{{ $purchase->purchase_date?->format('Y-m-d') }}</div>
    </div>
    <div>
      <div class="filter-label">Invoice #</div>
      <div class="font-bold">{{ $purchase->invoice_no ?? '-' }}</div>
    </div>
    <div>
      <div class="filter-label">Goods Total</div>
      <div class="font-bold">{{ number_format((float)$purchase->goods_total,2) }}</div>
    </div>
    <div>
      <div class="filter-label">Transport</div>
      <div class="font-bold">{{ number_format((float)$purchase->transport_charges,2) }}</div>
    </div>
    <div>
      <div class="filter-label">Paid</div>
      <div class="font-bold">{{ number_format((float)$purchase->payment_made,2) }}</div>
    </div>
  </div>

  @if($purchase->note)
    <div class="card mb-16">
      <div class="filter-label">Note</div>
      <div class="font-bold">{{ $purchase->note }}</div>
    </div>
  @endif

  <div class="table-card">
    <div style="padding:16px 20px 0;">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Items</h3>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Product</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Unit Cost</th>
          <th class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchase->items as $it)
          <tr>
            <td class="font-bold">{{ $it->product?->name ?? ('#'.$it->product_id) }}</td>
            <td class="text-right">{{ number_format((float)$it->qty,2) }}</td>
            <td class="text-right">{{ number_format((float)$it->unit_cost,2) }}</td>
            <td class="text-right font-bold">{{ number_format((float)$it->line_total,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
