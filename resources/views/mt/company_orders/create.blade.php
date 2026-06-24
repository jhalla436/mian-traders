@extends('mt.layouts.app')

@section('content')
@php
  $prefillCompany = $prefillCompany ?? null;
@endphp

  <div class="page-header">
    <div>
      <h2 class="page-title">New Company Order</h2>
      <div class="page-subtitle">Select a company to load their product list in an Excel-like interactive sheet.</div>
    </div>
    <a href="{{ route('mt.company_orders.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <form id="order_form" method="POST" action="{{ route('mt.company_orders.store') }}" class="form-stack">
    @csrf

    <div class="card">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Order Date</label>
          <input type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" required class="mt-input">
          @error('order_date') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label class="form-label">Company</label>
          <select id="company_id" name="company_id" required class="mt-select" onchange="loadCompanyProducts(this.value)">
            <option value="">-- Select Company --</option>
            @foreach($companies as $c)
              <option value="{{ $c->id }}" @selected((string)old('company_id', (string)$prefillCompany) === (string)$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
          @error('company_id') <div class="form-error">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="form-group" style="margin-top: 12px;">
        <label class="form-label">Note</label>
        <input name="note" value="{{ old('note') }}" class="mt-input" placeholder="Optional notes for this purchase order">
        @error('note') <div class="form-error">{{ $message }}</div> @enderror
      </div>
    </div>

    <!-- Excel-like Grid Card -->
    <div class="card" id="grid_card" style="display: none; padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
      <div style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 16px 20px;">
        <div class="flex justify-between items-center flex-wrap gap-12">
          <h3 style="margin:0; font-size:16px; font-weight:800; color:#0f172a;" class="flex items-center gap-8">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            Interactive Order Grid
          </h3>
          
          <div class="flex gap-12 items-center flex-wrap">
            <div class="form-checkbox" style="user-select: none;">
              <input type="checkbox" id="chk_selected_only" onchange="applyFilters()">
              <span>Show Selected Only (Qty &gt; 0)</span>
            </div>

            <button type="button" class="btn btn-danger btn-xs" style="padding: 6px 12px;" onclick="clearEntireOrder()">
              ✕ Clear Quantities
            </button>
            
            <div style="height: 20px; width: 1px; background: #cbd5e1;"></div>
            
            <div class="flex items-center gap-8">
              <span class="text-xs font-bold text-muted">Order Discounts (%):</span>
              <input type="number" id="global_orig_disc" placeholder="Original" class="mt-input text-right" style="width: 75px; padding: 6px 10px;" min="0" max="100" step="0.01">
              <span class="text-muted">+</span>
              <input type="number" id="global_extra_disc" placeholder="Extra" class="mt-input text-right" style="width: 75px; padding: 6px 10px;" min="0" max="100" step="0.01">
              <button type="button" class="btn btn-teal btn-sm" style="padding: 6px 12px;" onclick="applyGlobalDiscounts()">Apply to All</button>
            </div>
          </div>
        </div>

        <!-- Filters Bar inside grid -->
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; margin-top: 14px;">
          <input type="text" id="grid_search" class="mt-input" placeholder="🔍 Quick search products by name or SKU..." oninput="applyFilters()">
          <select id="grid_cat_filter" class="mt-select" onchange="applyFilters()">
            <option value="">-- All Categories --</option>
          </select>
        </div>
      </div>

      <!-- Scrollable Grid Body -->
      <div style="max-height: 550px; overflow-y: auto;">
        <table class="mt-table" style="margin: 0; width: 100%;">
          <thead style="position: sticky; top: 0; z-index: 10;">
            <tr>
              <th style="width: 180px;">Category</th>
              <th>Product & SKU</th>
              <th class="text-right" style="width: 140px;">Base Price (Rs.)</th>
              <th class="text-right" style="width: 110px;">Orig. Disc %</th>
              <th class="text-right" style="width: 110px;">Extra Disc %</th>
              <th class="text-right" style="width: 110px;">Order Qty</th>
              <th class="text-right" style="width: 120px;">Net Unit Cost</th>
              <th class="text-right" style="width: 140px;">Line Total (Rs.)</th>
            </tr>
          </thead>
          <tbody id="grid_body">
            <!-- Loaded dynamically -->
          </tbody>
        </table>
      </div>

      <div style="padding: 16px 20px; background: #f8fafc; border-top: 2px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="font-size: 13px; color: #64748b;">
          Total Products loaded: <span id="lbl_total_count" class="font-bold" style="color:#0f172a;">0</span> | 
          Selected: <span id="lbl_selected_count" class="font-bold" style="color:#22c55e;">0</span> products (<span id="lbl_total_items" class="font-bold" style="color:#4f46e5;">0</span> items)
        </div>
        <div style="display: flex; gap: 24px; align-items: center;">
          <div style="text-align: right;">
            <div style="font-size: 11px; color:#64748b; font-weight:700; text-transform:uppercase;">Goods Total</div>
            <div style="font-size: 24px; font-weight:900; color: #4f46e5;">Rs. <span id="lbl_goods_total">0.00</span></div>
          </div>
          <button type="submit" class="btn btn-success btn-sm" style="padding: 12px 24px; font-size:14px; border-radius:10px;">Save Order</button>
        </div>
      </div>
    </div>

    <!-- Blank State -->
    <div class="card text-center" id="grid_blank" style="padding: 60px 20px;">
      <div style="font-size: 40px; margin-bottom: 12px;">📋</div>
      <h3 style="font-weight: 800; color: #475569; margin: 0 0 6px;">No Company Selected</h3>
      <p style="color: #94a3b8; font-size: 13px; margin: 0;">Please select a company above to load its sheet-like product order grid.</p>
    </div>

    <!-- Loading State -->
    <div class="card text-center" id="grid_loading" style="display: none; padding: 60px 20px;">
      <div class="btn-role-dot" style="width: 32px; height: 32px; background: #6366f1; border-radius:50%; margin: 0 auto 16px; animation: pulse-dot 1s infinite;"></div>
      <h3 style="font-weight: 800; color: #475569; margin: 0 0 6px;">Loading Product List...</h3>
      <p style="color: #94a3b8; font-size: 13px; margin: 0;">Fetching matching products, sizes and active discount rules.</p>
    </div>

    <input type="hidden" name="global_original_discount" id="hid_global_orig_disc" value="0">
    <input type="hidden" name="global_extra_discount" id="hid_global_extra_disc" value="0">

  </form>

  <script>
    let rawProducts = [];

    function fmt(n) { return (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2); }

    async function loadCompanyProducts(companyId) {
      const blank = document.getElementById('grid_blank');
      const loader = document.getElementById('grid_loading');
      const card = document.getElementById('grid_card');
      const tbody = document.getElementById('grid_body');
      
      if (!companyId) {
        blank.style.display = 'block';
        loader.style.display = 'none';
        card.style.display = 'none';
        rawProducts = [];
        return;
      }

      blank.style.display = 'none';
      loader.style.display = 'block';
      card.style.display = 'none';

      try {
        const url = `{{ route('mt.company_orders.products') }}?company_id=${encodeURIComponent(companyId)}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
        const js = await res.json();
        
        if (!js.ok) {
          throw new Error('Could not fetch company products.');
        }

        rawProducts = js.products || [];
        
        // Prefill global discount inputs from company level defaults
        document.getElementById('global_orig_disc').value = js.default_original_discount || '';
        document.getElementById('global_extra_disc').value = js.default_extra_discount || '';
        document.getElementById('hid_global_orig_disc').value = js.default_original_discount || 0;
        document.getElementById('hid_global_extra_disc').value = js.default_extra_discount || 0;

        // Populate Categories filter
        const catFilter = document.getElementById('grid_cat_filter');
        catFilter.innerHTML = '<option value="">-- All Categories --</option>';
        const uniqueCats = [...new Set(rawProducts.map(p => p.category_name))].sort();
        uniqueCats.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c;
          opt.textContent = c;
          catFilter.appendChild(opt);
        });

        // Render rows
        renderGrid();

        loader.style.display = 'none';
        card.style.display = 'block';

      } catch (e) {
        console.error(e);
        alert('Failed to load products: ' + e.message);
        loader.style.display = 'none';
        blank.style.display = 'block';
      }
    }

    function renderGrid() {
      const tbody = document.getElementById('grid_body');
      tbody.innerHTML = '';
      
      if (rawProducts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted" style="padding: 40px 10px;">This company has no products registered.</td></tr>';
        document.getElementById('lbl_total_count').textContent = '0';
        return;
      }

      document.getElementById('lbl_total_count').textContent = rawProducts.length;

      const globalOrig = parseFloat(document.getElementById('global_orig_disc').value) || 0;
      const globalExtra = parseFloat(document.getElementById('global_extra_disc').value) || 0;

      rawProducts.forEach(p => {
        const tr = document.createElement('tr');
        tr.dataset.productId = p.id;
        tr.dataset.productName = p.name.toLowerCase();
        tr.dataset.productSku = (p.sku || '').toLowerCase();
        tr.dataset.categoryName = p.category_name;
        tr.dataset.basePrice = p.base_price;
        tr.className = 'grid-product-row';

        const isShell = p.pricing_mode === 'shell';
        const priceInfo = isShell 
          ? `<span class="badge badge-info" style="font-size:10px; padding:2px 6px;">cft</span> ${fmt(p.base_price)}<br><span class="text-xs text-muted">(${p.dimensions}) cft: ${p.cft}</span>`
          : `${fmt(p.base_price)}<br><span class="text-xs text-muted">MRP</span>`;

        const rowOrig = p.default_original_discount > 0 ? p.default_original_discount : globalOrig;
        const rowExtra = p.default_extra_discount > 0 ? p.default_extra_discount : globalExtra;

        tr.innerHTML = `
          <td style="color:#64748b; font-size:12px;">${p.category_name}</td>
          <td>
            <input type="hidden" name="product_id[]" value="${p.id}">
            <div class="font-bold" style="color:#0f172a;">${p.name}</div>
            <div class="text-xs text-muted">SKU: ${p.sku || '-'}</div>
          </td>
          <td class="text-right">${priceInfo}</td>
          <td>
            <input type="number" name="original_discount[]" value="${rowOrig || 0}" step="0.01" min="0" max="100" class="mt-input text-right row-orig-disc" style="width: 80px; padding: 6px;" oninput="recalcRow(this)">
          </td>
          <td>
            <input type="number" name="extra_discount[]" value="${rowExtra || 0}" step="0.01" min="0" max="100" class="mt-input text-right row-extra-disc" style="width: 80px; padding: 6px;" oninput="recalcRow(this)">
          </td>
          <td>
            <input type="number" name="qty[]" step="0.01" min="0" placeholder="0" class="mt-input text-right row-qty" style="width: 90px; padding: 6px; font-weight:700;" oninput="recalcRow(this)">
          </td>
          <td class="text-right">
            <input type="hidden" name="unit_cost[]" class="row-net-cost-val" value="0">
            <strong>Rs. <span class="row-net-cost-lbl">0.00</span></strong>
          </td>
          <td class="text-right">
            <strong style="color:#4f46e5;">Rs. <span class="row-line-total-lbl">0.00</span></strong>
          </td>
        `;

        tbody.appendChild(tr);
        recalcRowFields(tr);
      });

      applyFilters();
    }

    function recalcRow(el) {
      const tr = el.closest('tr');
      recalcRowFields(tr);
      recalcGrandTotal();
    }

    function recalcRowFields(tr) {
      const basePrice = parseFloat(tr.dataset.basePrice || 0);
      const orig = parseFloat(tr.querySelector('.row-orig-disc').value || 0);
      const extra = parseFloat(tr.querySelector('.row-extra-disc').value || 0);
      const qty = parseFloat(tr.querySelector('.row-qty').value || 0);

      // Sequential discount calculation
      let netCost = basePrice;
      if (orig > 0) netCost = netCost * (1 - (orig / 100));
      if (extra > 0) netCost = netCost * (1 - (extra / 100));

      const lineTotal = netCost * qty;

      // Update UI elements
      tr.querySelector('.row-net-cost-val').value = netCost;
      tr.querySelector('.row-net-cost-lbl').textContent = fmt(netCost);
      tr.querySelector('.row-line-total-lbl').textContent = fmt(lineTotal);

      // Highlight selected rows
      if (qty > 0) {
        tr.style.background = '#f0fdf4';
      } else {
        tr.style.background = '';
      }
    }

    function recalcGrandTotal() {
      let goodsTotal = 0;
      let selectedCount = 0;
      let totalItems = 0;

      document.querySelectorAll('#grid_body tr.grid-product-row').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.row-qty').value || 0);
        const basePrice = parseFloat(tr.dataset.basePrice || 0);
        const orig = parseFloat(tr.querySelector('.row-orig-disc').value || 0);
        const extra = parseFloat(tr.querySelector('.row-extra-disc').value || 0);

        let netCost = basePrice;
        if (orig > 0) netCost = netCost * (1 - (orig / 100));
        if (extra > 0) netCost = netCost * (1 - (extra / 100));

        if (qty > 0) {
          goodsTotal += netCost * qty;
          selectedCount++;
          totalItems += qty;
        }
      });

      document.getElementById('lbl_goods_total').textContent = fmt(goodsTotal);
      document.getElementById('lbl_selected_count').textContent = selectedCount;
      document.getElementById('lbl_total_items').textContent = totalItems % 1 === 0 ? totalItems : fmt(totalItems);
    }

    function applyFilters() {
      const q = document.getElementById('grid_search').value.toLowerCase().trim();
      const cat = document.getElementById('grid_cat_filter').value;
      const selectedOnly = document.getElementById('chk_selected_only').checked;

      document.querySelectorAll('#grid_body tr.grid-product-row').forEach(tr => {
        const name = tr.dataset.productName;
        const sku = tr.dataset.productSku;
        const category = tr.dataset.categoryName;
        const qty = parseFloat(tr.querySelector('.row-qty').value || 0);

        let matchQ = true;
        if (q) {
          if (q.includes('*')) {
            const escaped = q.replace(/[-\/\\^$+?.()|[\]{}]/g, '\\$&').replace(/\*/g, '.*');
            const regex = new RegExp(escaped);
            matchQ = regex.test(name) || regex.test(sku);
          } else {
            matchQ = name.includes(q) || sku.includes(q);
          }
        }

        let matchCat = !cat || category === cat;
        let matchSel = !selectedOnly || qty > 0;

        if (matchQ && matchCat && matchSel) {
          tr.style.display = '';
        } else {
          tr.style.display = 'none';
        }
      });
    }

    function applyGlobalDiscounts() {
      const orig = document.getElementById('global_orig_disc').value;
      const extra = document.getElementById('global_extra_disc').value;
      
      document.getElementById('hid_global_orig_disc').value = orig || 0;
      document.getElementById('hid_global_extra_disc').value = extra || 0;

      if (!orig && !extra) {
        alert('Please fill in at least one discount field first.');
        return;
      }

      if (!confirm('This will update the discount rates for all items in the grid. Continue?')) {
        return;
      }

      document.querySelectorAll('#grid_body tr.grid-product-row').forEach(tr => {
        if (orig !== '') {
          tr.querySelector('.row-orig-disc').value = orig;
        }
        if (extra !== '') {
          tr.querySelector('.row-extra-disc').value = extra;
        }
        recalcRowFields(tr);
      });

      recalcGrandTotal();
    }

    function clearEntireOrder() {
      if (!confirm('Are you sure you want to clear all product quantities and reset this order?')) {
        return;
      }

      document.querySelectorAll('#grid_body tr.grid-product-row').forEach(tr => {
        tr.querySelector('.row-qty').value = '';
        recalcRowFields(tr);
      });

      recalcGrandTotal();
      applyFilters();
    }

    // Form submit optimization to prevent max_input_vars limit
    document.getElementById('order_form').addEventListener('submit', function (e) {
      let selectedCount = 0;

      document.querySelectorAll('#grid_body tr.grid-product-row').forEach(tr => {
        const qtyVal = parseFloat(tr.querySelector('.row-qty').value || 0);
        if (qtyVal <= 0) {
          tr.querySelectorAll('input').forEach(input => {
            input.disabled = true;
          });
        } else {
          selectedCount++;
        }
      });

      if (selectedCount === 0) {
        e.preventDefault();
        alert('Please enter a quantity greater than 0 for at least one product.');

        // Re-enable all inputs so they can make changes
        document.querySelectorAll('#grid_body tr.grid-product-row input').forEach(input => {
          input.disabled = false;
        });
      }
    });

    // Prefill triggers
    document.addEventListener('DOMContentLoaded', () => {
      const val = document.getElementById('company_id').value;
      if (val) {
        loadCompanyProducts(val);
      }
    });
  </script>
@endsection
