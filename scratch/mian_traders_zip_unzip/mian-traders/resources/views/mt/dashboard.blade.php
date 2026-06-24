@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
  <h2 style="margin:0;">Dashboard</h2>
  <div style="color:#6b7280;font-size:12px;">
    Logged in as: <b>{{ auth()->user()->role ?? '-' }}</b>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:12px;align-items:stretch;">

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
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="color:#6b7280;font-size:12px;">Sales This Week</div>
    <div style="font-size:22px;font-weight:800;margin-top:6px;">
      {{ number_format((float)($salesThisWeek ?? 0), 2) }}
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="color:#6b7280;font-size:12px;">Sales This Month</div>
    <div style="font-size:22px;font-weight:800;margin-top:6px;">
      {{ number_format((float)($salesThisMonth ?? 0), 2) }}
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="color:#6b7280;font-size:12px;">Low Stock Items</div>
    <div style="font-size:22px;font-weight:800;margin-top:6px;">
      {{ (int)($lowStockCount ?? 0) }}
    </div>
    <div style="margin-top:10px;">
      <a href="{{ route('mt.products.index') }}">Open Products →</a>
    </div>
  </div>

  {{-- ✅ Profit cards hidden for cashier --}}
  @if($canSeeCost)
    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Profit Today</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($profitToday ?? 0), 2) }}
      </div>
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Profit This Week</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($profitThisWeek ?? 0), 2) }}
      </div>
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <div style="color:#6b7280;font-size:12px;">Profit This Month</div>
      <div style="font-size:22px;font-weight:800;margin-top:6px;">
        {{ number_format((float)($profitThisMonth ?? 0), 2) }}
      </div>
    </div>
  @endif

</div>
@endsection
