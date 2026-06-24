@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
  <h2 style="margin:0;">Udhar Customers (Merged by Phone)</h2>

  <form method="GET" style="display:flex;gap:8px;align-items:center;">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Search phone/name..."
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">
    <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>
  </form>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;">
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Phone</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Name</th>
        <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Total Sales</th>

        {{-- ✅ hide profit columns from cashier --}}
        @if($canSeeCost)
          <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Total Profit</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Realized Profit</th>
        @endif

        <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Current Udhar</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
            @php $wa = \App\Support\WhatsApp::url($r->customer_phone); @endphp
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
              <span>{{ $r->customer_phone }}</span>
              @if($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener"
                   style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
              @endif
            </div>
          </td>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;">{{ $r->customer_name ?? '-' }}</td>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;">{{ number_format((float)$r->total_sales,2) }}</td>

          @if($canSeeCost)
            <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;">{{ number_format((float)$r->total_profit,2) }}</td>
            <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;">{{ number_format((float)$r->total_profit_realized,2) }}</td>
          @endif

          <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;color:#b91c1c;">
            {{ number_format((float)$r->total_udhar,2) }}
          </td>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
            <a href="{{ route('mt.udhar.show', $r->customer_phone) }}"
               style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">
              Open
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:12px;">
    {{ $rows->links() }}
  </div>
</div>
@endsection
