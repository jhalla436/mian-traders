@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
  $g = $g ?? '';
  $c = $c ?? '';

  $groups = $groups ?? [
    '' => 'All', 'foam' => 'Foam', 'uncovered_foam' => 'Uncovered Foam', 'hardware' => 'Hardware', 'fabric' => 'Fabric',
    'spring' => 'Spring', 'accessories' => 'Accessories', 'other' => 'Other', 'leftover' => 'Leftover',
  ];

  $canSeeCost = \App\Support\Authz::canSeeCost();

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

  $sheetDefaults = function($p) use ($g, $isSheetCandidate) {
    $w = (float)($p->sheet_full_w ?? 0);
    $l = (float)($p->sheet_full_l ?? 0);
    if ($w > 0 && $l > 0) { $a = max($w, $l); $b = min($w, $l); return [$a, $b]; }
    if ($isSheetCandidate($p)) {
      if ($g === 'hardware') return [8.0, 4.0];
      if ($g === 'foam') return [6.0, 3.0];
    }
    return [0.0, 0.0];
  };

  $cutOptionsForGroup = function($g) {
    if ($g === 'hardware') return [1,2,3];
    if ($g === 'foam') return [1,2,3,4,5];
    return [];
  };
@endphp

@if($needsShopSelection ?? false)
  <div class="card" style="max-width:600px;margin:40px auto;text-align:center;padding:40px 30px;">
    <div style="margin-bottom:16px;">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto;">
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/>
        <line x1="12" y1="17" x2="12.01" y2="17"/>
      </svg>
    </div>
    <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px;">Select a Shop</h2>
    <p style="color:#64748b;font-size:14px;line-height:1.6;margin-bottom:20px;">
      POS requires a specific shop to be selected.<br>
      Please choose a shop from the <b>sidebar</b> dropdown (not "All Shops") to start selling.
    </p>
    <div style="display:inline-flex;align-items:center;gap:8px;background:#fffbeb;border:1px solid #fde68a;padding:10px 18px;border-radius:10px;color:#92400e;font-size:13px;font-weight:600;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
      Use the shop selector in the sidebar
    </div>
  </div>
@else

