@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Company Orders</h2>
    <a href="{{ route('mt.company_orders.create') }}" style="padding:10px 12px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;font-weight:800;">+ New Order</a>
  </div>

  <form method="GET" style="background:#fff;padding:12px;border-radius:10px;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
    <div>
      <div style="font-size:12px;color:#6b7280;">Company</div>
      <select name="company_id" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:240px;">
        <option value="">-- All --</option>
        @foreach($companies as $c)
          <option value="{{ $c->id }}" @selected((string)$companyId === (string)$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <div style="font-size:12px;color:#6b7280;">Status</div>
      <select name="status" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:180px;">
        <option value="">-- All --</option>
        <option value="open" @selected($status==='open')>Open</option>
        <option value="received" @selected($status==='received')>Received</option>
        <option value="cancelled" @selected($status==='cancelled')>Cancelled</option>
      </select>
    </div>

    <button style="padding:10px 12px;border-radius:10px;background:#2563eb;color:#fff;border:0;font-weight:800;">Filter</button>
  </form>

  <div style="background:#fff;padding:0;border-radius:10px;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Order</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Company</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Date</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Status</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Goods Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
              <a href="{{ route('mt.company_orders.show', $r) }}" style="text-decoration:none;font-weight:800;">#{{ $r->id }}</a>
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ $r->company?->name }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ optional($r->order_date)->format('Y-m-d') }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">{{ strtoupper($r->status) }}</td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:800;">{{ number_format((float)$r->goods_total, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="padding:12px;">No orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">{{ $rows->links() }}</div>
@endsection
