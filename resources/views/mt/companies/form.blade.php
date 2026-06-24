@php
  $groupKey = old('group_key', $company->group_key ?? '');
  $isActive = (int)old('is_active', isset($company->is_active) ? (int)$company->is_active : 1) === 1;
@endphp

<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Company Name</label>
    <input name="name" value="{{ old('name', $company->name ?? '') }}" required class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Company Type (Group)</label>
    <select name="group_key" class="mt-select">
      <option value="">-- Select --</option>
      <option value="foam" @selected($groupKey==='foam')>Foam</option>
      <option value="uncovered_foam" @selected($groupKey==='uncovered_foam')>Uncovered Foam</option>
      <option value="hardware" @selected($groupKey==='hardware')>Hardware</option>
      <option value="fabric" @selected($groupKey==='fabric')>Fabric</option>
      <option value="spring" @selected($groupKey==='spring')>Spring</option>
      <option value="accessories" @selected($groupKey==='accessories')>Accessories</option>
      <option value="other" @selected($groupKey==='other')>Other</option>
    </select>
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Main Phone</label>
    <input name="phone_main" value="{{ old('phone_main', $company->phone_main ?? '') }}" class="mt-input" placeholder="e.g. 0300xxxxxxx">
  </div>
  <div class="form-group">
    <label class="form-label">Email</label>
    <input name="email" value="{{ old('email', $company->email ?? '') }}" class="mt-input" placeholder="optional">
  </div>
</div>

<div class="form-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
  <div class="form-group">
    <label class="form-label">Max POS Discount %</label>
    <input name="max_discount_percent" type="number" step="0.01" min="0" max="100" value="{{ old('max_discount_percent', $company->max_discount_percent ?? 0) }}" class="mt-input">
    @error('max_discount_percent')<div class="form-error">{{ $message }}</div>@enderror
  </div>
  <div class="form-group">
    <label class="form-label">Default Original Discount %</label>
    <input name="default_original_discount" type="number" step="0.01" min="0" max="100" value="{{ old('default_original_discount', $company->default_original_discount ?? 0) }}" class="mt-input">
    @error('default_original_discount')<div class="form-error">{{ $message }}</div>@enderror
  </div>
  <div class="form-group">
    <label class="form-label">Default Extra Discount %</label>
    <input name="default_extra_discount" type="number" step="0.01" min="0" max="100" value="{{ old('default_extra_discount', $company->default_extra_discount ?? 0) }}" class="mt-input">
    @error('default_extra_discount')<div class="form-error">{{ $message }}</div>@enderror
  </div>
  <div class="form-group">
    <label class="form-label">Default Lamination Rate (MRP)</label>
    <input name="default_lamination_rate" type="number" step="0.01" min="0" value="{{ old('default_lamination_rate', $company->default_lamination_rate ?? '') }}" class="mt-input" placeholder="e.g. 4000">
    @error('default_lamination_rate')<div class="form-error">{{ $message }}</div>@enderror
  </div>
</div>
<div class="text-xs text-muted" style="margin-top: 4px; margin-bottom: 16px; line-height:1.4;">
  • <b>Max POS Discount %</b> is the maximum retail discount limit for sales cashiers.<br>
  • <b>Default Original & Extra Discount %</b> will automatically prefill when creating new purchase orders for this company.<br>
  • <b>Default Lamination Rate (MRP)</b> will automatically apply as the base price (mrp) when importing or adding lamination sheets for this company without a price.
</div>

<div class="form-group">
  <label class="form-label">Address</label>
  <input name="address" value="{{ old('address', $company->address ?? '') }}" class="mt-input" placeholder="Company address (optional)">
</div>

<div class="form-group">
  <label class="form-label">Note</label>
  <textarea name="note" rows="3" class="mt-input" style="resize:vertical;" placeholder="Any notes about the company (optional)">{{ old('note', $company->note ?? '') }}</textarea>
</div>

<label class="form-checkbox">
  <input type="checkbox" name="is_active" value="1" @checked($isActive)>
  Active
</label>

<hr class="separator">

<div>
  <div class="font-bold" style="font-size:15px;margin-bottom:4px;">Company Discount Rules (Purchase Cost)</div>
  <div class="text-xs text-muted" style="margin-bottom:14px;">
    These rules control how buying cost is calculated from MRP when you create <b>Company Orders</b> or <b>Purchases</b>.
    Example: Master Covered 12.5%, Style Hard = 25% + 5% (two-step).
  </div>

  <div class="table-card" style="overflow:auto;">
    <table class="mt-table" style="min-width:760px;">
      <thead>
        <tr>
          <th>Discount Type</th>
          <th>Rule</th>
          <th class="text-right">Percent 1</th>
          <th class="text-right">Percent 2</th>
          <th class="text-right">Fixed Purchase</th>
        </tr>
      </thead>
      <tbody>
        @foreach($discountTypes as $dt)
          @php
            $r = $companyDiscountRules[$dt->id] ?? null;
            $baseKey = "discount_rules.{$dt->id}.";
            $ruleType = old($baseKey.'rule_type', $r->rule_type ?? 'none');
            $p1 = old($baseKey.'percent_1', $r->percent_1 ?? '');
            $p2 = old($baseKey.'percent_2', $r->percent_2 ?? '');
            $fx = old($baseKey.'fixed_purchase_price', $r->fixed_purchase_price ?? '');
          @endphp
          <tr>
            <td>
              <div class="font-bold">{{ $dt->name }}</div>
              @if($dt->description)
                <div class="text-xs text-muted">{{ $dt->description }}</div>
              @endif
            </td>
            <td>
              <select name="discount_rules[{{ $dt->id }}][rule_type]" class="mt-select" style="min-width:200px;">
                <option value="none" @selected($ruleType==='none')>No rule</option>
                <option value="percent_once" @selected($ruleType==='percent_once')>Percent (once)</option>
                <option value="percent_twostep" @selected($ruleType==='percent_twostep')>Percent (two-step)</option>
                <option value="fixed_purchase" @selected($ruleType==='fixed_purchase')>Fixed purchase price</option>
              </select>
            </td>
            <td class="text-right">
              <input name="discount_rules[{{ $dt->id }}][percent_1]" value="{{ $p1 }}" type="number" step="0.01" placeholder="e.g. 12.5" class="mt-input" style="width:120px;text-align:right;">
            </td>
            <td class="text-right">
              <input name="discount_rules[{{ $dt->id }}][percent_2]" value="{{ $p2 }}" type="number" step="0.01" placeholder="e.g. 5" class="mt-input" style="width:120px;text-align:right;">
            </td>
            <td class="text-right">
              <input name="discount_rules[{{ $dt->id }}][fixed_purchase_price]" value="{{ $fx }}" type="number" step="0.01" placeholder="optional" class="mt-input" style="width:140px;text-align:right;">
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@if($errors->any())
  <div class="errors-box">
    <b>Fix errors:</b>
    <ul>
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif
