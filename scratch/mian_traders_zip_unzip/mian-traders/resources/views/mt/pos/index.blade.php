@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
  $g = $g ?? '';
  $c = $c ?? '';

  // ✅ groups (add leftover tab)
  $groups = $groups ?? [
    '' => 'All',
    'foam' => 'Foam',
    'hardware' => 'Hardware',
    'fabric' => 'Fabric',
    'spring' => 'Spring',
    'accessories' => 'Accessories',
    'other' => 'Other',
    'leftover' => 'Leftover',
  ];

  $tabStyle = function($active) {
    return $active
      ? 'background:#111827;color:#fff;'
      : 'background:#fff;color:#111827;border:1px solid #e5e7eb;';
  };

  $canSeeCost = \App\Support\Authz::canSeeCost();

  // ✅ Decide if product is "sheet/slab" candidate
  // We show cut/full buttons if:
  // 1) product explicitly has full-sheet dimensions, OR
  // 2) name/category looks like a sheet/slab material
  $isSheetCandidate = function($p) {
    $w = (float)($p->sheet_full_w ?? 0);
    $l = (float)($p->sheet_full_l ?? 0);
    if ($w > 0 && $l > 0) return true;
    $n = strtolower((string)$p->name);
    $cat = strtolower((string)($p->category?->name ?? ''));
    return str_contains($n,'sheet') || str_contains($cat,'sheet') ||
           str_contains($n,'slab')  || str_contains($cat,'slab')  ||
           str_contains($n,'lamination') || str_contains($n,'chipboard') ||
           str_contains($n,'shesham') || str_contains($n,'lasani') || str_contains($n,'commercial');
  };

  /**
   * ✅ Full sheet size rules:
   * - Hardware sheets: 8×4
   * - Foam sheets: 6×3
   *
   * IMPORTANT:
   * Here we treat the first number as "cut side" (W in controller algorithm),
   * and second number as constant length (L).
   *
   * Hardware cut: 1×4, 2×4 ... leftover becomes (8-cut)×4
   * Foam cut: 1×3, 2×3 ... leftover becomes (6-cut)×3
   */
  $sheetDefaults = function($p) use ($g, $isSheetCandidate) {
    $w = (float)($p->sheet_full_w ?? 0);
    $l = (float)($p->sheet_full_l ?? 0);

    // If stored on product, accept either order (4×8 or 8×4)
    // We always treat the *bigger* side as the cut-side (8), and the smaller as constant (4).
    if ($w > 0 && $l > 0) {
      $a = max($w, $l);
      $b = min($w, $l);
      return [$a, $b];
    }

    // Only fall back to group defaults if it's actually a sheet/slab-like item
    if ($isSheetCandidate($p)) {
      if ($g === 'hardware') return [8.0, 4.0];
      if ($g === 'foam') return [6.0, 3.0];
    }

    return [0.0, 0.0];
  };

  // ✅ Cut options:
  $cutOptionsForGroup = function($g) {
    // ✅ Your requirement:
    // Hardware: 8×4 full, cut options: 1×4, 2×4, 3×4
    // Foam: 6×3 full, cut options: 1×3..5×3
    if ($g === 'hardware') return [1,2,3]; // ×4 fixed
    if ($g === 'foam') return [1,2,3,4,5]; // ×3 fixed
    return [];
  };
@endphp

