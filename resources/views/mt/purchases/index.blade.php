@extends('mt.layouts.app')

@section('content')
@php
  $companyId = $companyId ?? '';
  $from = $from ?? '';
  $to = $to ?? '';
@endphp

  <div class="page-header">
    <h2 class="page-title">Purchases (Stock In)</h2>
    <a href="{{ route('mt.purchases.create') }}" class="btn btn-success btn-sm">+ New Purchase</a>
  </div>

  <div class="filter-bar">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">Company</span>
        <select name="company_id" class="mt-select" style="min-width:220px;">
          <option value="">All</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected((string)$companyId === (string)$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">From</span>
        <input type="date" name="from" value="{{ $from }}" class="mt-input">
      </div>
      <div class="filter-group">
        <span class="filter-label">To</span>
        <input type="date" name="to" value="{{ $to }}" class="mt-input">
      </div>
      <button class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Company/Supplier</th>
          <th>Invoice</th>
          <th class="text-right">Goods</th>
          <th class="text-right">Transport</th>
          <th class="text-right">Paid</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->purchase_date?->format('Y-m-d') }}</td>
            <td class="font-bold">{{ $r->company?->name ?? ($r->supplier_name ?? '-') }}</td>
            <td>{{ $r->invoice_no ?? '-' }}</td>
            <td class="text-right">{{ number_format((float)$r->goods_total,2) }}</td>
            <td class="text-right">{{ number_format((float)$r->transport_charges,2) }}</td>
            <td class="text-right">{{ number_format((float)$r->payment_made,2) }}</td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.purchases.show', $r) }}" class="btn btn-secondary btn-xs">Open</a>
                @if($r->company_id)
                  <a href="{{ route('mt.company_ledger.index', $r->company_id) }}" class="btn btn-teal btn-xs">Ledger</a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="7">No purchases found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