<div class="pos-layout">
  <div id="posNotificationContainer" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

  {{-- Categories --}}
  <div class="pos-sidebar">
    <div style="border:1px solid #ccc;padding:8px;background:#fff;">
      <strong style="font-size:11px;text-transform:uppercase;color:#555;">Categories</strong>
      <ul style="list-style:none;padding-left:0;margin-top:6px;" id="sidebarCategories">
        @foreach($groups as $key => $label)
          <li>
            <a href="{{ route('mt.pos.index', ['g'=>$key]) }}"
               data-group="{{ $key }}"
               style="text-decoration:none;display:block;padding:5px 8px;margin-bottom:1px;font-size:12px;{{ $g===$key ? 'background:#333;color:#fff;font-weight:700;' : 'color:#333;' }}"
               class="js-pos-tab">
              {{ $label }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

  {{-- Companies --}}
  <div class="pos-sidebar">
    <div style="border:1px solid #ccc;padding:8px;background:#fff;">
      <strong style="font-size:11px;text-transform:uppercase;color:#555;">Companies</strong>
      <div id="sidebarCompaniesContainer">
        @if($g === 'leftover')
          <div style="color:#999;font-size:11px;margin-top:6px;">Select a category first.</div>
        @else
          <ul style="list-style:none;padding-left:0;margin-top:6px;max-height:600px;overflow:auto;" id="sidebarCompanies">
            <li>
              <a href="{{ route('mt.pos.index', ['g'=>$g]) }}"
                 data-company=""
                 style="text-decoration:none;display:block;padding:5px 8px;margin-bottom:1px;font-size:12px;{{ $c === '' ? 'background:#333;color:#fff;font-weight:700;' : 'color:#333;' }}"
                 class="js-pos-company">
                All Companies
              </a>
            </li>
            @foreach(($companies ?? []) as $co)
              <li>
                <a href="{{ route('mt.pos.index', ['g'=>$g,'c'=>$co->id]) }}"
                   data-company="{{ $co->id }}"
                   style="text-decoration:none;display:block;padding:5px 8px;margin-bottom:1px;font-size:12px;{{ (string)$c === (string)$co->id ? 'background:#333;color:#fff;font-weight:700;' : 'color:#333;' }}"
                   class="js-pos-company">
                  {{ $co->name }}
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

  {{-- LEFT: Products --}}
  <div class="pos-products">
    <div class="pos-products-inner">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
        <div style="display:flex;gap:8px;align-items:center;">
          <h2 style="margin:0;font-size:16px;font-weight:700;color:#000;">POS</h2>
          @if($canSeeCost)
            <a href="{{ route('mt.purchases.create') }}" style="padding:3px 8px;background:#080;color:#fff;font-size:11px;font-weight:700;text-decoration:none;border:1px solid #060;">+ Stock In</a>
          @endif
        </div>
        <div style="display:flex;gap:4px;align-items:center;">
          <input id="posSearch" list="posSuggestList" placeholder="Search..." style="padding:4px 8px;border:1px solid #999;font-size:12px;min-width:180px;" value="{{ $q }}">
          <datalist id="posSuggestList"></datalist>
          <button id="posSearchBtn" type="button" style="padding:4px 10px;background:#333;color:#fff;border:1px solid #111;font-size:11px;font-weight:700;cursor:pointer;">Search</button>
          <div style="display:flex;gap:2px;">
            <button type="button" onclick="addQtyToRecent(1)" class="qty-quick-btn" title="Add ×1">1</button>
            <button type="button" onclick="addQtyToRecent(2)" class="qty-quick-btn" title="Add ×2">2</button>
            <button type="button" onclick="addQtyToRecent(3)" class="qty-quick-btn" title="Add ×3">3</button>
            <button type="button" onclick="addQtyToRecent(4)" class="qty-quick-btn" title="Add ×4">4</button>
            <button type="button" onclick="addQtyToRecent(5)" class="qty-quick-btn" title="Add ×5">5</button>
          </div>
        </div>
      </div>

      {{-- Tabs and Company filters removed as they are now in the parallel sidebars --}}
      <div id="companyFilterBar" style="display:none;">
          <select id="companyFilterSelect"></select>
          <a href="javascript:void(0)" id="companyFilterClear"></a>
          <span id="companyFilterLabel"></span>
      </div>

      {{-- Product list container (rendered dynamically via JS) --}}
      <div id="posProductListContainer" style="flex:1;min-height:0;display:flex;flex-direction:column;">
        {{-- LEFTOVER TAB --}}
        @if($g === 'leftover')
          <div class="pos-product-list">
            <div class="pos-product-header" style="grid-template-columns:1.4fr 0.6fr 1.2fr;">
              <div>Leftover Item</div>
              <div style="text-align:right;">Size</div>
              <div style="text-align:right;">Action</div>
            </div>

            @forelse(($leftovers ?? []) as $lf)
              @php
                $p = $lf->product;
                $w = (float)$lf->width_ft;
                $l = (float)$lf->length_ft;
                $isHardwareLeft = abs($l - 4.0) < 0.01;
                $isFoamLeft = abs($l - 3.0) < 0.01;
                $cutList = [];
                if ($isHardwareLeft) { foreach ([1,2,3] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw; }
                elseif ($isFoamLeft) { foreach ([1,2,3,4,5] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw; }
                else { for ($cw=1; $cw<=floor($w); $cw++) $cutList[] = $cw; }
              @endphp

              <div class="pos-product-row js-pos-product-row" data-product-id="{{ $p?->id ?? '' }}" style="grid-template-columns:1.4fr 0.6fr 1.2fr;">
                <div>
                  <div class="pos-product-name">{{ $p?->name ?? 'Unknown' }}</div>
                  <div class="pos-product-meta">{{ $p?->company?->name ?? '-' }} • {{ $p?->category?->name ?? '-' }}</div>
                </div>
                <div style="text-align:right;font-weight:700;">{{ number_format($w,2) }}×{{ number_format($l,2) }}</div>
                <div class="pos-product-actions">
                  <form method="POST" action="{{ route('mt.pos.leftover_add_full') }}" class="js-pos-ajax-form">
                    @csrf
                    <input type="hidden" name="leftover_id" value="{{ $lf->id }}">
                    <button type="submit" style="padding:3px 8px;background:#080;color:#fff;font-size:10px;font-weight:700;border:1px solid #060;cursor:pointer;">Add Full</button>
                  </form>
                  <form method="POST" action="{{ route('mt.pos.leftover_add_cut') }}" class="flex gap-8 items-center js-pos-ajax-form">
                    @csrf
                    <input type="hidden" name="leftover_id" value="{{ $lf->id }}">
                    <select name="cut_w_ft" style="padding:3px 6px;min-width:auto;font-size:11px;border:1px solid #999;">
                      @foreach($cutList as $cw)
                        <option value="{{ $cw }}">{{ $cw }}×{{ number_format($l,0) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" style="padding:3px 8px;background:#333;color:#fff;font-size:10px;font-weight:700;border:1px solid #111;cursor:pointer;">Add Cut</button>
                  </form>
                </div>
              </div>
            @empty
              <div style="padding:16px;color:#999;text-align:center;font-size:12px;">No leftovers yet.</div>
            @endforelse
          </div>

        @else
          {{-- PRODUCTS LIST --}}
          <div class="pos-product-list">
            <div class="pos-product-header">
              <div>Product</div>
              <div style="text-align:right;">Sell</div>
              <div style="text-align:right;">Action</div>
            </div>

            @foreach(($products ?? []) as $p)
              @php
                $sell = (float)($p->selling_price_default ?? $p->mrp ?? 0);
                $maxDiscountPercent = round(max(0, (float)($p->max_discount_percent ?? 0)), 2);
                $minSell = round(max($sell * (1 - ($maxDiscountPercent / 100)), 0), 2);
                $sheet = $isSheetCandidate($p);
                [$fullW,$fullL] = $sheetDefaults($p);
                $showSheetButtons = $sheet && $fullW > 0 && $fullL > 0 && in_array($g, ['hardware','foam'], true);
                $cutWidths = $showSheetButtons ? $cutOptionsForGroup($g) : [];
              @endphp

              <div class="pos-product-row js-pos-product-row" data-product-id="{{ $p->id }}">
                <div>
                  <div class="pos-product-name">
                    @php
                      $catLower = strtolower($p->category?->name ?? '');
                      $isSlab = str_contains($catLower, 'slab') || str_contains($catLower, 'sheet') || str_contains(strtolower($p->name), 'slab') || str_contains(strtolower($p->name), 'sheet');
                      if ($isSlab && $p->length_in > 0 && $p->width_in > 0 && $p->height_in > 0) {
                          $len = (float)$p->length_in;
                          $wid = (float)$p->width_in;
                          $hei = (float)$p->height_in;
                          $derivedFam = trim(preg_replace('/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*[x×*].*$/i', '', $p->name));
                          $derivedFam = trim(preg_replace('/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*$/i', '', $derivedFam));
                          echo $derivedFam . ' ' . $len . '-' . $wid . '-' . $hei;
                      } else {
                          echo $p->name;
                      }
                    @endphp
                  </div>
                  <div class="pos-product-meta">
                    {{ $p->company?->name ?? '-' }} • {{ $p->category?->name ?? '-' }}
                    @if($showSheetButtons) • Sheet: {{ $fullW }}×{{ $fullL }} @endif
                  </div>
                </div>
                <div class="pos-product-price">
                  <input
                    type="number"
                    step="0.1"
                    min="{{ number_format($minSell, 1, '.', '') }}"
                    value=""
                    placeholder="{{ number_format($sell, 1, '.', '') }}"
                    class="pos-product-sell-input js-product-sell"
                    data-default="{{ number_format($sell, 1, '.', '') }}"
                    data-min="{{ number_format($minSell, 1, '.', '') }}">
                  @if($maxDiscountPercent > 0)
                    <div class="pos-product-sell-note">Max {{ rtrim(rtrim(number_format($maxDiscountPercent, 2, '.', ''), '0'), '.') }}% off</div>
                  @endif
                </div>
                <div class="pos-product-actions">
                  @if(!$showSheetButtons)
                    <form method="POST" action="{{ route('mt.pos.add') }}" class="js-pos-price-form js-pos-ajax-form">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $p->id }}">
                      <input type="hidden" name="selling_price" value="{{ number_format($sell, 1, '.', '') }}" class="js-product-sell-hidden">
                      <input type="hidden" name="g" value="{{ $g }}">
                      <input type="hidden" name="q" value="{{ $q }}">
                      <input type="hidden" name="c" value="{{ $c }}">
                      <button type="submit" style="padding:3px 8px;background:#080;color:#fff;font-size:10px;font-weight:700;border:1px solid #060;cursor:pointer;">Add</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('mt.pos.add_cut') }}" class="js-pos-price-form js-pos-ajax-form">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $p->id }}">
                      <input type="hidden" name="selling_price" value="{{ number_format($sell, 1, '.', '') }}" class="js-product-sell-hidden">
                      <input type="hidden" name="full_w_ft" value="{{ $fullW }}">
                      <input type="hidden" name="full_l_ft" value="{{ $fullL }}">
                      <input type="hidden" name="cut_w_ft" value="{{ $fullW }}">
                      <input type="hidden" name="cut_l_ft" value="{{ $fullL }}">
                      <input type="hidden" name="g" value="{{ $g }}">
                      <input type="hidden" name="q" value="{{ $q }}">
                      <input type="hidden" name="c" value="{{ $c }}">
                      <button type="submit" style="padding:3px 8px;background:#080;color:#fff;font-size:10px;font-weight:700;border:1px solid #060;cursor:pointer;">Add Full</button>
                    </form>
                    <form method="POST" action="{{ route('mt.pos.add_cut') }}" class="flex gap-8 items-center js-pos-price-form js-pos-ajax-form">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $p->id }}">
                      <input type="hidden" name="selling_price" value="{{ number_format($sell, 1, '.', '') }}" class="js-product-sell-hidden">
                      <input type="hidden" name="full_w_ft" value="{{ $fullW }}">
                      <input type="hidden" name="full_l_ft" value="{{ $fullL }}">
                      <input type="hidden" name="cut_l_ft" value="{{ $fullL }}">
                      <input type="hidden" name="g" value="{{ $g }}">
                      <input type="hidden" name="q" value="{{ $q }}">
                      <input type="hidden" name="c" value="{{ $c }}">
                      <select name="cut_w_ft" style="padding:3px 6px;min-width:auto;font-size:11px;border:1px solid #999;">
                        @foreach($cutWidths as $cw)
                          <option value="{{ $cw }}">{{ $cw }}×{{ $fullL }}</option>
                        @endforeach
                      </select>
                      <button type="submit" style="padding:3px 8px;background:#333;color:#fff;font-size:10px;font-weight:700;border:1px solid #111;cursor:pointer;">Add Cut</button>
                    </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
  </div>

    </div>
  </div>

  {{-- RIGHT: Cart --}}
  <div class="pos-cart" style="border-radius:0;border:1px solid #ccc;">
    <div style="padding:6px 10px;background:#eee;border-bottom:1px solid #ccc;display:flex;justify-content:space-between;align-items:center;">
      <strong id="cartHeaderCount" style="font-size:11px;text-transform:uppercase;color:#333;">Cart ({{ count($cartRows ?? []) }})</strong>
      <form method="POST" action="{{ route('mt.pos.clear') }}" class="js-pos-ajax-form js-pos-clear-form" onsubmit="return confirm('Clear all items from cart?')">
        @csrf
        <button type="submit" style="padding:2px 8px;font-size:10px;background:#c00;color:#fff;border:1px solid #900;cursor:pointer;">Clear</button>
      </form>
    </div>

    <div class="pos-cart-body">
    <div class="cart-items-wrap" style="padding:6px 10px;">
      @forelse(($cartRows ?? []) as $r)
        <div class="pos-cart-row">
          <div class="pos-cart-row-top">
            <div class="pos-cart-row-name">{{ $r['name'] }}</div>
            <div class="pos-cart-row-controls">
            <input class="js-cart-qty pos-cart-inline-input" data-row="{{ $r['row_id'] }}" value="" type="number" step="1" min="1" title="Qty" placeholder="Qty">
            <span style="color:#94a3b8;font-size:11px;">x</span>
            <span class="pos-cart-field-label">Sell</span>
            <input class="js-cart-price pos-cart-inline-input pos-cart-price-input" data-row="{{ $r['row_id'] }}" value="{{ number_format((float)$r['price'],1,'.','') }}" type="number" step="0.1" min="0" style="width:72px;" title="Selling Price">
            <span style="color:#94a3b8;font-size:11px;">/</span>
            <input class="js-cart-discount pos-cart-inline-input" data-row="{{ $r['row_id'] }}" data-source="{{ $r['discount_source'] }}" value="{{ (float)$r['discount_percent'] > 0 ? number_format((float)$r['discount_percent'],1,'.','') : '' }}" placeholder="0.0" type="number" step="0.1" min="0" @if($r['max_discount_percent'] > 0) max="{{ $r['max_discount_percent'] }}" @endif style="width:50px;" title="Discount %">
            <span style="color:#94a3b8;font-size:11px;">%</span>
            <form method="POST" action="{{ route('mt.pos.remove') }}" class="js-pos-ajax-form js-cart-remove-form" style="margin:0;display:inline;">
              @csrf
              <input type="hidden" name="row_id" value="{{ $r['row_id'] }}">
              <button type="submit" class="pos-cart-remove" title="Remove">x</button>
            </form>
          </div>
          </div>
          <div class="pos-cart-row-meta">
            <div class="pos-cart-row-stats">
              @if($canSeeCost)
                <span class="pos-cart-profit" data-row-profit="{{ $r['row_id'] }}">P:{{ number_format((float)$r['line_profit'],0) }}</span>
              @endif
              <span class="pos-cart-line-total" data-row-total="{{ $r['row_id'] }}">Rs {{ number_format((float)$r['line_total'],0) }}</span>
            </div>
          </div>
        </div>
      @empty
        <div style="color:#94a3b8;text-align:center;padding:20px 10px;font-size:12px;">Cart empty</div>
      @endforelse
    </div>

    <div class="pos-cart-checkout">
      <form method="POST" action="{{ route('mt.pos.checkout') }}" class="form-stack" id="posCheckoutForm">
        @csrf

        {{-- Customer Details - Compact & Bright --}}
        <div class="pos-customer-compact">
          <div class="pos-customer-title">Customer</div>
          <input name="customer_name" placeholder="Customer Name" class="pos-cust-input" id="customerNameField" autocomplete="off">
          <input name="customer_phone" placeholder="Phone Number" class="pos-cust-input" id="customerPhoneField" autocomplete="off">
          <input id="phoneField2" name="customer_phone2" placeholder="Phone #2" class="pos-cust-input pos-cust-extra-phone" style="display:none;">
          <input id="phoneField3" name="customer_phone3" placeholder="Phone #3" class="pos-cust-input pos-cust-extra-phone" style="display:none;">
          <div id="phoneToggleWrapper">
            <a href="javascript:void(0)" id="addPhoneToggle" class="pos-cust-add-phone">+ More</a>
          </div>
          <input name="customer_address" placeholder="Address" class="pos-cust-input" id="customerAddressField" autocomplete="off">
          
          <div style="display:flex;align-items:center;gap:6px;">
            <select name="payment_method" class="pos-cust-select" style="flex:1;">
              @foreach(\App\Support\PaymentMethod::options() as $methodKey => $methodLabel)
                <option value="{{ $methodKey }}" @selected($methodKey === \App\Support\PaymentMethod::HARD_CASH)>{{ $methodLabel }}</option>
              @endforeach
            </select>
            <label style="font-size:10px;color:#333;cursor:pointer;display:flex;align-items:center;gap:3px;flex-shrink:0;background:#f1f5f9;padding:3px 6px;border-radius:4px;border:1px solid #cbd5e1;">
              <input type="checkbox" name="include_sms" id="includeSmsToggle" value="1" style="cursor:pointer;width:12px;height:12px;">
              SMS
            </label>
          </div>
        </div>

        {{-- Total Bill --}}
        <div class="pos-total-card">
          <div class="pos-total-caption">Bill Summary ({{ count($cartRows ?? []) }} items)</div>
          <div class="pos-total-row">
            <span>Gross Total</span>
            <strong id="cartGrossTotal">{{ number_format((float)($cartGrossTotal ?? 0),1) }}</strong>
          </div>
          <div class="pos-total-row">
            <span>Subtotal</span>
            <strong id="cartSubtotal">{{ number_format((float)($cartSubtotal ?? 0),1) }}</strong>
          </div>
          <div class="form-group pos-total-discount-group">
            <label for="posOverallDiscount" class="pos-total-input-label">Overall Discount</label>
            <input id="posOverallDiscount" name="overall_discount" type="number" step="0.1" min="0" value="{{ (float)($cartOverallDiscount ?? 0) > 0 ? number_format((float)($cartOverallDiscount ?? 0),1,'.','') : '' }}" placeholder="0.0" class="mt-input pos-total-input">
          </div>
          <div class="pos-total-row pos-total-final-row">
            <span>Net Total</span>
            <strong id="cartTotal" class="pos-total-amount">Rs {{ number_format((float)($cartTotal ?? 0),1) }}</strong>
          </div>
          @if($canSeeCost)
            <div class="pos-total-row pos-total-profit-row">
              <span>Profit Left</span>
              <strong id="cartProfit" class="pos-total-profit-value">{{ number_format((float)($cartProfit ?? 0),1) }}</strong>
            </div>
          @endif

          <div id="posTenderSummary" class="pos-tender-card" style="border-top:1px solid #555;margin-top:8px;padding-top:8px;">
            <div class="pos-tender-title">Tender Summary</div>
            <div class="pos-tender-row">
              <span>Received</span>
              <strong id="posReceivedPreview" style="font-family:monospace;color:#fff;">Rs 0.0</strong>
            </div>
            <div class="pos-tender-row">
              <span>Change Return</span>
              <strong id="posChangePreview" style="font-family:monospace;color:#fbbf24;">Rs 0.0</strong>
            </div>
            <div id="posDueRow" class="pos-tender-row">
              <span>Balance Due</span>
              <strong id="posDuePreview" style="font-family:monospace;color:#f87171;">Rs 0.0</strong>
            </div>
          </div>
        </div>

        <div class="pos-received-card">
          <label for="posReceivedCash" class="pos-received-label">Amount Received / Tendered</label>
          <input id="posReceivedCash" name="received_cash" type="number" step="0.01" min="0" placeholder="Enter received amount" class="mt-input pos-received-input">
          <div class="pos-received-hint">Enter 0 for udhar / credit sale.</div>
        </div>

        {{-- Action Buttons --}}
        <div class="pos-checkout-actions">
          <button type="button" onclick="posCheckout('save')" class="pos-action-btn pos-action-save" title="Save">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            SAVE
          </button>
          <button type="button" onclick="posCheckout('print')" class="pos-action-btn pos-action-icon" title="Print" style="background:#005;border-color:#003;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          </button>
          <button type="button" onclick="posCheckout('sms')" class="pos-action-btn pos-action-icon" title="SMS" style="background:#850;border-color:#630;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
          </button>
          <button type="button" onclick="posCheckout('whatsapp')" class="pos-action-btn pos-action-icon" title="WhatsApp" style="background:#060;border-color:#040;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          </button>
          <button type="button" onclick="posCheckout('all')" class="pos-action-btn pos-action-icon pos-action-all" title="All (Save+Print+WA+SMS if enabled)" style="background:#050;border-color:#030;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </button>
          <button type="button" onclick="reprintLastBill()" class="pos-action-btn pos-action-icon" id="reprintBtn" title="Reprint Last Bill" @if(empty(session('mt_last_sale_id')))disabled style="background:#555;border-color:#333;cursor:not-allowed;opacity:0.5;" @else style="background:#609;border-color:#407;" @endif>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/><path d="M9 21v-3h6v3"/></svg>
          </button>
        </div>

        <div class="form-group" style="margin-top:6px;">
          <textarea name="note" placeholder="Notes for this sale..." class="mt-textarea pos-note-field"></textarea>
        </div>
      </form>
    </div>
    </div>

  </div>

