@extends('mt.layouts.app')

@section('content')
@php
  $isType = function(string $t) use ($type) { return $type === $t; };
@endphp

<div class="page-header">
  <div>
    <h2 class="page-title">WhatsApp Numbers</h2>
    <div class="page-subtitle">
      Use this to copy/export customer phone numbers for WhatsApp groups.
      Total matching: <b>{{ (int)($totalCount ?? 0) }}</b>
    </div>
  </div>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center flex-wrap">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Search phone/name..." class="mt-input" style="min-width:200px;">
      <input type="hidden" name="type" value="{{ $type }}">
      <button class="btn btn-primary btn-sm">Search</button>
      <a href="{{ route('mt.whatsapp.index', ['type'=>$type]) }}" class="btn btn-outline btn-sm">Clear</a>
      <a href="{{ route('mt.whatsapp.export', ['type'=>$type, 'q'=>$q]) }}" class="btn btn-secondary btn-sm">Download CSV</a>
    </form>
  </div>
</div>

<div class="filter-bar" style="justify-content:space-between;">
  <div class="mt-tabs">
    <a href="{{ route('mt.whatsapp.index', ['type'=>'udhar', 'q'=>$q]) }}" class="mt-tab{{ $isType('udhar') ? ' active' : '' }}">Udhar Customers</a>
    <a href="{{ route('mt.whatsapp.index', ['type'=>'walkin', 'q'=>$q]) }}" class="mt-tab{{ $isType('walkin') ? ' active' : '' }}">Walk-in / Paid</a>
    <a href="{{ route('mt.whatsapp.index', ['type'=>'all', 'q'=>$q]) }}" class="mt-tab{{ $isType('all') ? ' active' : '' }}">All</a>
  </div>
  <span class="text-xs text-muted">Tip: WhatsApp format is digits only (92xxxxxxxxxx).</span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">
  <div class="card">
    <div class="flex justify-between items-center">
      <h3 style="margin:0;font-size:16px;font-weight:800;">WhatsApp Format (92...)</h3>
      <button type="button" onclick="copyText('waBox')" class="btn btn-success btn-sm">Copy</button>
    </div>
    <textarea id="waBox" rows="10" class="mt-input" style="margin-top:12px;resize:vertical;">{{ $numbersWa ?? '' }}</textarea>
    <div class="text-xs text-muted" style="margin-top:6px;">Paste in WhatsApp "Invite via link" or your phone contacts list.</div>
  </div>

  <div class="card">
    <div class="flex justify-between items-center">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Original Numbers</h3>
      <button type="button" onclick="copyText('origBox')" class="btn btn-primary btn-sm">Copy</button>
    </div>
    <textarea id="origBox" rows="10" class="mt-input" style="margin-top:12px;resize:vertical;">{{ $numbersOriginal ?? '' }}</textarea>
    <div class="text-xs text-muted" style="margin-top:6px;">These are the exact numbers saved in invoices.</div>
  </div>
</div>

<div class="table-card" style="margin-top:16px;">
  <div style="padding:16px 20px 0;"><h3 style="margin:0;font-size:16px;font-weight:800;">Customers</h3></div>
  <table class="mt-table" style="margin-top:12px;">
    <thead>
      <tr>
        <th>Name</th>
        <th>Phones</th>
        <th class="text-right">Total Sales</th>
        <th class="text-right">Current Udhar</th>
        <th>Last Purchase</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
        @php
          $p1 = trim((string)($r->customer_phone ?? ''));
          $p2 = trim((string)($r->customer_phone2 ?? ''));
          $p3 = trim((string)($r->customer_phone3 ?? ''));
        @endphp
        <tr>
          <td class="font-bold">{{ $r->customer_name ?? '-' }}</td>
          <td>
            @foreach([$p1,$p2,$p3] as $pp)
              @if(!empty($pp))
                @php $wa = \App\Support\WhatsApp::url($pp); @endphp
                <div class="flex gap-8 items-center" style="margin-bottom:4px;">
                  <span>{{ $pp }}</span>
                  @if($wa)
                    <a href="{{ $wa }}" target="_blank" class="badge-wa">WA</a>
                  @endif
                </div>
              @endif
            @endforeach
            @if(empty($p1) && empty($p2) && empty($p3)) - @endif
          </td>
          <td class="text-right">{{ number_format((float)($r->total_sales ?? 0), 2) }}</td>
          <td class="text-right">
            <span class="font-bold {{ (float)($r->total_udhar ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
              {{ number_format((float)($r->total_udhar ?? 0), 2) }}
            </span>
          </td>
          <td>{{ $r->last_purchase ?? '-' }}</td>
          <td>
            <div class="actions">
              @if(!empty($p1))
                <a href="{{ route('mt.customers.profile', $p1) }}" class="btn btn-primary btn-xs">Profile</a>
              @endif
              @if(!empty($p1) && (float)($r->total_udhar ?? 0) > 0)
                <a href="{{ route('mt.udhar.show', $p1) }}" class="btn btn-secondary btn-xs">Udhar</a>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr class="empty-row"><td colspan="6">No results.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination-wrap">{{ $rows->links() }}</div>
</div>

<script>
  function copyText(id){
    const el = document.getElementById(id);
    if (!el) return;
    const text = el.value || '';
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(() => toast('Copied!')).catch(() => fallbackCopy(el));
      return;
    }
    fallbackCopy(el);
  }
  function fallbackCopy(el){ el.focus(); el.select(); try { document.execCommand('copy'); toast('Copied!'); } catch(e) { toast('Copy failed.'); } window.getSelection().removeAllRanges(); }
  function toast(msg){
    const t = document.createElement('div');
    t.innerText = msg;
    Object.assign(t.style, { position:'fixed', bottom:'20px', right:'20px', background:'linear-gradient(135deg,#6366f1,#4f46e5)', color:'#fff', padding:'10px 16px', borderRadius:'12px', fontSize:'13px', fontWeight:'600', zIndex:999999, boxShadow:'0 8px 24px rgba(0,0,0,0.2)', animation:'slideDown 0.3s ease' });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 1500);
  }
</script>
@endsection
