@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <div>
      <h2 class="page-title">Stock Movements</h2>
      <div class="page-subtitle">Manual stock in/out/adjust history.</div>
    </div>
    <div class="page-actions">
      <a href="{{ route('mt.stock_movements.create') }}" class="btn btn-success btn-sm">+ Add Movement</a>
      <form method="GET" class="flex gap-8 items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search product/note..." class="mt-input" style="min-width:200px;">
        <button class="btn btn-primary btn-sm">Search</button>
      </form>
    </div>
  </div>
  <div class="table-card">
    <table class="mt-table">
      <thead><tr><th>#</th><th>Product</th><th>Type</th><th class="text-right">Qty</th><th>Note</th><th>Date</th></tr></thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->product?->name ?? '-' }}</td>
            <td>
              @php $t = $r->type; @endphp
              <span class="badge {{ $t === 'in' ? 'badge-success' : ($t === 'out' ? 'badge-danger' : 'badge-info') }}">{{ strtoupper($t) }}</span>
            </td>
            <td class="text-right font-bold">{{ number_format((float)$r->qty,2) }}</td>
            <td>{{ $r->note ?? '-' }}</td>
            <td>{{ $r->created_at }}</td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="6">No movements yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
