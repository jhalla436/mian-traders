@extends('mt.layouts.app')

@section('content')
@php
  $prefillCompany = $prefillCompany ?? null;
@endphp

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">New Company Order</h2>
    <a href="{{ route('mt.company_orders.index') }}" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <form method="POST" action="{{ route('mt.company_orders.store') }}" style="background:#fff;padding:14px;border-radius:10px;">
    @csrf

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Order Date</div>
        <input type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        @error('order_date') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">Company</div>
        <select id="company_id" name="company_id" required style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:280px;" onchange="recalcAllCosts()">
          <option value="">-- Select Company --</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected((string)old('company_id', (string)$prefillCompany) === (string)$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        @error('company_id') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>

      <div style="flex:1;min-width:280px;">
        <div style="font-size:12px;color:#6b7280;">Note</div>
        <input name="note" value="{{ old('note') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Optional">
        @error('note') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
    </div>

    <div style="border:1px solid #eee;border-radius:10px;overflow:hidden;margin-bottom:12px;">
      <div style="background:#f9fafb;padding:10px;font-weight:800;">Items</div>
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Unit Cost (auto)</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Line Total</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">#</th>
          </tr>
        </thead>
        <tbody id="lines">
          <tr>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;">
              <select name="product_id[]" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" onchange="calcCostForRow(this)">
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <input name="qty[]" type="number" step="0.01" value="1" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:120px;text-align:right;" oninput="recalc()">
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <input name="unit_cost[]" type="number" step="0.01" value="0" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:140px;text-align:right;" oninput="recalc()">
              <div style="font-size:11px;color:#6b7280;margin-top:4px;">Auto from company discount (you can edit).</div>
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;font-weight:800;" class="line_total">0.00</td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <button type="button" onclick="removeRow(this)" style="padding:8px 10px;border-radius:10px;background:#ef4444;color:#fff;border:0;">X</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="padding:10px;display:flex;gap:10px;justify-content:space-between;align-items:center;flex-wrap:wrap;">
        <button type="button" onclick="addRow()" style="padding:10px 12px;border-radius:10px;background:#2563eb;color:#fff;border:0;">+ Add Line</button>
        <div style="font-weight:900;">Goods Total: <span id="goodsTotal">0.00</span></div>
      </div>
    </div>

    @error('product_id') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror

    <button style="padding:12px 14px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;">Save Order</button>
  </form>

  <script>
    function fmt(n){ return (Math.round((n + Number.EPSILON)*100)/100).toFixed(2); }

    async function fetchCost(companyId, productId){
      const url = `{{ route('mt.company_orders.calc_cost') }}?company_id=${encodeURIComponent(companyId)}&product_id=${encodeURIComponent(productId)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const js = await res.json();
      if (!js.ok) throw new Error(js.message || 'Cost fetch failed');
      return parseFloat(js.unit_cost || 0);
    }

    function recalc(){
      let goods = 0;
      document.querySelectorAll('#lines tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('input[name="qty[]"]').value || '0');
        const cost = parseFloat(tr.querySelector('input[name="unit_cost[]"]').value || '0');
        const total = qty*cost;
        tr.querySelector('.line_total').textContent = fmt(total);
        goods += total;
      });
      document.getElementById('goodsTotal').textContent = fmt(goods);
    }

    async function calcCostForRow(sel){
      const tr = sel.closest('tr');
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      try {
        const cost = await fetchCost(companyId, sel.value);
        tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost);
        recalc();
      } catch(e){
        console.error(e);
        recalc();
      }
    }

    async function recalcAllCosts(){
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      for (const tr of document.querySelectorAll('#lines tr')){
        const pid = tr.querySelector('select[name="product_id[]"]').value;
        try {
          const cost = await fetchCost(companyId, pid);
          tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost);
        } catch(e){ console.error(e); }
      }
      recalc();
    }

    function addRow(){
      const tbody = document.getElementById('lines');
      const first = tbody.querySelector('tr');
      const clone = first.cloneNode(true);
      clone.querySelector('input[name="qty[]"]').value = 1;
      clone.querySelector('input[name="unit_cost[]"]').value = 0;
      clone.querySelector('.line_total').textContent = '0.00';
      // rebind onchange
      clone.querySelector('select[name="product_id[]"]').onchange = function(){ calcCostForRow(this); };
      tbody.appendChild(clone);
      recalcAllCosts();
    }

    function removeRow(btn){
      const tbody = document.getElementById('lines');
      if (tbody.querySelectorAll('tr').length <= 1) {
        alert('At least 1 line is required.');
        return;
      }
      btn.closest('tr').remove();
      recalc();
    }

    recalc();
    // If company prefilled, auto-fill first row cost
    setTimeout(recalcAllCosts, 100);
  </script>
@endsection
