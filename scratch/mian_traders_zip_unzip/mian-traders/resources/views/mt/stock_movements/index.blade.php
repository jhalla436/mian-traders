@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Stock Movements</h2>
      <div style="color:#6b7280;font-size:12px;margin-top:4px;">Manual stock in/out/adjust history.</div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('mt.stock_movements.create') }}"
         style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">Add Movement</a>

      <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search product/note..."
               style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer;">Search</button>
      </form>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">#</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Type</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Note</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->id }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->product?->name ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              @php
                $t = $r->type;
                $label = strtoupper($t);
                $bg = $t === 'in' ? '#dcfce7' : ($t === 'out' ? '#fee2e2' : '#e0f2fe');
              @endphp
              <span style="padding:4px 8px;border-radius:999px;background:{{ $bg }};">
                {{ $label }}
              </span>
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">
              {{ number_format((float)$r->qty,2) }}
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->note ?? '-' }}</td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $r->created_at }}</td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:12px;">No movements yet.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $rows->links() }}
    </div>
  </div>
@endsection
