@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Sales</h2>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('mt.pos.index') }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Go POS</a>
      <a href="{{ route('mt.udhar.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Udhar List</a>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Sale #</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Customer</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Phone</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Total</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Balance</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $s)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">#{{ $s->id }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $s->customer_name ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              @php $wa = \App\Support\WhatsApp::url($s->customer_phone); @endphp
              <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <span>{{ $s->customer_phone ?? '-' }}</span>
                @if($wa)
                  <a href="{{ $wa }}" target="_blank" rel="noopener"
                     style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
                @endif
              </div>
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$s->total_amount,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">{{ number_format((float)$s->paid_amount,2) }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:700;">
              {{ number_format((float)$s->balance_amount,2) }}
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              {{ $s->status ?? '-' }} / {{ $s->sale_type ?? '-' }}
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;white-space:nowrap;">
              <a href="{{ route('mt.sales.show', $s) }}"
                 style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">
                View
              </a>

              @if($s->customer_phone)
                <a href="{{ route('mt.customers.profile', $s->customer_phone) }}"
                   style="padding:6px 10px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;margin-left:6px;">
                  Profile
                </a>
              @endif

              @if($s->customer_phone && (float)$s->balance_amount > 0)
                <a href="{{ route('mt.udhar.show', $s->customer_phone) }}"
                   style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;margin-left:6px;">
                  Udhar
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="padding:12px;">No sales found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $sales->links() }}
    </div>
  </div>
@endsection
