@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div class="page-header">
  <h2 class="page-title">Udhar Customers</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Search phone/name..." class="mt-input" style="width:220px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th>Phone</th>
        <th>Name</th>
        <th class="text-right">Total Sales</th>
        @if($canSeeCost)
          <th class="text-right">Total Profit</th>
          <th class="text-right">Realized Profit</th>
        @endif
        <th class="text-right">Current Udhar</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          <td>
            @php $wa = \App\Support\WhatsApp::url($r->customer_phone); @endphp
            <div class="flex gap-8 items-center flex-wrap">
              <span>{{ $r->customer_phone }}</span>
              @if($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="badge-wa">WA</a>
              @endif
            </div>
          </td>
          <td>{{ $r->customer_name ?? '-' }}</td>
          <td class="text-right">{{ number_format((float)$r->total_sales,2) }}</td>
          @if($canSeeCost)
            <td class="text-right">{{ number_format((float)$r->total_profit,2) }}</td>
            <td class="text-right">{{ number_format((float)$r->total_profit_realized,2) }}</td>
          @endif
          <td class="text-right text-danger font-bold">{{ number_format((float)$r->total_udhar,2) }}</td>
          <td>
            <a href="{{ route('mt.udhar.show', $r->customer_phone) }}" class="btn btn-primary btn-xs">Open</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination-wrap">{{ $rows->links() }}</div>
</div>
@endsection