<div style="display:flex;gap:12px;align-items:flex-start;">

  {{-- LEFT --}}
  <div style="flex:1;background:#fff;border-radius:10px;padding:12px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <h2 style="margin:0;">POS</h2>
        @if($canSeeCost)
          <a href="{{ route('mt.purchases.create') }}"
             style="padding:8px 10px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;font-weight:800;">
            + Stock In
          </a>
        @endif
      </div>

      <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <input type="hidden" name="g" value="{{ $g }}">
        <input type="hidden" name="c" value="{{ $c }}">
        <input id="posSearch" list="posSuggestList" name="q" value="{{ $q }}" placeholder="Search product..."
               style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <datalist id="posSuggestList"></datalist>
        <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer;">
          Search
        </button>
      </form>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
      @foreach($groups as $key => $label)
        <a href="{{ route('mt.pos.index', ['g'=>$key]) }}"
           style="padding:8px 12px;border-radius:10px;text-decoration:none;{{ $tabStyle($g===$key) }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Companies dropdown (not for leftover) --}}
    @if($g !== '' && $g !== 'leftover')
      <div style="margin-top:10px;background:#f9fafb;border:1px solid #eee;border-radius:12px;padding:10px;">
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
          <div style="font-weight:800;">{{ strtoupper($g) }} Company:</div>

          <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <input type="hidden" name="g" value="{{ $g }}">
            @if($q !== '') <input type="hidden" name="q" value="{{ $q }}"> @endif

            <select name="c" onchange="this.form.submit()"
                    style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:240px;">
              <option value="">All {{ strtoupper($g) }} Companies</option>
              @foreach(($companies ?? []) as $co)
                <option value="{{ $co->id }}" @selected((string)$c === (string)$co->id)>{{ $co->name }}</option>
              @endforeach
            </select>

            @if($c !== '')
              <a href="{{ route('mt.pos.index', ['g'=>$g,'q'=>$q]) }}"
                 style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">
                Clear
              </a>
            @endif
          </form>
        </div>
      </div>
    @endif

    {{-- ✅ LEFTOVER TAB --}}
    @if($g === 'leftover')
      <div style="margin-top:12px;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="display:grid;grid-template-columns:1.4fr 0.6fr 1.2fr;gap:0;border-bottom:1px solid #eee;background:#f9fafb;padding:10px;font-weight:800;">
          <div>Leftover Item</div>
          <div style="text-align:right;">Size</div>
          <div style="text-align:right;">Action</div>
        </div>

        @forelse(($leftovers ?? []) as $lf)
          @php
            $p = $lf->product;
            $w = (float)$lf->width_ft;
            $l = (float)$lf->length_ft;

            // decide if this leftover is hardware-like (length ~4) or foam-like (length ~3)
            $isHardwareLeft = abs($l - 4.0) < 0.01;
            $isFoamLeft     = abs($l - 3.0) < 0.01;

            $cutList = [];
            if ($isHardwareLeft) {
              foreach ([1,2,3] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw;
            } elseif ($isFoamLeft) {
              foreach ([1,2,3,4,5] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw;
            } else {
              // fallback: allow 1..floor(width)
              for ($cw=1; $cw<=floor($w); $cw++) $cutList[] = $cw;
            }
          @endphp

          <div style="display:grid;grid-template-columns:1.4fr 0.6fr 1.2fr;gap:0;padding:10px;border-bottom:1px solid #f3f4f6;align-items:center;">
            <div>
              <div style="font-weight:900;">{{ $p?->name ?? 'Unknown' }}</div>
              <div style="font-size:11px;color:#6b7280;">
                {{ $p?->company?->name ?? '-' }} • {{ $p?->category?->name ?? '-' }}
              </div>
            </div>

            <div style="text-align:right;font-weight:800;">
              {{ number_format($w,2) }}×{{ number_format($l,2) }}
            </div>

            <div style="text-align:right;display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">

              {{-- Add Full leftover --}}
              <form method="POST" action="{{ route('mt.pos.leftover_add_full') }}">
                @csrf
                <input type="hidden" name="leftover_id" value="{{ $lf->id }}">
                <button style="padding:8px 10px;border:0;border-radius:10px;background:#16a34a;color:#fff;cursor:pointer;">
                  Add Full
                </button>
              </form>

              {{-- Add Cut from leftover --}}
              <form method="POST" action="{{ route('mt.pos.leftover_add_cut') }}" style="display:flex;gap:6px;align-items:center;">
                @csrf
                <input type="hidden" name="leftover_id" value="{{ $lf->id }}">

                <select name="cut_w_ft" style="padding:8px;border:1px solid #ddd;border-radius:10px;">
                  @foreach($cutList as $cw)
                    <option value="{{ $cw }}">{{ $cw }}×{{ number_format($l,0) }}</option>
                  @endforeach
                </select>

                <button style="padding:8px 10px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer;">
                  Add Cut
                </button>
              </form>

            </div>
          </div>
        @empty
          <div style="padding:12px;color:#6b7280;">No leftovers yet.</div>
        @endforelse
      </div>

    @else
      {{-- ✅ PRODUCTS LIST --}}
      <div style="margin-top:12px;border:1px solid #eee;border-radius:10px;overflow:hidden;">
        <div style="display:grid;grid-template-columns:1.6fr 0.6fr 1.1fr;gap:0;border-bottom:1px solid #eee;background:#f9fafb;padding:10px;font-weight:800;">
          <div>Product</div>
          <div style="text-align:right;">Sell</div>
          <div style="text-align:right;">Action</div>
        </div>

        @foreach(($products ?? []) as $p)
          @php
            $sell = (float)($p->selling_price_default ?? $p->mrp ?? 0);
            $sheet = $isSheetCandidate($p);
            [$fullW,$fullL] = $sheetDefaults($p);

            $showSheetButtons = $sheet && $fullW > 0 && $fullL > 0 && in_array($g, ['hardware','foam'], true);

            // cut options based on group
            $cutWidths = $showSheetButtons ? $cutOptionsForGroup($g) : [];
          @endphp

          <div style="display:grid;grid-template-columns:1.6fr 0.6fr 1.1fr;gap:0;padding:10px;border-bottom:1px solid #f3f4f6;align-items:center;">
            <div>
              <div style="font-weight:900;">{{ $p->name }}</div>
              <div style="font-size:11px;color:#6b7280;">
                {{ $p->company?->name ?? '-' }} • {{ $p->category?->name ?? '-' }}
                @if($showSheetButtons)
                  • Sheet: {{ $fullW }}×{{ $fullL }}
                @endif
              </div>
            </div>

            <div style="text-align:right;">
              {{ number_format($sell, 2) }}
            </div>

            <div style="text-align:right;display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
              {{-- Normal Add --}}
              @if(!$showSheetButtons)
                <form method="POST" action="{{ route('mt.pos.add', ['g'=>$g, 'q'=>$q, 'c'=>$c]) }}">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <button style="padding:8px 10px;border:0;border-radius:10px;background:#16a34a;color:#fff;cursor:pointer;">
                    Add
                  </button>
                </form>
              @else
                {{-- ✅ Add Full via add_cut (cut = full, so it opens sheets and makes no leftover) --}}
                <form method="POST" action="{{ route('mt.pos.add_cut', ['g'=>$g, 'q'=>$q, 'c'=>$c]) }}">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <input type="hidden" name="full_w_ft" value="{{ $fullW }}">
                  <input type="hidden" name="full_l_ft" value="{{ $fullL }}">
                  <input type="hidden" name="cut_w_ft" value="{{ $fullW }}">
                  <input type="hidden" name="cut_l_ft" value="{{ $fullL }}">
                  <button style="padding:8px 10px;border:0;border-radius:10px;background:#16a34a;color:#fff;cursor:pointer;">
                    Add Full
                  </button>
                </form>

                {{-- ✅ Add Cut --}}
                <form method="POST" action="{{ route('mt.pos.add_cut', ['g'=>$g, 'q'=>$q, 'c'=>$c]) }}" style="display:flex;gap:6px;align-items:center;">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <input type="hidden" name="full_w_ft" value="{{ $fullW }}">
                  <input type="hidden" name="full_l_ft" value="{{ $fullL }}">
                  <input type="hidden" name="cut_l_ft" value="{{ $fullL }}">

                  <select name="cut_w_ft" style="padding:8px;border:1px solid #ddd;border-radius:10px;">
                    @foreach($cutWidths as $cw)
                      <option value="{{ $cw }}">{{ $cw }}×{{ $fullL }}</option>
                    @endforeach
                  </select>

                  <button style="padding:8px 10px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer;">
                    Add Cut
                  </button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>

  {{-- RIGHT: Cart --}}
  <div style="width:320px;background:#fff;border-radius:10px;padding:12px;position:sticky;top:10px;max-height:85vh;overflow:auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;">
      <h3 style="margin:0;">Cart</h3>
      <form method="POST" action="{{ route('mt.pos.clear', ['g'=>$g,'q'=>$q,'c'=>$c]) }}">
        @csrf
        <button style="padding:8px 10px;border:0;border-radius:10px;background:#b91c1c;color:#fff;cursor:pointer;">
          Clear
        </button>
      </form>
    </div>

    <hr style="margin:12px 0;border:0;border-top:1px solid #eee;">
    @forelse(($cartRows ?? []) as $r)
      <div style="border:1px solid #eee;border-radius:10px;padding:10px;margin-bottom:10px;">
        <div style="font-weight:900;">{{ $r['name'] }}</div>

        <div style="margin-top:8px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
          <div>
            <div style="font-size:11px;color:#6b7280;">Qty</div>
            <input
              class="js-cart-qty"
              data-row="{{ $r['row_id'] }}"
              value="{{ $r['qty'] }}"
              type="number"
              step="1"
              min="1"
              style="width:100%;padding:8px;border:1px solid #ddd;border-radius:10px;">
          </div>

          <div>
            <div style="font-size:11px;color:#6b7280;">Sell Price</div>
            <input
              class="js-cart-price"
              data-row="{{ $r['row_id'] }}"
              value="{{ number_format((float)$r['price'],2,'.','') }}"
              type="number"
              step="0.01"
              min="0"
              style="width:100%;padding:8px;border:1px solid #ddd;border-radius:10px;">
          </div>
        </div>

        <div style="margin-top:8px;display:flex;justify-content:flex-start;align-items:center;">
          <form method="POST" action="{{ route('mt.pos.remove', ['g'=>$g,'q'=>$q,'c'=>$c]) }}">
            @csrf
            <input type="hidden" name="row_id" value="{{ $r['row_id'] }}">
            <button style="padding:8px 10px;border:0;border-radius:10px;background:#374151;color:#fff;cursor:pointer;">
              Remove
            </button>
          </form>
        </div>

        @if($canSeeCost)
          <div style="margin-top:6px;font-size:11px;color:#6b7280;">
            Unit Cost: {{ number_format((float)($r['purchase_effective'] ?? $r['purchase_price']),2) }} |
            Line Profit: {{ number_format((float)$r['line_profit'],2) }}
          </div>
        @endif
      </div>
    @empty
      <div style="color:#6b7280;">Cart is empty.</div>
    @endforelse

    <hr style="margin:12px 0;border:0;border-top:1px solid #eee;">

    {{-- Checkout --}}
    <h3 style="margin:0 0 10px;">Checkout</h3>
    <form method="POST" action="{{ route('mt.pos.checkout') }}" style="display:grid;gap:10px;">
      @csrf

      <input name="customer_name" placeholder="Customer Name"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="customer_phone" placeholder="Contact #1 (Primary)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="customer_phone2" placeholder="Contact #2 (Optional)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="customer_phone3" placeholder="Contact #3 (Optional)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="customer_address" placeholder="Address"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="customer_cnic" placeholder="CNIC (Optional)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="received_cash" type="number" step="0.01" min="0" placeholder="Cash Received (0 = udhar)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <input name="note" placeholder="Note (optional)"
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">

      <button type="submit"
              style="padding:12px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
        Complete Sale
      </button>
    </form>


  </div>

</div>


{{-- Floating Cart Total (bottom-right of screen) --}}
<div style="position:fixed;right:18px;bottom:18px;z-index:9999;background:#111827;color:#fff;border-radius:14px;padding:12px 14px;min-width:170px;box-shadow:0 10px 30px rgba(0,0,0,.18);">
  <div style="font-size:12px;opacity:.85;">Cart Total</div>
  <div id="cartTotal" style="font-size:26px;font-weight:900;line-height:1.1;">{{ number_format((float)($cartTotal ?? 0),2) }}</div>
  @if($canSeeCost)
    <div style="margin-top:4px;font-size:12px;opacity:.85;">
      Profit: <b id="cartProfit">{{ number_format((float)($cartProfit ?? 0),2) }}</b>
    </div>
  @endif
</div>

<script>
(function(){
  // POS typeahead
  const suggestUrl = @json(route('mt.pos.suggest'));
  const g = @json($g);
  const c = @json($c);
  const qInput = document.getElementById('posSearch');
  const dl = document.getElementById('posSuggestList');
  let suggestT = null;

  function fillSuggest(items){
    if(!dl) return;
    dl.innerHTML = '';
    (items || []).forEach(it => {
      const opt = document.createElement('option');
      const meta = [it.company, it.category].filter(Boolean).join(' • ');
      opt.value = it.name;
      opt.label = meta ? (it.name + ' — ' + meta) : it.name;
      dl.appendChild(opt);
    });
  }

  if (qInput && dl) {
    qInput.addEventListener('input', function(){
      const val = (qInput.value || '').trim();
      if (val.length < 1) { fillSuggest([]); return; }
      clearTimeout(suggestT);
      suggestT = setTimeout(async () => {
        try {
          const u = new URL(suggestUrl, window.location.origin);
          u.searchParams.set('q', val);
          if (g) u.searchParams.set('g', g);
          if (c) u.searchParams.set('c', c);
          const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          fillSuggest(data.items || []);
        } catch(e) {
          // ignore
        }
      }, 200);
    });
  }

  const token = @json(csrf_token());
  const url = @json(route('mt.pos.update_item'));

  let t = null;
  function debounce(fn){
    if (t) clearTimeout(t);
    t = setTimeout(fn, 250);
  }

  function postUpdate(rowId, qty, price){
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ row_id: rowId, qty: qty, price: price })
    })
    .then(r => r.json())
    .then(data => {
      if (!data || !data.ok) return;
      if (document.getElementById('cartTotal')) document.getElementById('cartTotal').innerText = Number(data.total).toFixed(2);
      if (document.getElementById('cartProfit')) document.getElementById('cartProfit').innerText = Number(data.profit).toFixed(2);
    })
    .catch(() => {});
  }

  document.querySelectorAll('.js-cart-qty').forEach(inp => {
    inp.addEventListener('input', function(){
      const rowId = this.dataset.row;
      const qty = this.value;
      const priceInp = document.querySelector('.js-cart-price[data-row="'+rowId+'"]');
      const price = priceInp ? priceInp.value : null;
      debounce(() => postUpdate(rowId, qty, price));
    });
  });

  document.querySelectorAll('.js-cart-price').forEach(inp => {
    inp.addEventListener('input', function(){
      const rowId = this.dataset.row;
      const price = this.value;
      const qtyInp = document.querySelector('.js-cart-qty[data-row="'+rowId+'"]');
      const qty = qtyInp ? qtyInp.value : null;
      debounce(() => postUpdate(rowId, qty, price));
    });
  });

})();
</script>
@endsection
