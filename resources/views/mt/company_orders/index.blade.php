@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Company Orders</h2>
    <a href="{{ route('mt.company_orders.create') }}" class="btn btn-success btn-sm">+ New Order</a>
  </div>

  <div class="filter-bar">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">Company</span>
        <select name="company_id" class="mt-select" style="min-width:240px;">
          <option value="">-- All --</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected((string)$companyId === (string)$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Status</span>
        <select name="status" class="mt-select" style="min-width:180px;">
          <option value="">-- All --</option>
          <option value="open" @selected($status==='open')>Open</option>
          <option value="received" @selected($status==='received')>Received</option>
          <option value="cancelled" @selected($status==='cancelled')>Cancelled</option>
        </select>
      </div>
      <button class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Company</th>
          <th>Date</th>
          <th>Status</th>
          <th>Payment Status</th>
          <th class="text-right">Goods Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          @php
            $payClass = 'badge-danger';
            if ($r->payment_status === 'paid') $payClass = 'badge-success';
            elseif ($r->payment_status === 'partial') $payClass = 'badge-info';
          @endphp
          <tr>
            <td><a href="{{ route('mt.company_orders.show', $r) }}" class="font-bold" style="color:#6366f1;">#{{ $r->id }}</a></td>
            <td>{{ $r->company?->name }}</td>
            <td>{{ optional($r->order_date)->format('Y-m-d') }}</td>
            <td><span class="badge badge-gray">{{ strtoupper($r->status) }}</span></td>
            <td><span class="badge {{ $payClass }}">{{ strtoupper($r->payment_status ?: 'unpaid') }}</span></td>
            <td class="text-right font-bold">{{ number_format((float)$r->goods_total, 2) }}</td>
            <td class="text-right text-success font-semibold">{{ number_format((float)$r->paid_amount, 2) }}</td>
            <td class="text-right text-danger font-semibold">{{ number_format((float)$r->balance, 2) }}</td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.company_orders.show', $r) }}" class="btn btn-primary btn-xs">View</a>
                @if($r->status !== 'received')
                  <form method="POST" action="{{ route('mt.company_orders.destroy', $r) }}" onsubmit="return confirm('Delete order #{{ $r->id }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs">Delete</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="9">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
