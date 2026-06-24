@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Products</h2>

    <form method="GET" style="display:flex;gap:8px;align-items:center;">
      <input name="q" value="{{ $q }}" placeholder="Search name/sku/id..."
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>
    </form>
  </div>

  @if(auth()->user()?->role !== 'cashier')
    <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
      <a href="{{ route('mt.products.create') }}" style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">+ Add Product</a>

      <a href="{{ route('mt.products.import_form') }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Import Price List</a>
    </div>
  @endif

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Category</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Company</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">MRP</th>

          {{-- ✅ hide Purchase from cashier --}}
          @if($canSeeCost)
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Purchase</th>
          @endif

          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Stock</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          @php
            $purchase = \App\Services\PricingService::purchasePrice($p);
          @endphp
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;">{{ $p->name }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $p->category?->name ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $p->company?->name ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$p->mrp,2) }}</td>

            @if($canSeeCost)
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:700;">
                {{ number_format((float)$purchase,2) }}
              </td>
            @endif

            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$p->stock_qty,2) }}</td>

            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              @if(auth()->user()?->role !== 'cashier')
                <a href="{{ route('mt.products.edit', $p) }}" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>
              @else
                <span style="color:#6b7280;font-size:12px;">View only</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="padding:12px;">No products found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $products->links() }}
    </div>
  </div>
@endsection