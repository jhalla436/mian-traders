@extends('mt.layouts.app')

@section('content')
@php
  $prefillCompany = $prefillCompany ?? null;
@endphp

  <div class="page-header">
    <h2 class="page-title">New Purchase (Stock In)</h2>
    <a href="{{ route('mt.purchases.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <form method="POST" action="{{ route('mt.purchases.store') }}" class="card">
    @csrf

    <div class="flex gap-12 flex-wrap items-end" style="margin-bottom:16px;">
      <div class="form-group">
        <label class="form-label">Purchase Date</label>
        <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" required class="mt-input">
        @error('purchase_date') <div class="form-error">{{ $message }}</div> @enderror
      </div>
      <div class="form-group">
        <label class="form-label">Company (optional)</label>
        <select id="company_id" name="company_id" class="mt-select" style="min-width:260px;" onchange="recalcAllCosts()">
          <option value="">-- Not a company --</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected((string)old('company_id', (string)$prefillCompany) === (string)$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        @error('company_id') <div class="form-error">{{ $message }}</div> @enderror
      </div>
      <div class="form-group">
        <label class="form-label">Local Supplier Name</label>
        <input name="supplier_name" value="{{ old('supplier_name') }}" placeholder="If not company" class="mt-input" style="min-width:220px;">
        @error('supplier_name') <div class="form-error">{{ $message }}</div> @enderror
      </div>
      <div class="form-group">
        <label class="form-label">Invoice #</label>
        <input name="invoice_no" value="{{ old('invoice_no') }}" class="mt-input" style="min-width:160px;">
        @error('invoice_no') <div class="form-error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="flex gap-12 flex-wrap items-end" style="margin-bottom:16px;">
      <div class="form-group">
        <label class="form-label">Transport Charges (we paid)</label>
        <input type="number" step="0.01" name="transport_charges" value="{{ old('transport_charges', 0) }}" class="mt-input">
        @error('transport_charges') <div class="form-error">{{ $message }}</div> @enderror
        <span class="text-xs text-muted">Added as <b>credit</b> in Company Ledger.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Payment made to company</label>
        <input type="number" step="0.01" name="payment_made" value="{{ old('payment_made', 0) }}" class="mt-input">
        @error('payment_made') <div class="form-error">{{ $message }}</div> @enderror
      </div>
      <div class="form-group" style="flex:1;min-width:260px;">
        <label class="form-label">Note</label>
        <input name="note" value="{{ old('note') }}" class="mt-input" placeholder="Optional">
        @error('note') <div class="form-error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="table-card" style="margin-bottom:16px;">
      <div style="padding:12px 16px;background:#f8fafc;border-bottom:2px solid #e2e8f0;font-weight:800;font-size:14px;">Items</div>
      <table class="mt-table">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Line Total</th>
            <th class="text-right">#</th>
          </tr>
        </thead>
        <tbody id="lines">
          <tr>
            <td>
              <select name="product_id[]" class="mt-select" onchange="calcCostForRow(this)">
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </td>
            <td class="text-right">
              <input name="qty[]" type="number" step="0.01" value="1" class="mt-input" style="width:100px;text-align:right;" oninput="recalc()">
            </td>
            <td class="text-right">
              <input name="unit_cost[]" type="number" step="0.01" value="0" class="mt-input" style="width:120px;text-align:right;" oninput="recalc()">
              <div class="text-xs text-muted" style="margin-top:2px;">Auto-loads from discount rules.</div>
            </td>
            <td class="text-right font-bold line_total">0.00</td>
            <td class="text-right">
              <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-xs">✕</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="padding:12px 16px;display:flex;gap:12px;justify-content:space-between;align-items:center;flex-wrap:wrap;border-top:1px solid #e2e8f0;">
        <button type="button" onclick="addRow()" class="btn btn-primary btn-sm">+ Add Line</button>
        <div class="font-bold" style="font-size:15px;">Goods Total: <span id="goodsTotal" style="color:#6366f1;">0.00</span></div>
      </div>
    </div>

    @error('product_id') <div class="form-error" style="margin-bottom:12px;">{{ $message }}</div> @enderror

    <button class="btn btn-success">Save Purchase & Stock In</button>
  </form>

  <script>
    function fmt(n){ return (Math.round((n + Number.EPSILON)*100)/100).toFixed(2); }
    async function fetchCost(companyId, productId){
      const url = `{{ route('mt.purchases.calc_cost') }}?company_id=${encodeURIComponent(companyId)}&product_id=${encodeURIComponent(productId)}`;
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
    function addRow(){
      const tbody = document.getElementById('lines');
      const first = tbody.querySelector('tr');
      const clone = first.cloneNode(true);
      clone.querySelector('input[name="qty[]"]').value = 1;
      clone.querySelector('input[name="unit_cost[]"]').value = 0;
      clone.querySelector('.line_total').textContent = '0.00';
      clone.querySelector('select[name="product_id[]"]').onchange = function(){ calcCostForRow(this); };
      tbody.appendChild(clone);
      recalcAllCosts();
    }
    async function calcCostForRow(sel){
      const tr = sel.closest('tr');
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      try {
        const cost = await fetchCost(companyId, sel.value);
        tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost);
        recalc();
      } catch(e){ console.error(e); recalc(); }
    }
    async function recalcAllCosts(){
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      for (const tr of document.querySelectorAll('#lines tr')){
        const pid = tr.querySelector('select[name="product_id[]"]').value;
        try { const cost = await fetchCost(companyId, pid); tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost); } catch(e){ console.error(e); }
      }
      recalc();
    }
    function removeRow(btn){
      const tbody = document.getElementById('lines');
      if (tbody.querySelectorAll('tr').length <= 1) { alert('At least 1 line is required.'); return; }
      btn.closest('tr').remove(); recalc();
    }
    recalc(); setTimeout(recalcAllCosts, 100);
  </script>
@endsection
