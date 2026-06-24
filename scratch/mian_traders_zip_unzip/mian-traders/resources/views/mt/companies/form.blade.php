@php
  $groupKey = old('group_key', $company->group_key ?? '');
  $isActive = (int)old('is_active', isset($company->is_active) ? (int)$company->is_active : 1) === 1;
@endphp

<div style="display:grid;gap:10px;grid-template-columns:1fr 1fr;">
  <div>
    <label style="font-size:12px;color:#6b7280;">Company Name</label>
    <input name="name" value="{{ old('name', $company->name ?? '') }}" required
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
  </div>

  <div>
    <label style="font-size:12px;color:#6b7280;">Company Type (Group)</label>
    <select name="group_key" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- Select --</option>
      <option value="foam" @selected($groupKey==='foam')>Foam</option>
      <option value="hardware" @selected($groupKey==='hardware')>Hardware</option>
      <option value="fabric" @selected($groupKey==='fabric')>Fabric</option>
      <option value="spring" @selected($groupKey==='spring')>Spring</option>
      <option value="accessories" @selected($groupKey==='accessories')>Accessories</option>
      <option value="other" @selected($groupKey==='other')>Other</option>
    </select>
  </div>
</div>

<div style="display:grid;gap:10px;grid-template-columns:1fr 1fr;">
  <div>
    <label style="font-size:12px;color:#6b7280;">Main Phone</label>
    <input name="phone_main" value="{{ old('phone_main', $company->phone_main ?? '') }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="e.g. 0300xxxxxxx">
  </div>

  <div>
    <label style="font-size:12px;color:#6b7280;">Email</label>
    <input name="email" value="{{ old('email', $company->email ?? '') }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="optional">
  </div>
</div>

<div>
  <label style="font-size:12px;color:#6b7280;">Address</label>
  <input name="address" value="{{ old('address', $company->address ?? '') }}"
         style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="Company address (optional)">
</div>

<div>
  <label style="font-size:12px;color:#6b7280;">Note</label>
  <textarea name="note" rows="3" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="Any notes about the company (optional)">{{ old('note', $company->note ?? '') }}</textarea>
</div>

<div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
  <label style="display:flex;gap:8px;align-items:center;">
    <input type="checkbox" name="is_active" value="1" @checked($isActive)>
    Active
  </label>
</div>

<div style="margin-top:14px;border-top:1px solid #eee;padding-top:12px;">
  <div style="font-weight:900;margin-bottom:6px;">Company Discount Rules (Purchase Cost)</div>
  <div style="color:#6b7280;font-size:12px;margin-bottom:10px;">
    These rules control how buying cost is calculated from MRP when you create <b>Company Orders</b> or <b>Purchases</b>.
    Example: Master Covered 12.5%, Style Hard = 25% + 5% (two-step).
  </div>

  <div style="overflow:auto;border:1px solid #eee;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;min-width:760px;">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Discount Type</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Rule</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Percent 1</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Percent 2</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Fixed Purchase</th>
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
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
              <div style="font-weight:800;">{{ $dt->name }}</div>
              @if($dt->description)
                <div style="font-size:12px;color:#6b7280;">{{ $dt->description }}</div>
              @endif
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
              <select name="discount_rules[{{ $dt->id }}][rule_type]" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:220px;">
                <option value="none" @selected($ruleType==='none')>No rule</option>
                <option value="percent_once" @selected($ruleType==='percent_once')>Percent (once)</option>
                <option value="percent_twostep" @selected($ruleType==='percent_twostep')>Percent (two-step)</option>
                <option value="fixed_purchase" @selected($ruleType==='fixed_purchase')>Fixed purchase price</option>
              </select>
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;">
              <input name="discount_rules[{{ $dt->id }}][percent_1]" value="{{ $p1 }}" type="number" step="0.01" placeholder="e.g. 12.5" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:140px;text-align:right;">
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;">
              <input name="discount_rules[{{ $dt->id }}][percent_2]" value="{{ $p2 }}" type="number" step="0.01" placeholder="e.g. 5" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:140px;text-align:right;">
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;">
              <input name="discount_rules[{{ $dt->id }}][fixed_purchase_price]" value="{{ $fx }}" type="number" step="0.01" placeholder="optional" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:160px;text-align:right;">
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@if($errors->any())
  <div style="margin-top:10px;background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
    <ul style="margin:0;padding-left:18px;">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif
