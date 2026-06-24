@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Sales</h2>
    <div class="page-actions">
      <a href="{{ route('mt.pos.index') }}" class="btn btn-primary btn-sm">Go POS</a>
      <a href="{{ route('mt.udhar.index') }}" class="btn btn-secondary btn-sm">Udhar List</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Sale #</th>
          <th>Customer</th>
          <th>Phone</th>
          <th class="text-right">Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr>
            <td class="font-bold">#{{ $s->id }}</td>
            <td>{{ $s->customer_name ?? '-' }}</td>
            <td>
              @php $wa = \App\Support\WhatsApp::url($s->customer_phone); @endphp
              <div class="flex gap-8 items-center flex-wrap">
                <span>{{ $s->customer_phone ?? '-' }}</span>
                @if($wa)
                  <a href="{{ $wa }}" target="_blank" rel="noopener" class="badge-wa">WA</a>
                @endif
              </div>
            </td>
            <td class="text-right">{{ number_format((float)$s->total_amount,2) }}</td>
            <td class="text-right">{{ number_format((float)$s->paid_amount,2) }}</td>
            <td class="text-right font-bold" style="color:#dc2626;">{{ number_format((float)$s->balance_amount,2) }}</td>
            <td><span class="badge badge-gray">{{ $s->status ?? '-' }} / {{ $s->sale_type ?? '-' }}</span></td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.sales.show', $s) }}" class="btn btn-secondary btn-xs">View</a>
                @if($s->customer_phone)
                  <a href="{{ route('mt.customers.profile', $s->customer_phone) }}" class="btn btn-success btn-xs">Profile</a>
                @endif
                @if($s->customer_phone && (float)$s->balance_amount > 0)
                  <a href="{{ route('mt.udhar.show', $s->customer_phone) }}" class="btn btn-primary btn-xs">Udhar</a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="8">No sales found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $sales->links() }}</div>
  </div>
@endsection