</div>



<script>
function selectCustomer(name, phone, address) {
  document.getElementById('customerNameField').value = name;
  document.getElementById('customerPhoneField').value = phone;
  document.getElementById('customerAddressField').value = address;
}

(function(){
  /* ───── state ───── */
  const productsApiUrl = @json(route('mt.pos.products'));
  const suggestUrl     = @json(route('mt.pos.suggest'));
  const addUrl         = @json(route('mt.pos.add'));
  const addCutUrl      = @json(route('mt.pos.add_cut'));
  const leftoverAddFullUrl = @json(route('mt.pos.leftover_add_full'));
  const leftoverAddCutUrl  = @json(route('mt.pos.leftover_add_cut'));
  const removeUrl      = @json(route('mt.pos.remove'));
  const token          = @json(csrf_token());
  const canSeeCost     = @json($canSeeCost);

  let currentG = @json($g);
  let currentC = @json($c);
  let currentQ = @json($q);

  const container     = document.getElementById('posProductListContainer');
  const companyBar    = document.getElementById('companyFilterBar');
  const companyLabel  = document.getElementById('companyFilterLabel');
  const companySelect = document.getElementById('companyFilterSelect');
  const companyClear  = document.getElementById('companyFilterClear');
  const qInput        = document.getElementById('posSearch');
  const searchBtn     = document.getElementById('posSearchBtn');
  const dl            = document.getElementById('posSuggestList');

  let latestPosData = null;

  /* ───── AJAX fetch products ───── */
  let fetchCtrl = null; // AbortController

  function fetchProducts(g, q, c) {
    currentG = g;
    currentQ = q;
    currentC = c;
    expandedCompanyGroups.clear();
    expandedFamilyGroups.clear();

    // Update URL bar without reload
    const u = new URL(window.location.href);
    u.searchParams.set('g', g);
    if (q) u.searchParams.set('q', q); else u.searchParams.delete('q');
    if (c) u.searchParams.set('c', c); else u.searchParams.delete('c');
    window.history.replaceState(null, '', u.toString());

    // Update active category visuals
    document.querySelectorAll('.js-pos-tab').forEach(tab => {
      const isActive = tab.dataset.group === g;
      tab.style.background = isActive ? '#333' : 'transparent';
      tab.style.color = isActive ? '#fff' : '#333';
      tab.style.fontWeight = isActive ? '700' : '400';
    });

    // Show loading
    container.innerHTML = '<div style="padding:20px;text-align:center;color:#999;font-size:12px;"><span class="pos-loading-spinner"></span> Loading...</div>';

    // Abort previous request
    if (fetchCtrl) fetchCtrl.abort();
    fetchCtrl = new AbortController();

    const apiUrl = new URL(productsApiUrl, window.location.origin);
    apiUrl.searchParams.set('g', g);
    if (q) apiUrl.searchParams.set('q', q);
    if (c) apiUrl.searchParams.set('c', c);

    fetch(apiUrl.toString(), {
      headers: { 'Accept': 'application/json' },
      signal: fetchCtrl.signal,
    })
    .then(r => r.json())
    .then(data => {
      latestPosData = data;
      renderProducts(data);
      renderCompanySidebar(data);
    })
    .catch(e => {
      if (e.name !== 'AbortError') {
        container.innerHTML = '<div style="padding:16px;color:#c00;text-align:center;font-size:12px;">Failed to load products.</div>';
      }
    });
  }

  /* ───── render product list ───── */
  const expandedCompanyGroups = new Set();
  const expandedFamilyGroups = new Set();

  function deriveProductFamilyName(name) {
    if (!name) return '';
    let trimmed = String(name).trim();
    const patterns = [
      /\s+(?:\d+(?:\.\d+)?(?:\s*[x×]\s*\d+(?:\.\d+)?)+)(?:\s*(?:ft|feet|in|inch|inches|["'’]+))?$/i,
      /\s+\d+(?:\.\d+)?(?:\s*(?:ft|feet|in|inch|inches|["'’]+))?$/i,
    ];

    for (const pattern of patterns) {
      const stripped = trimmed.replace(pattern, '').trim();
      if (stripped && stripped !== trimmed) {
        trimmed = stripped;
        break;
      }
    }

    // Additional cleanup for slab sheets
    const lower = trimmed.toLowerCase();
    if (lower.includes('slab') || lower.includes('sheet')) {
      trimmed = trimmed.replace(/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*$/i, '').trim();
    }

    return trimmed;
  }

  function buildCompanyGroups(products) {
    const companies = {};
    products.forEach(p => {
      const companyName = p.company_name || '-';
      const familyName = deriveProductFamilyName(p.name || '');
      const companyKey = companyName;
      const familyKey = companyKey + '||' + familyName;

      if (!companies[companyKey]) {
        companies[companyKey] = {
          key: companyKey,
          name: companyName,
          category_name: p.category_name || '-',
          families: {},
        };
      }

      if (!companies[companyKey].families[familyKey]) {
        companies[companyKey].families[familyKey] = {
          key: familyKey,
          name: familyName,
          variants: [],
        };
      }

      companies[companyKey].families[familyKey].variants.push(p);
    });

    return Object.values(companies).map(co => ({
      ...co,
      families: Object.values(co.families),
    }));
  }

  function normalizeSizeSeparator(text) {
    return String(text || '').replace(/(?<=\d)\s*[x×]\s*(?=\d)/g, '*');
  }

  function renderProductMeta(p) {
    let meta = esc(p.company_name) + ' • ' + esc(p.category_name);
    if (p.width_in || p.length_in || p.height_in) {
      const isSlab = p.category_name && (p.category_name.toLowerCase().includes('slab') || p.category_name.toLowerCase().includes('sheet'));
      if (isSlab) {
        meta += ' • ' + parseFloat(p.length_in || 0) + '-' + parseFloat(p.width_in || 0) + '-' + parseFloat(p.height_in || 0);
      } else {
        meta += ' • ' + esc(p.width_in || 0) + '*' + esc(p.length_in || 0) + '*' + esc(p.height_in || 0);
      }
    } else if (p.is_sheet) {
      meta += ' • Sheet: ' + p.full_w + '×' + p.full_l;
    }
    if (typeof p.stock_qty !== 'undefined') {
      if (p.stock_qty <= 0) {
        meta += ' • Out of stock';
      } else {
        meta += ' • Stock: ' + esc(p.stock_qty);
      }
    }
    if (p.sku) {
      meta += ' • SKU: ' + esc(p.sku);
    }
    return meta;
  }

  function renderProductRow(p, indentation = 0, familyName = '') {
    const isVariant = indentation > 0;
    const indentStyle = isVariant ? 'padding-left:' + (indentation * 18) + 'px;' : '';
    const variantStyle = isVariant ? 'background:#f8fafc;border-left:3px solid #0ea5e9;border-radius:8px;margin-bottom:4px;padding:10px;' : '';
    
    let displayName = p.name;
    const isSlab = p.category_name && (p.category_name.toLowerCase().includes('slab') || p.category_name.toLowerCase().includes('sheet'));
    if (isSlab && p.length_in && p.width_in && p.height_in) {
      displayName = parseFloat(p.length_in) + '-' + parseFloat(p.width_in) + '-' + parseFloat(p.height_in);
    } else if (isVariant && familyName) {
      // Escape special regex characters in familyName
      const escapedFamily = familyName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      const regex = new RegExp(escapedFamily, 'i');
      let cleaned = p.name.replace(regex, '').trim();
      if (cleaned) {
        displayName = cleaned;
      }
    }
    
    let html = '<div class="pos-product-row js-pos-product-row" data-product-id="' + p.id + '" style="' + indentStyle + variantStyle + '">';
    html += '<div>';
    html += '<div class="pos-product-name" style="font-weight:' + (isVariant ? '600' : '700') + ';display:flex;align-items:center;gap:8px;flex-wrap:wrap;">' + esc(normalizeSizeSeparator(displayName));
    if (typeof p.stock_qty !== 'undefined' && p.stock_qty < 0) {
      html += '<span style="font-size:10px;color:#fff;background:#ea580c;padding:2px 6px;border-radius:999px;font-weight:700;letter-spacing:0.02em;">Stock: ' + esc(p.stock_qty) + '</span>';
    } else if (p.out_of_stock || p.stock_qty === 0) {
      html += '<span style="font-size:10px;color:#fff;background:#b91c1c;padding:2px 6px;border-radius:999px;font-weight:700;letter-spacing:0.02em;">OUT OF STOCK</span>';
    } else if (typeof p.stock_qty !== 'undefined') {
      html += '<span style="font-size:10px;color:#0f766e;background:#d1fae5;padding:2px 6px;border-radius:999px;font-weight:700;letter-spacing:0.02em;">Stock: ' + esc(p.stock_qty) + '</span>';
    }
    html += '</div>';
    html += '<div class="pos-product-meta" style="color:' + (isVariant ? '#475569' : '#64748b') + ';">' + renderProductMeta(p) + '</div>';
    if ((p.out_of_stock || p.stock_qty <= 0) && p.equivalents && p.equivalents.length > 0) {
      const inStockEquivalents = p.equivalents.filter(eq => eq.stock_qty > 0);
      if (inStockEquivalents.length > 0) {
        html += '<div style="margin-top:6px; font-size:11px; color:#c2410c; background:#fff7ed; border:1px solid #ffedd5; padding:6px 10px; border-radius:6px; display:flex; flex-direction:column; gap:4px; margin-bottom:2px;">';
        html += '<div style="font-weight:700; display:flex; align-items:center; gap:4px;">⚠️ Alternative stock available:</div>';
        html += '<div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">';
        inStockEquivalents.forEach(eq => {
          const codeLabel = eq.sheet_code ? ' (Code: ' + esc(eq.sheet_code) + ')' : '';
          html += '<button type="button" class="js-add-eq-btn" data-eq-id="' + eq.id + '" data-eq-sell="' + eq.sell_price + '" style="background:#ea580c; color:#fff; border:none; padding:2px 8px; border-radius:4px; cursor:pointer; font-weight:700; font-size:10px; transition:background-color 150ms;" onmouseover="this.style.backgroundColor=\'#d97706\'" onmouseout="this.style.backgroundColor=\'#ea580c\'">';
          html += 'Add ' + esc(eq.company_name) + codeLabel + ' — ' + esc(eq.name) + ' (' + eq.stock_qty + ' in stock)';
          html += '</button>';
        });
        html += '</div></div>';
      }
    }
    html += '</div>';
    html += '<div class="pos-product-price">';
    html += '<input type="number" step="0.01" min="' + esc(p.min_sell) + '" value="" class="pos-product-sell-input js-product-sell" data-default="' + esc(p.sell_raw) + '" data-min="' + esc(p.min_sell) + '" placeholder="' + esc(p.sell_raw) + '">';
    if ((Number(p.max_discount_percent) || 0) > 0) {
      html += '<div class="pos-product-sell-note">Max ' + esc(p.max_discount_percent) + '% off</div>';
    }
    html += '</div>';
    html += '<div class="pos-product-actions">';

    if (!p.is_sheet) {
      html += '<form method="POST" action="' + addUrl + '" class="js-pos-price-form js-pos-ajax-form">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="product_id" value="' + p.id + '">';
      html += '<input type="hidden" name="selling_price" value="' + esc(p.sell_raw) + '" class="js-product-sell-hidden">';
      html += '<input type="hidden" name="g" value="' + esc(currentG) + '">';
      html += '<input type="hidden" name="q" value="' + esc(currentQ) + '">';
      html += '<input type="hidden" name="c" value="' + esc(currentC) + '">';
      html += '<button type="submit" style="padding:3px 8px;background:#080;color:#fff;font-size:10px;font-weight:700;border:1px solid #060;cursor:pointer;">Add</button>';
      html += '</form>';
    } else {
      html += '<form method="POST" action="' + addCutUrl + '" class="js-pos-price-form js-pos-ajax-form">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="product_id" value="' + p.id + '">';
      html += '<input type="hidden" name="selling_price" value="' + esc(p.sell_raw) + '" class="js-product-sell-hidden">';
      html += '<input type="hidden" name="full_w_ft" value="' + p.full_w + '">';
      html += '<input type="hidden" name="full_l_ft" value="' + p.full_l + '">';
      html += '<input type="hidden" name="cut_w_ft" value="' + p.full_w + '">';
      html += '<input type="hidden" name="cut_l_ft" value="' + p.full_l + '">';
      html += '<input type="hidden" name="g" value="' + esc(currentG) + '">';
      html += '<input type="hidden" name="q" value="' + esc(currentQ) + '">';
      html += '<input type="hidden" name="c" value="' + esc(currentC) + '">';
      html += '<button type="submit" style="padding:3px 8px;background:#080;color:#fff;font-size:10px;font-weight:700;border:1px solid #060;cursor:pointer;">Add Full</button>';
      html += '</form>';
      html += '<form method="POST" action="' + addCutUrl + '" class="flex gap-8 items-center js-pos-price-form js-pos-ajax-form">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="product_id" value="' + p.id + '">';
      html += '<input type="hidden" name="selling_price" value="' + esc(p.sell_raw) + '" class="js-product-sell-hidden">';
      html += '<input type="hidden" name="full_w_ft" value="' + p.full_w + '">';
      html += '<input type="hidden" name="full_l_ft" value="' + p.full_l + '">';
      html += '<input type="hidden" name="cut_l_ft" value="' + p.full_l + '">';
      html += '<input type="hidden" name="g" value="' + esc(currentG) + '">';
      html += '<input type="hidden" name="q" value="' + esc(currentQ) + '">';
      html += '<input type="hidden" name="c" value="' + esc(currentC) + '">';
      html += '<select name="cut_w_ft" style="padding:3px 6px;min-width:auto;font-size:11px;border:1px solid #999;">';
      (p.cut_widths || []).forEach(cw => {
        html += '<option value="' + cw + '">' + cw + '×' + p.full_l + '</option>';
      });
      html += '</select>';
      html += '<button type="submit" style="padding:3px 8px;background:#333;color:#fff;font-size:10px;font-weight:700;border:1px solid #111;cursor:pointer;">Add Cut</button>';
      html += '</form>';
    }

    html += '</div></div>';
    return html;
  }

  function renderProducts(data) {
    if (data.is_leftover) {
      renderLeftovers(data.leftovers || []);
      return;
    }
    const products = data.products || [];
    if (products.length === 0) {
      container.innerHTML = '<div class="pos-product-list"><div style="padding:16px;color:#999;text-align:center;font-size:12px;">No products found.</div></div>';
      return;
    }

    const companies = buildCompanyGroups(products);
    let html = '<div class="pos-product-list">';
    html += '<div class="pos-product-header"><div>Product</div><div style="text-align:right;">Sell</div><div style="text-align:right;">Action</div></div>';

    companies.forEach(company => {
      const companyExpanded = expandedCompanyGroups.has(company.key);
      const familyCount = company.families.length;
      const totalVariants = company.families.reduce((sum, family) => sum + family.variants.length, 0);

      html += '<div class="pos-product-row js-pos-product-company" data-company="' + esc(company.key) + '" style="cursor:pointer;background:' + (companyExpanded ? '#eef2ff' : '#fff') + ';border:1px solid ' + (companyExpanded ? '#c7d2fe' : '#e2e8f0') + ';border-radius:10px;margin-bottom:4px;padding:8px 10px;">';
      html += '<div>';
      html += '<div class="pos-product-name" style="font-weight:800;">';
      html += '<span style="display:inline-block;width:16px;text-align:center;margin-right:6px;font-size:12px;color:#475569;">' + (companyExpanded ? '▼' : '▶') + '</span>';
      html += esc(company.name) + ' <span style="font-size:11px;color:#475569;">(' + familyCount + ' qualities, ' + totalVariants + ' variants)</span>';
      html += '</div>';
      html += '<div class="pos-product-meta" style="color:#475569;">' + esc(company.category_name) + '</div>';
      html += '</div>';
      html += '<div class="pos-product-price"></div>';
      html += '<div class="pos-product-actions" style="text-align:right;">';
      html += '<button type="button" class="pos-action-btn" style="padding:3px 8px;background:' + (companyExpanded ? '#0f172a' : '#0b6') + ';color:#fff;border:1px solid #111;font-size:11px;cursor:pointer;border-radius:6px;">' + (companyExpanded ? 'Hide products' : 'Show products') + '</button>';
      html += '</div></div>';

      if (companyExpanded) {
        company.families.forEach(family => {
          const familyExpanded = expandedFamilyGroups.has(family.key);
          const variantCount = family.variants.length;

          if (variantCount === 1) {
            html += renderProductRow(family.variants[0], 1, family.name);
            return;
          }

          html += '<div class="pos-product-row js-pos-product-family" data-family="' + esc(family.key) + '" style="cursor:pointer;background:' + (familyExpanded ? '#f8fafc' : '#fff') + ';border:1px solid ' + (familyExpanded ? '#cbd5e1' : '#e2e8f0') + ';border-radius:10px;margin:4px 0 4px 18px;padding:8px 10px;">';
          html += '<div>';
          html += '<div class="pos-product-name" style="font-weight:700;">';
          html += '<span style="display:inline-block;width:16px;text-align:center;margin-right:6px;font-size:12px;color:#94a3b8;">' + (familyExpanded ? '▼' : '▶') + '</span>';
          html += esc(family.name) + ' <span style="font-size:11px;color:#475569;">(' + variantCount + ' sizes)</span>';
          html += '</div>';
          html += '<div class="pos-product-meta" style="color:#475569;">' + esc(company.name) + '</div>';
          html += '</div>';
          html += '<div class="pos-product-price"></div>';
          html += '<div class="pos-product-actions" style="text-align:right;">';
          html += '<button type="button" class="pos-action-btn" style="padding:3px 8px;background:' + (familyExpanded ? '#0f172a' : '#0b6') + ';color:#fff;border:1px solid #111;font-size:11px;cursor:pointer;border-radius:6px;">' + (familyExpanded ? 'Hide sizes' : 'Show sizes') + '</button>';
          html += '</div></div>';

          if (familyExpanded) {
            family.variants.forEach(p => {
              html += renderProductRow(p, 2, family.name);
            });
          }
        });
      }
    });

    html += '</div>';
    container.innerHTML = html;
    attachProductListeners();
  }

  /* ───── render leftover list ───── */
  function renderLeftovers(leftovers) {
    if (leftovers.length === 0) {
      container.innerHTML = '<div class="pos-product-list"><div style="padding:16px;color:#999;text-align:center;font-size:12px;">No leftovers yet.</div></div>';
      return;
    }

    let html = '<div class="pos-product-list">';
    html += '<div class="pos-product-header" style="grid-template-columns:1.4fr 0.6fr 1.2fr;"><div>Leftover Item</div><div style="text-align:right;">Size</div><div style="text-align:right;">Action</div></div>';

    leftovers.forEach(lf => {
      html += '<div class="pos-product-row" style="grid-template-columns:1.4fr 0.6fr 1.2fr;">';
      html += '<div><div class="pos-product-name">' + esc(lf.product_name) + '</div>';
      html += '<div class="pos-product-meta">' + esc(lf.company_name) + ' • ' + esc(lf.category_name) + '</div></div>';
      html += '<div style="text-align:right;font-weight:700;">' + lf.width + '×' + lf.length + '</div>';
      html += '<div class="pos-product-actions">';

      // Add Full
      html += '<form method="POST" action="' + leftoverAddFullUrl + '">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="leftover_id" value="' + lf.id + '">';
      html += '<button type="submit" class="btn btn-success btn-xs">Add Full</button>';
      html += '</form>';

      // Add Cut
      html += '<form method="POST" action="' + leftoverAddCutUrl + '" class="flex gap-8 items-center">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="leftover_id" value="' + lf.id + '">';
      html += '<select name="cut_w_ft" class="mt-select" style="padding:6px 8px;min-width:auto;font-size:12px;">';
      (lf.cut_list || []).forEach(cw => {
        html += '<option value="' + cw + '">' + cw + '×' + Math.round(lf.length_raw) + '</option>';
      });
      html += '</select>';
      html += '<button type="submit" class="btn btn-primary btn-xs">Add Cut</button>';
      html += '</form>';

      html += '</div></div>';
    });

    html += '</div>';
    container.innerHTML = html;
  }

  /* ───── render company filter ───── */
  function renderCompanySidebar(data) {
    const g = data.g || '';
    const companies = data.companies || [];
    const coContainer = document.getElementById('sidebarCompaniesContainer');
    if (!coContainer) return;

    if (g === 'leftover') {
      coContainer.innerHTML = '<div style="color:#999;font-size:11px;margin-top:6px;">Select a category first.</div>';
      return;
    }

    let html = '<ul style="list-style:none;padding-left:0;margin-top:6px;max-height:600px;overflow:auto;" id="sidebarCompanies">';
    
    // "All Companies"
    const isAllActive = (currentC === '' || currentC === null);
    html += '<li><a href="javascript:void(0)" data-company="" class="js-pos-company" style="text-decoration:none;display:block;padding:5px 8px;margin-bottom:1px;font-size:12px;' + (isAllActive ? 'background:#333;color:#fff;font-weight:700;' : 'color:#333;') + '">All Companies</a></li>';

    companies.forEach(co => {
      const isActive = String(currentC) === String(co.id);
      html += '<li><a href="javascript:void(0)" data-company="' + co.id + '" class="js-pos-company" style="text-decoration:none;display:block;padding:5px 8px;margin-bottom:1px;font-size:12px;' + (isActive ? 'background:#333;color:#fff;font-weight:700;' : 'color:#333;') + '">' + esc(co.name) + '</a></li>';
    });

    html += '</ul>';
    coContainer.innerHTML = html;
    attachCompanyListeners();
  }

  function attachCompanyListeners() {
    document.querySelectorAll('.js-pos-company').forEach(link => {
      link.onclick = function(e) {
        e.preventDefault();
        const cid = this.dataset.company || '';
        fetchProducts(currentG, currentQ, cid);
      };
    });
  }

  function attachProductListeners() {
    document.querySelectorAll('.js-pos-product-row').forEach(row => {
      row.onclick = function(e) {
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' ||
            e.target.tagName === 'FORM' || e.target.closest('form')) {
          return;
        }

        const productId = this.dataset.productId;
        if (!productId) return;

        lastClickedProduct = { id: productId };
        document.querySelectorAll('.js-pos-product-row').forEach(r => { r.style.backgroundColor = ''; });
        this.style.backgroundColor = '#f5f5f5';
      };
    });

    document.querySelectorAll('.js-pos-product-company').forEach(row => {
      row.onclick = function(e) {
        if (e.target.closest('form')) {
          return;
        }

        const key = this.dataset.company;
        if (!key) return;
        if (expandedCompanyGroups.has(key)) {
          expandedCompanyGroups.delete(key);
        } else {
          expandedCompanyGroups.add(key);
        }
        const latest = latestPosData || { products: [] };
        renderProducts({
          ...latest,
          products: latest.products || []
        });
      };
    });

    document.querySelectorAll('.js-pos-product-family').forEach(row => {
      row.onclick = function(e) {
        if (e.target.closest('form')) {
          return;
        }

        const key = this.dataset.family;
        if (!key) return;
        if (expandedFamilyGroups.has(key)) {
          expandedFamilyGroups.delete(key);
        } else {
          expandedFamilyGroups.add(key);
        }
        const latest = latestPosData || { products: [] };
        renderProducts({
          ...latest,
          products: latest.products || []
        });
      };
    });
  }

  function renderCompanyFilter(data) { /* stub for compatibility */ }

  /* ───── utility ───── */
  function esc(s) {
    if (s == null) return '';
    const d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
  }

  /* ───── event: tab click ───── */
  document.querySelectorAll('.js-pos-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      const g = this.dataset.group;
      fetchProducts(g, currentQ, '');
    });
  });

  attachCompanyListeners();
  attachProductListeners();
  fetchProducts(currentG, currentQ, currentC);

  /* ───── event: search ───── */
  searchBtn.addEventListener('click', function() {
    currentQ = (qInput.value || '').trim();
    currentG = '';
    currentC = '';
    
    document.querySelectorAll('.js-pos-tab').forEach(tab => {
      const isActive = tab.dataset.group === '';
      tab.style.background = isActive ? '#333' : 'transparent';
      tab.style.color = isActive ? '#fff' : '#333';
      tab.style.fontWeight = isActive ? '700' : '400';
    });
    
    fetchProducts(currentG, currentQ, currentC);
  });

  qInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      currentQ = (qInput.value || '').trim();
      currentG = '';
      currentC = '';
      
      document.querySelectorAll('.js-pos-tab').forEach(tab => {
        const isActive = tab.dataset.group === '';
        tab.style.background = isActive ? '#333' : 'transparent';
        tab.style.color = isActive ? '#fff' : '#333';
        tab.style.fontWeight = isActive ? '700' : '400';
      });
      
      fetchProducts(currentG, currentQ, currentC);
    }
  });

  /* ───── typeahead suggest with product filtering ───── */
  let suggestT = null;
  let suggestItems = []; // Store suggestions for lookup
  let suggestIndex = {}; // Map product name/id to product data
  
  function fillSuggest(items){
    if(!dl) return;
    suggestItems = items || [];
    suggestIndex = {};
    dl.innerHTML = '';
    
    (items || []).forEach(it => {
      const opt = document.createElement('option');
      const meta = [it.company, it.category].filter(Boolean).join(' • ');
      const stockLabel = (typeof it.stock_qty !== 'undefined' && it.stock_qty < 0)
        ? 'Stock: ' + it.stock_qty
        : (typeof it.stock_qty !== 'undefined' && it.stock_qty === 0 ? 'Out of stock' : (typeof it.stock_qty !== 'undefined' ? 'Stock: ' + it.stock_qty : ''));
      
      const isSlab = it.category && (it.category.toLowerCase().includes('slab') || it.category.toLowerCase().includes('sheet'));
      let displayName = it.name;
      if (isSlab && it.length_in && it.width_in && it.height_in) {
        const familyName = deriveProductFamilyName(it.name || '');
        displayName = familyName + ' ' + parseFloat(it.length_in) + '-' + parseFloat(it.width_in) + '-' + parseFloat(it.height_in);
      }

      const fullLabel = [normalizeSizeSeparator(displayName), meta, stockLabel].filter(Boolean).join(' — ');
      opt.value = normalizeSizeSeparator(displayName);
      opt.label = fullLabel;
      opt.dataset.productId = it.id;
      dl.appendChild(opt);
      
      // Store for lookup
      suggestIndex[displayName.toLowerCase()] = it;
      suggestIndex[normalizeSizeSeparator(displayName).toLowerCase()] = it;
      suggestIndex[it.id] = it;
    });
  }

  if (qInput && dl) {
    qInput.addEventListener('input', function(){
      const val = (qInput.value || '').trim();
      if (val.length < 1) { 
        fillSuggest([]);
        // If search bar is cleared, show all products
        currentQ = '';
        fetchProducts(currentG, currentQ, currentC);
        return; 
      }
      clearTimeout(suggestT);
      suggestT = setTimeout(async () => {
        try {
          const u = new URL(suggestUrl, window.location.origin);
          u.searchParams.set('q', val);
          const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          fillSuggest(data.items || []);
        } catch(e) {}
      }, 200);
    });
    
    // Detect when user selects a suggestion from datalist
    qInput.addEventListener('change', function(){
      const val = (qInput.value || '').trim();
      if (!val) return;
      
      // Look up the selected product
      const product = suggestIndex[val.toLowerCase()] || suggestIndex[val];
      if (product && product.id) {
        // Clear active category and company filters to perform a global search
        currentG = '';
        currentC = '';
        
        // Update active category visuals in UI
        document.querySelectorAll('.js-pos-tab').forEach(tab => {
          const isActive = tab.dataset.group === '';
          tab.style.background = isActive ? '#333' : 'transparent';
          tab.style.color = isActive ? '#fff' : '#333';
          tab.style.fontWeight = isActive ? '700' : '400';
        });

        // Set the search input to the product ID to filter for just this product
        qInput.value = product.id;
        // Trigger search with just this product ID
        currentQ = product.id;
        fetchProducts(currentG, currentQ, currentC);
      }
    });
  }

  /* ───── live cart qty/price update (same as before) ───── */
  const updateItemUrl = @json(route('mt.pos.update_item'));
  const updateOverallDiscountUrl = @json(route('mt.pos.update_overall_discount'));
  const receivedCashInput = document.getElementById('posReceivedCash');
  const overallDiscountInput = document.getElementById('posOverallDiscount');
  const cartGrossTotalEl = document.getElementById('cartGrossTotal');
  const cartLineDiscountEl = document.getElementById('cartLineDiscount');
  const cartSubtotalEl = document.getElementById('cartSubtotal');
  const cartTotalEl = document.getElementById('cartTotal');
  const cartProfitEl = document.getElementById('cartProfit');
  const receivedPreviewEl = document.getElementById('posReceivedPreview');
  const changePreviewEl = document.getElementById('posChangePreview');
  const dueRowEl = document.getElementById('posDueRow');
  const duePreviewEl = document.getElementById('posDuePreview');
  const rowSourceState = {};
  const debounceTimers = {};
  let currentCartSummary = {
    gross_total: Number(@json((float)($cartGrossTotal ?? 0))) || 0,
    line_discount_total: Number(@json((float)($cartLineDiscountTotal ?? 0))) || 0,
    subtotal: Number(@json((float)($cartSubtotal ?? 0))) || 0,
    overall_discount: Number(@json((float)($cartOverallDiscount ?? 0))) || 0,
    total: Number(@json((float)($cartTotal ?? 0))) || 0,
    profit: Number(@json((float)($cartProfit ?? 0))) || 0
  };
  let currentCartTotal = Number(currentCartSummary.total) || 0;

  function formatMoney(value) {
    return 'Rs ' + (Number(value) || 0).toFixed(1);
  }

  function formatInputNumber(value) {
    return (Number(value) || 0).toFixed(1);
  }

  function formatDiscountInput(value) {
    const n = Number(value) || 0;
    return n > 0 ? n.toFixed(1) : '';
  }

  function createNotificationContainer() {
    let container = document.getElementById('posNotificationContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'posNotificationContainer';
      container.style.position = 'fixed';
      container.style.top = '16px';
      container.style.right = '16px';
      container.style.zIndex = '9999';
      container.style.display = 'flex';
      container.style.flexDirection = 'column';
      container.style.gap = '10px';
      container.style.pointerEvents = 'none';
      document.body.appendChild(container);
    }
    return container;
  }

  function mtNotify(message, type = 'info') {
    if (!message) return;
    const container = createNotificationContainer();
    const note = document.createElement('div');
    const palette = {
      success: { bg: '#d1fae5', border: '#a7f3d0', color: '#065f46' },
      error: { bg: '#fee2e2', border: '#fecaca', color: '#991b1b' },
      info: { bg: '#dbeafe', border: '#bfdbfe', color: '#1e3a8a' }
    };
    const style = palette[type] || palette.info;

    note.style.background = style.bg;
    note.style.border = '1px solid ' + style.border;
    note.style.color = style.color;
    note.style.padding = '12px 14px';
    note.style.borderRadius = '10px';
    note.style.boxShadow = '0 10px 30px rgba(15, 23, 42, 0.08)';
    note.style.maxWidth = '320px';
    note.style.fontSize = '13px';
    note.style.lineHeight = '1.4';
    note.style.pointerEvents = 'auto';
    note.style.opacity = '0';
    note.style.transform = 'translateY(-10px)';
    note.style.transition = 'opacity 180ms ease, transform 180ms ease';
    note.innerText = message;

    container.appendChild(note);
    requestAnimationFrame(() => {
      note.style.opacity = '1';
      note.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
      note.style.opacity = '0';
      note.style.transform = 'translateY(-10px)';
      note.addEventListener('transitionend', function() {
        note.remove();
      }, { once: true });
    }, 3800);
  }

  window.mtShowNotification = window.mtShowNotification || mtNotify;

  function setRowSource(rowId, source) {
    rowSourceState[rowId] = source || 'none';
  }

  function updateTenderSummary() {
    const received = Math.max(0, Number(receivedCashInput?.value || 0) || 0);
    const change = Math.max(received - currentCartTotal, 0);
    const due = Math.max(currentCartTotal - received, 0);

    if (receivedPreviewEl) receivedPreviewEl.innerText = formatMoney(received);
    if (changePreviewEl) changePreviewEl.innerText = formatMoney(change);
    if (duePreviewEl) duePreviewEl.innerText = formatMoney(due);
    if (dueRowEl) dueRowEl.style.display = due > 0.009 ? 'flex' : 'none';
  }

  function applyCartSummary(summary) {
    if (!summary) return;

    currentCartSummary = Object.assign({}, currentCartSummary, summary);
    currentCartTotal = Number(currentCartSummary.total) || 0;

    if (cartGrossTotalEl) cartGrossTotalEl.innerText = formatMoney(currentCartSummary.gross_total);
    if (cartLineDiscountEl) cartLineDiscountEl.innerText = formatMoney(currentCartSummary.line_discount_total);
    if (cartSubtotalEl) cartSubtotalEl.innerText = formatMoney(currentCartSummary.subtotal);
    if (cartTotalEl) cartTotalEl.innerText = formatMoney(currentCartSummary.total);
    if (cartProfitEl) cartProfitEl.innerText = Number(currentCartSummary.profit || 0).toFixed(2);
    if (overallDiscountInput && document.activeElement !== overallDiscountInput) {
      overallDiscountInput.value = formatDiscountInput(currentCartSummary.overall_discount);
    }

    updateTenderSummary();
  }

  function applyRowState(row) {
    if (!row || !row.row_id) return;

    const qtyInp = document.querySelector('.js-cart-qty[data-row="' + row.row_id + '"]');
    const priceInp = document.querySelector('.js-cart-price[data-row="' + row.row_id + '"]');
    const discountInp = document.querySelector('.js-cart-discount[data-row="' + row.row_id + '"]');
    const lineTotalEl = document.querySelector('[data-row-total="' + row.row_id + '"]');
    const lineProfitEl = document.querySelector('[data-row-profit="' + row.row_id + '"]');

    if (qtyInp) qtyInp.value = row.qty;
    if (priceInp && document.activeElement !== priceInp) {
      priceInp.value = formatInputNumber(row.price);
    }
    if (discountInp && document.activeElement !== discountInp) {
      discountInp.value = formatDiscountInput(row.discount_percent);
    }
    if (lineTotalEl) lineTotalEl.innerText = formatMoney(row.line_total);
    if (lineProfitEl) lineProfitEl.innerText = 'P:' + Math.round(Number(row.line_profit || 0));
    if (row.discount_source) {
      setRowSource(row.row_id, row.discount_source);
      if (discountInp) discountInp.dataset.source = row.discount_source;
    }
  }

  function debounce(key, fn, delay = 250) {
    if (debounceTimers[key]) clearTimeout(debounceTimers[key]);
    debounceTimers[key] = setTimeout(fn, delay);
  }

  function postUpdate(payload) {
    return fetch(updateItemUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if (!data || !data.ok) return data;
      applyRowState(data.row);
      applyCartSummary(data.summary);
      return data;
    })
    .catch(() => null);
  }

  function postOverallDiscount() {
    return fetch(updateOverallDiscountUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      body: JSON.stringify({ overall_discount: overallDiscountInput?.value || 0 })
    })
    .then(r => r.json())
    .then(data => {
      if (!data || !data.ok) return data;
      applyCartSummary(data.summary);
      return data;
    })
    .catch(() => null);
  }

  function buildRowSyncPayload(rowId) {
    const qtyInp = document.querySelector('.js-cart-qty[data-row="' + rowId + '"]');
    const priceInp = document.querySelector('.js-cart-price[data-row="' + rowId + '"]');
    const discountInp = document.querySelector('.js-cart-discount[data-row="' + rowId + '"]');

    return {
      row_id: rowId,
      qty: qtyInp ? qtyInp.value : null,
      price: priceInp ? priceInp.value : null,
      discount_percent: discountInp ? discountInp.value : null,
      discount_source: rowSourceState[rowId] || discountInp?.dataset.source || 'none'
    };
  }

  function updateCartHeaderCount(count) {
    const header = document.getElementById('cartHeaderCount');
    if (!header) return;
    header.innerText = 'Cart (' + (Number(count) || 0) + ')';
  }

  function renderCartRows(rows) {
    const container = document.querySelector('.cart-items-wrap');
    if (!container) return;

    if (!Array.isArray(rows) || rows.length === 0) {
      container.innerHTML = '<div style="color:#94a3b8;text-align:center;padding:20px 10px;font-size:12px;">Cart empty</div>';
      updateCartHeaderCount(0);
      return;
    }

    let html = '';
    rows.forEach(r => {
      const maxDiscountAttr = Number(r.max_discount_percent || 0) > 0 ? ' max="' + esc(r.max_discount_percent) + '"' : '';
      const discountSource = esc(r.discount_source || 'none');

      html += '<div class="pos-cart-row">';
      html += '<div class="pos-cart-row-top">';
      html += '<div class="pos-cart-row-name">' + esc(r.name) + '</div>';
      html += '<div class="pos-cart-row-controls">';
      html += '<input class="js-cart-qty pos-cart-inline-input" data-row="' + esc(r.row_id) + '" value="" type="number" step="1" min="1" title="Qty" placeholder="Qty">';
      html += '<span style="color:#94a3b8;font-size:11px;">x</span>';
      html += '<span class="pos-cart-field-label">Sell</span>';
      html += '<input class="js-cart-price pos-cart-inline-input pos-cart-price-input" data-row="' + esc(r.row_id) + '" value="' + esc(r.price) + '" type="number" step="0.1" min="0" style="width:72px;" title="Selling Price">';
      html += '<span style="color:#94a3b8;font-size:11px;">/</span>';
      html += '<input class="js-cart-discount pos-cart-inline-input" data-row="' + esc(r.row_id) + '" data-source="' + discountSource + '" value="' + esc(formatDiscountInput(r.discount_percent)) + '" placeholder="0.0" type="number" step="0.1" min="0"' + maxDiscountAttr + ' style="width:50px;" title="Discount %">';
      html += '<span style="color:#94a3b8;font-size:11px;">%</span>';
      html += '<form method="POST" action="' + removeUrl + '" class="js-pos-ajax-form js-cart-remove-form" style="margin:0;display:inline;">';
      html += '<input type="hidden" name="_token" value="' + token + '">';
      html += '<input type="hidden" name="row_id" value="' + esc(r.row_id) + '">';
      html += '<button type="submit" class="pos-cart-remove" title="Remove">x</button>';
      html += '</form>';
      html += '</div></div>';
      html += '<div class="pos-cart-row-meta"><div class="pos-cart-row-stats">';

      if (canSeeCost) {
        html += '<span class="pos-cart-profit" data-row-profit="' + esc(r.row_id) + '">P:' + Math.round(Number(r.line_profit || 0)) + '</span>';
      }

      html += '<span class="pos-cart-line-total" data-row-total="' + esc(r.row_id) + '">Rs ' + Math.round(Number(r.line_total || 0)) + '</span>';
      html += '</div></div></div>';
    });

    container.innerHTML = html;
    updateCartHeaderCount(rows.length);
    attachCartListeners();
  }

  function attachCartListeners() {
    document.querySelectorAll('.js-cart-discount').forEach(inp => {
      const rowId = inp.dataset.row;
      setRowSource(rowId, inp.dataset.source || 'none');
      inp.oninput = function() {
        setRowSource(rowId, 'percent');
        debounce('row:' + rowId, () => postUpdate({ row_id: rowId, discount_percent: this.value, discount_source: 'percent' }));
      };
      inp.onfocus = function() {
        const val = parseFloat(this.value) || 0;
        if (val === 0) {
          this.value = '';
        }
        setTimeout(() => this.select(), 50);
      };
      inp.onblur = function() {
        if (this.value === '') {
          setRowSource(rowId, 'percent');
          postUpdate({ row_id: rowId, discount_percent: 0, discount_source: 'percent' });
        }
      };
    });

    document.querySelectorAll('.js-cart-qty').forEach(inp => {
      const rowId = inp.dataset.row;
      inp.oninput = function() {
        debounce('row:' + rowId, () => postUpdate({ row_id: rowId, qty: this.value }));
      };
    });

    document.querySelectorAll('.js-cart-price').forEach(inp => {
      const rowId = inp.dataset.row;
      const syncPrice = function() {
        setRowSource(rowId, 'price');
        postUpdate({ row_id: rowId, price: this.value, discount_source: 'price' });
      };

      inp.onchange = syncPrice;
      inp.onblur = syncPrice;
      inp.onkeydown = function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          this.blur();
        }
      };
    });
  }

  function submitPosAjaxForm(form) {
    const formData = new FormData(form);
    return fetch(form.action, {
      method: (form.method || 'POST').toUpperCase(),
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: formData,
    })
    .then(async response => {
      const data = await response.json().catch(() => null);
      if (!response.ok || !data || !data.ok) {
        const message = data?.message || 'Action failed';
        throw new Error(message);
      }
      if (Array.isArray(data.rows)) {
        renderCartRows(data.rows);
      }
      if (data.summary) {
        applyCartSummary(data.summary);
      }
      mtShowNotification('Cart updated', 'success');
      return data;
    });
  }

  attachCartListeners();

  if (overallDiscountInput) {
    overallDiscountInput.addEventListener('input', function() {
      debounce('overall-discount', () => postOverallDiscount());
    });
    overallDiscountInput.addEventListener('change', function() {
      postOverallDiscount();
    });
    overallDiscountInput.addEventListener('focus', function() {
      const val = parseFloat(this.value) || 0;
      if (val === 0) {
        this.value = '';
      }
      setTimeout(() => this.select(), 50);
    });
    overallDiscountInput.addEventListener('blur', function() {
      if (this.value === '') {
        postOverallDiscount();
      }
    });
  }

  function normalizeProductSellInput(input) {
    if (!input) return '0.00';

    const fallback = Number(input.dataset.default || 0) || 0;
    const min = Number(input.dataset.min || 0) || 0;
    let value = Number(input.value || fallback);

    if (!Number.isFinite(value)) value = fallback;
    if (value < min) value = min;
    if (value < 0) value = 0;

    const formatted = value.toFixed(2);
    input.value = formatted;
    return formatted;
  }

  document.addEventListener('change', function(event) {
    const input = event.target.closest('.js-product-sell');
    if (!input) return;
    normalizeProductSellInput(input);
  });

  document.addEventListener('submit', function(event) {
    const form = event.target.closest('form');
    if (!form) return;

    if (form.classList.contains('js-pos-price-form')) {
      const row = form.closest('.pos-product-row');
      const sellInput = row ? row.querySelector('.js-product-sell') : null;
      const sellHidden = form.querySelector('.js-product-sell-hidden');

      if (sellInput && sellHidden) {
        sellHidden.value = normalizeProductSellInput(sellInput);
      }
    }

    if (form.classList.contains('js-pos-ajax-form')) {
      event.preventDefault();
      submitPosAjaxForm(form).catch(error => {
        mtShowNotification(error.message, 'error');
      });
    }
  });

  document.addEventListener('click', function(event) {
    const btn = event.target.closest('.js-add-eq-btn');
    if (!btn) return;
    event.preventDefault();
    const id = btn.dataset.eqId;
    const sell = btn.dataset.eqSell;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = addUrl;

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = token;
    form.appendChild(tokenInput);

    const prodInput = document.createElement('input');
    prodInput.type = 'hidden';
    prodInput.name = 'product_id';
    prodInput.value = id;
    form.appendChild(prodInput);

    const sellInput = document.createElement('input');
    sellInput.type = 'hidden';
    sellInput.name = 'selling_price';
    sellInput.value = sell;
    form.appendChild(sellInput);

    const gInput = document.createElement('input');
    gInput.type = 'hidden';
    gInput.name = 'g';
    gInput.value = currentG;
    form.appendChild(gInput);

    const qInputHidden = document.createElement('input');
    qInputHidden.type = 'hidden';
    qInputHidden.name = 'q';
    qInputHidden.value = currentQ;
    form.appendChild(qInputHidden);

    const cInputHidden = document.createElement('input');
    cInputHidden.type = 'hidden';
    cInputHidden.name = 'c';
    cInputHidden.value = currentC;
    form.appendChild(cInputHidden);

    submitPosAjaxForm(form).catch(error => {
      mtShowNotification(error.message, 'error');
    });
  });

  // Phone toggle (step-by-step)
  const phoneToggle = document.getElementById('addPhoneToggle');
  const field2 = document.getElementById('phoneField2');
  const field3 = document.getElementById('phoneField3');
  const toggleWrapper = document.getElementById('phoneToggleWrapper');

  if (phoneToggle) {
    phoneToggle.addEventListener('click', function() {
      if (field2 && field2.style.display === 'none') {
        field2.style.display = 'block';
      } else if (field3 && field3.style.display === 'none') {
        field3.style.display = 'block';
        if (toggleWrapper) toggleWrapper.style.display = 'none';
      }
    });
  }

  if (receivedCashInput) {
    receivedCashInput.addEventListener('input', updateTenderSummary);
    receivedCashInput.addEventListener('change', updateTenderSummary);
  }

  window.syncPosCartBeforeCheckout = async function() {
    const rowIds = Array.from(new Set(
      Array.from(document.querySelectorAll('.js-cart-qty, .js-cart-price, .js-cart-discount'))
        .map(el => el.dataset.row)
        .filter(Boolean)
    ));

    const requests = rowIds.map(rowId => postUpdate(buildRowSyncPayload(rowId)));
    if (overallDiscountInput) {
      requests.push(postOverallDiscount());
    }

    await Promise.allSettled(requests);
  };

  window.renderCartRows = renderCartRows;
  window.applyCartSummary = applyCartSummary;

  applyCartSummary(currentCartSummary);
})();

