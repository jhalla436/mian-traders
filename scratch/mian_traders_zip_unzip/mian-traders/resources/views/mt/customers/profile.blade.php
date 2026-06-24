@extends('mt.layouts.app')

@section('content')
@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Customer Profile</h2>
      <div style="color:#6b7280;font-size:12px;margin-top:4px;">
        <b>Name:</b> {{ $customerName }} |
        <b>Phone:</b> {{ $phone }}
        @if($lastPurchaseAt)
          | <b>Last Purchase:</b> {{ $lastPurchaseAt }}
        @endif
      </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('mt.udhar.show', $phone) }}"
         style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">
        Ledger (Udhar)
      </a>

      @if(!empty($waNumber))
        <a href="https://wa.me/{{ $waNumber }}"
           target="_blank"
           style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">
          WhatsApp
        </a>
      @endif

      <a href="{{ route('mt.whatsapp.index', ['type'=>'all','q'=>$phone]) }}"
         style="padding:8px 12px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;">
        WhatsApp List
      </a>

      <a href="{{ route('mt.udhar.index') }}"
         style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">
        Back
      </a>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start;">
    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Lifetime Totals</h3>

      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Total Sales (from start)</span>
        <b>{{ number_format((float)$totalSales, 2) }}</b>
      </div>
      @if($canSeeCost)

      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Total Profit (from start)</span>
        <b>{{ number_format((float)$totalProfit, 2) }}</b>
      </div>

      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Realized Profit</span>
        <b>{{ number_format((float)$profitRealized, 2) }}</b>
      </div>

      @endif
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
        <span>Current Udhar</span>
        <b style="color:#b91c1c;">{{ number_format((float)$totalUdhar, 2) }}</b>
      </div>

      <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">

      <h3 style="margin-top:0;">Outstanding Bills (Only Unpaid/Partial)</h3>

      @if($outstandingBills->count() === 0)
        <div style="color:#6b7280;">No outstanding bills.</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Bill</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Balance</th>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Open</th>
            </tr>
          </thead>
          <tbody>
            @foreach($outstandingBills as $b)
              <tr>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">#{{ $b->id }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $b->created_at }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$b->total_amount,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;color:#b91c1c;">{{ number_format((float)$b->balance_amount,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">
                  <a href="{{ route('mt.sales.show', $b->id) }}">Open</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    <div style="background:#fff;padding:14px;border-radius:10px;">
      <h3 style="margin-top:0;">Top Purchased Products</h3>

      @if($topProducts->count() === 0)
        <div style="color:#6b7280;">No product history found.</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topProducts as $p)
              <tr>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $p->product_name }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$p->total_qty, 2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$p->total_amount, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      <hr style="margin:14px 0;border:0;border-top:1px solid #eee;">
      @if($canSeeCost)

      <h3 style="margin-top:0;">Profit By Month</h3>

      @if($profitByMonth->count() === 0)
        <div style="color:#6b7280;">No monthly data.</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Month</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Sales</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Profit</th>
              <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Realized</th>
            </tr>
          </thead>
          <tbody>
            @foreach($profitByMonth as $m)
              <tr>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $m->ym }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$m->total_sales,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$m->total_profit,2) }}</td>
                <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$m->realized_profit,2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
      @endif

    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-top:12px;">
    <h3 style="margin-top:0;">Recent Bills (Last 15)</h3>

    @if($recentBills->count() === 0)
      <div style="color:#6b7280;">No bills found.</div>
    @else
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Bill</th>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Balance</th>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Open</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentBills as $b)
            <tr>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;">#{{ $b->id }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $b->created_at }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$b->total_amount,2) }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$b->paid_amount,2) }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;text-align:right;">{{ number_format((float)$b->balance_amount,2) }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;">{{ $b->status }}</td>
              <td style="border-bottom:1px solid #f3f4f6;padding:8px;">
                <a href="{{ route('mt.sales.show', $b->id) }}">Open</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection
