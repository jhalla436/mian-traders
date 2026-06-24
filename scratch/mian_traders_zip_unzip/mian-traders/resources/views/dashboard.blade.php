@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Dashboard</h2>
      <div style="color:#6b7280;font-size:12px;margin-top:4px;">
        Week = Monday to Sunday | Month = current month
      </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('mt.pos.index') }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Go POS</a>
      <a href="{{ route('mt.sales.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Sales</a>
      <a href="{{ route('mt.udhar.index') }}" style="padding:8px 12px;border-radius:8px;background:#b91c1c;color:#fff;text-decoration:none;">Udhar</a>
    </div>
  </div>

  {{-- TOP CARDS --}}
  <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:12px;align-items:stretch;margin-bottom:12px;">

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Total Udhar (All Customers)</div>
      <div style="font-size:22px;font-weight:800;color:#b91c1c;margin-top:6px;">
        {{ number_format((float)($totalUdharAll ?? 0), 2) }}
      </div>
      <div style="margin-top:10px;">
        <a href="{{ route('mt.udhar.index') }}">Open Udhar List →</a>
      </div>
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Sales Today</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($salesToday ?? 0), 2) }}
      </div>
      @if($canSeeCost)
        <div style="color:#6b7280;font-size:12px;margin-top:6px;">
          Profit: <b style="color:#16a34a;">{{ number_format((float)($profitToday ?? 0), 2) }}</b>
        </div>
      @endif
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Sales This Week</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($salesThisWeek ?? 0), 2) }}
      </div>
      @if($canSeeCost)
        <div style="color:#6b7280;font-size:12px;margin-top:6px;">
          Profit: <b style="color:#16a34a;">{{ number_format((float)($profitThisWeek ?? 0), 2) }}</b>
        </div>
      @endif
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Sales This Month</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($salesThisMonth ?? 0), 2) }}
      </div>
      @if($canSeeCost)
        <div style="color:#6b7280;font-size:12px;margin-top:6px;">
          Profit: <b style="color:#16a34a;">{{ number_format((float)($profitThisMonth ?? 0), 2) }}</b>
        </div>
      @endif
    </div>

  </div>

  {{-- SECOND ROW --}}
  <div style="display:grid;grid-template-columns:1fr 0.5fr;gap:12px;align-items:start;">

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Recent Sales</h3>

      @if(($recentSales ?? collect())->count() === 0)
        <div style="color:#6b7280;">No sales yet.</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Bill</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Customer</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Phone</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Balance</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Open</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentSales as $s)
              <tr>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">#{{ $s->id }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $s->customer_name ?? '-' }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $s->customer_phone ?? '-' }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$s->total_amount,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$s->paid_amount,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;color:#b91c1c;font-weight:700;">{{ number_format((float)$s->balance_amount,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $s->status ?? '-' }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">
                  <a href="{{ route('mt.sales.show', $s) }}" style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">View</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Stock Alerts</h3>
      <div style="color:#6b7280;font-size:12px;">Low stock items</div>
      <div style="font-size:26px;font-weight:900;margin-top:8px;">
        {{ (int)($lowStockCount ?? 0) }}
      </div>
      <div style="margin-top:10px;">
        <a href="{{ route('mt.products.index') }}">Open Products →</a>
      </div>
    </div>

  </div>
@endsection