// Checkout action handler (outside IIFE so onclick can access it)
async function posCheckout(action) {
  const form = document.getElementById('posCheckoutForm');
  if (!form) return;

  // give user feedback by disabling the clicked button
  const btn = document.activeElement;
  const originalHTML = btn?.innerHTML || '';
  if (btn && btn.classList && btn.classList.contains('pos-action-btn')) {
    btn.disabled = true;
    btn.innerHTML = '<span class="pos-loading-spinner"></span>';
  }

  try {
    if (typeof window.syncPosCartBeforeCheckout === 'function') {
      await window.syncPosCartBeforeCheckout();
    }

    // Get form data
    const formData = new FormData(form);
    formData.set('checkout_action', action);

    // Add SMS flag
    const includeSms = document.getElementById('includeSmsToggle')?.checked ? '1' : '0';
    formData.set('include_sms', includeSms);

    // Submit via AJAX
    const response = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || 'Checkout failed');
    }

    // Handle success - show message
    let successMsg = result.message || 'Sale completed';
    if (result.should_print) {
      successMsg += " Bill sent to printer.";
    }
    console.log(successMsg);

    // Handle printing
    if (result.should_print) {
      // Open sale page in new tab for printing (which will print and auto-close itself)
      const printUrl = result.sale_url + '?autoprint=1';
      window.open(printUrl, 'saleReceipt');
    }

    // Handle WhatsApp
    if (result.should_whatsapp && result.whatsapp_url) {
      window.open(result.whatsapp_url, 'mtPopup');
    }

    // Handle SMS
    if (result.should_sms) {
      console.log(result.sms_message || 'SMS would be sent');
    }

    // Clear cart and reset form
    clearPosCart();
    form.reset();
    const includeSmsToggle = document.getElementById('includeSmsToggle');
    if (includeSmsToggle) {
      includeSmsToggle.checked = false;
    }

    // Show success notification
    mtShowNotification(successMsg, 'success');

  } catch (error) {
    console.error('Checkout error:', error);
    mtShowNotification('Error: ' + error.message, 'error');
  } finally {
    // Re-enable button
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    }
  }
}

// Helper to clear cart on POS (reload page or clear session)
function clearPosCart() {
  if (typeof window.renderCartRows === 'function') {
    window.renderCartRows([]);
  }
  if (typeof window.applyCartSummary === 'function') {
    window.applyCartSummary({
      gross_total: 0,
      line_discount_total: 0,
      subtotal: 0,
      overall_discount: 0,
      total_discount: 0,
      total: 0,
      profit: 0,
    });
  }
}

// Reprint last bill
function reprintLastBill() {
  const lastSaleId = @json(session('mt_last_sale_id') ?? null);
  if (!lastSaleId) {
    alert('No previous bill to reprint.');
    return;
  }

  const printUrl = '/sales/' + lastSaleId + '?autoprint=1';
  window.open(printUrl, 'reprintReceipt');
  mtShowNotification('Bill #' + lastSaleId + ' sent to printer.', 'success');
}

// Quick quantity buttons - add last clicked product with specific qty
let lastClickedProduct = null;

function addQtyToRecent(qty) {
  if (!lastClickedProduct) {
    alert('Please click a product first.');
    return;
  }
  const form = document.querySelector(`input[value="${lastClickedProduct.id}"]`)?.closest('form');
  if (!form) {
    alert('Could not find product form.');
    return;
  }
  const qtyInput = form.querySelector('input[name="qty"]');
  if (qtyInput) {
    qtyInput.value = qty;
  } else {
    const qtyField = document.createElement('input');
    qtyField.type = 'hidden';
    qtyField.name = 'qty';
    qtyField.value = qty;
    form.appendChild(qtyField);
  }
  form.submit();
}
</script>

<style>
  .pos-loading-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #999;
    border-top-color: transparent;
    border-radius: 50%;
    animation: pos-spin 0.6s linear infinite;
    vertical-align: middle;
    margin-right: 4px;
  }
  @keyframes pos-spin { to { transform: rotate(360deg); } }

  .qty-quick-btn {
    padding: 4px 6px !important;
    background: #1e293b !important;
    color: #fff !important;
    border: 1px solid #0f172a !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    border-radius: 3px;
    cursor: pointer;
    min-width: 24px;
  }
  .qty-quick-btn:hover {
    background: #0f172a !important;
  }

  .pos-product-list {
    width: 100%;
  }
  .pos-product-header {
    display: grid;
    grid-template-columns: 1.4fr 0.6fr 1.2fr;
    gap: 8px;
    padding: 6px 10px;
    font-size: 12px;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
  }
  .pos-product-row {
    display: grid;
    grid-template-columns: 1.4fr 0.6fr 1.2fr;
    gap: 8px;
    align-items: center;
    padding: 6px 10px;
    min-height: 34px;
    border-bottom: 1px solid #e2e8f0;
  }
  .pos-product-row:last-child {
    border-bottom: none;
  }
  .pos-product-name {
    font-size: 13px;
    line-height: 1.2;
  }
  .pos-product-meta {
    font-size: 11px;
    margin-top: 2px;
    color: #64748b;
  }
  .pos-product-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
  }
  .pos-product-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
  }
  .pos-action-btn {
    padding: 3px 8px;
    font-size: 10px;
    border-radius: 6px;
  }
  .pos-product-sell-input {
    width: 78px;
    min-height: 28px;
    padding: 4px 6px;
    border: 1px solid #94a3b8;
    background: #fff;
    color: #0f172a;
    font-size: 11px;
    font-weight: 700;
    text-align: right;
  }
  .pos-product-sell-note {
    font-size: 9px;
    color: #64748b;
    white-space: nowrap;
  }
  .pos-cart-row {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 4px;
    padding: 4px 0;
  }
  .pos-cart-row-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 6px;
  }
  .pos-cart-row-name {
    white-space: normal;
    line-height: 1.25;
  }
  .pos-cart-row-controls {
    flex-shrink: 0;
  }
  .pos-cart-row-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
  }
  .pos-cart-discount-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    min-width: 0;
  }
  .pos-cart-field-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.25px;
  }
  .pos-cart-price-input {
    background: #f8fafc;
    border-color: #94a3b8;
  }
  .pos-cart-mini-label,
  .pos-cart-max-discount,
  .pos-cart-line-total {
    font-size: 9px;
    font-weight: 700;
  }
  .pos-cart-mini-label,
  .pos-cart-max-discount {
    color: #64748b;
  }
  .pos-cart-line-total {
    color: #0f172a;
    white-space: nowrap;
  }
  .pos-cart-discount-input {
    width: 48px;
  }
  .pos-cart-row-stats {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-left: auto;
  }
  .pos-cart-checkout {
    width: 270px;
    padding: 4px;
    overflow-y: hidden;
    display: flex;
    flex-direction: column;
    gap: 3px;
  }
  #posCheckoutForm {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-height: 0;
    overflow: hidden;
  }
  /* ── Customer Compact Card ── */
  .pos-customer-compact {
    background: #fff;
    border: 1.5px solid #3b82f6;
    border-radius: 6px;
    padding: 4px 6px;
    display: flex;
    flex-direction: column;
    gap: 3px;
  }
  .pos-customer-title {
    font-size: 8px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #1d4ed8;
    margin-bottom: 0;
  }
  .pos-cust-input {
    width: 100%;
    padding: 3px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s;
    min-height: 24px;
  }
  .pos-cust-input:focus {
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.15);
  }
  .pos-cust-input::placeholder {
    color: #94a3b8;
    font-weight: 500;
  }
  .pos-cust-extra-phone {
    margin-top: 0;
  }
  .pos-cust-select {
    width: 100%;
    padding: 3px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    background: #f8fafc;
    cursor: pointer;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    padding-right: 20px;
    transition: border-color 0.15s;
    min-height: 24px;
  }
  .pos-cust-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.15);
  }
  .pos-cust-add-phone {
    font-size: 9px;
    font-weight: 700;
    color: #3b82f6;
    text-decoration: none;
    letter-spacing: 0.3px;
  }
  .pos-cust-add-phone:hover {
    color: #1d4ed8;
  }
  .pos-cart-checkout .form-group {
    margin: 0;
  }
  .pos-cart-checkout .form-stack,
  .pos-cart-checkout .form-grid {
    gap: 4px;
  }
  .pos-cart-checkout .mt-input,
  .pos-cart-checkout .mt-select,
  .pos-cart-checkout .mt-textarea {
    min-height: 24px;
    padding: 3px 6px;
    font-size: 11px;
  }
  .pos-note-field {
    min-height: 28px;
    height: 28px;
  }
  .pos-total-card {
    margin-top: 1px;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    border: 1.5px solid #2563eb;
    border-radius: 6px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
    min-height: 0;
    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.25);
  }
  .pos-total-caption {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    color: #93c5fd;
    letter-spacing: 0.5px;
  }
  .pos-total-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    font-size: 11px;
    color: #e0f2fe;
  }
  .pos-total-row strong {
    font-family: monospace;
    color: #ffffff;
    font-size: 12px;
  }
  .pos-total-discount-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .pos-total-input-label {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    color: #93c5fd;
    letter-spacing: 0.35px;
  }
  .pos-total-input {
    min-height: 24px !important;
    padding: 3px 6px !important;
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    background: rgba(0, 0, 0, 0.25) !important;
    color: #ffffff !important;
    font-size: 12px !important;
    font-weight: 700;
    text-align: right;
    border-radius: 4px;
    transition: all 0.2s;
  }
  .pos-total-input:focus {
    border-color: #ffffff !important;
    background: rgba(0, 0, 0, 0.35) !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2) !important;
  }
  .pos-total-final-row {
    margin-top: 2px;
    padding-top: 6px;
    border-top: 1.5px dashed rgba(255, 255, 255, 0.2);
    align-items: flex-end;
  }
  .pos-total-amount {
    font-size: 22px;
    font-weight: 900;
    color: #4ade80;
    line-height: 1.1;
    font-family: monospace;
    text-shadow: 0 0 8px rgba(74, 222, 128, 0.3);
  }
  .pos-total-profit-row {
    font-size: 11px;
    color: #cbd5e1;
  }
  .pos-total-profit-value {
    color: #4ade80 !important;
    font-family: monospace;
    font-weight: 700;
  }
  .pos-received-card {
    margin-top: 0;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    border: 1.5px solid #2563eb;
    border-radius: 6px;
    padding: 8px 10px;
    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.25);
  }
  .pos-received-label {
    display: block;
    margin-bottom: 4px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #ffffff;
  }
  .pos-received-input {
    min-height: 36px !important;
    padding: 6px 10px !important;
    border: 2px solid #ffffff !important;
    background: #ffffff !important;
    font-size: 18px !important;
    font-weight: 900;
    line-height: 1.1;
    text-align: center;
    color: #1e3a8a !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    transition: all 0.2s;
  }
  .pos-received-input:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5) !important;
  }
  .pos-received-input::placeholder {
    color: #94a3b8;
    font-weight: 600;
  }
  .pos-received-hint {
    margin-top: 4px;
    font-size: 8px;
    font-weight: 700;
    color: #e0f2fe;
    text-align: center;
    letter-spacing: 0.2px;
  }
  .pos-tender-card {
    margin-top: 0;
    background: rgba(15, 23, 42, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 6px;
    padding: 6px 8px;
  }
  .pos-tender-title {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    color: #93c5fd;
    letter-spacing: 0.5px;
  }
  .pos-tender-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3px;
    font-size: 11px;
    color: #cbd5e1;
  }
  .pos-checkout-actions {
    margin-top: auto !important;
    display: flex;
    align-items: center;
    gap: 4px;
    padding-top: 2px;
  }
  .pos-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid;
    border-radius: 4px;
    color: #fff;
    line-height: 1;
    transition: opacity 0.15s;
  }
  .pos-action-save {
    flex: 1;
    gap: 3px;
    min-height: 28px;
    padding: 3px 6px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: #1e293b;
    border-color: #0f172a;
  }
  .pos-action-icon {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    padding: 0;
  }
  .pos-action-all {
    width: 28px;
    height: 28px;
  }
  .pos-action-btn:hover {
    opacity: 0.8;
  }
  .pos-action-btn:active {
    opacity: 0.65;
    transform: scale(0.95);
  }
  #phoneToggleWrapper {
    margin-top: 2px;
  }
  #phoneToggleWrapper a {
    font-size: 10px !important;
  }
</style>

@endif
@endsection
