@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
@endphp

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Name</div>
    <input name="name" value="{{ old('name', $product->name) }}" required
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('name')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">SKU (optional)</div>
    <input name="sku" value="{{ old('sku', $product->sku) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('sku')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Company</div>
    <select name="company_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      @foreach($companies as $co)
        <option value="{{ $co->id }}" @selected((int)old('company_id', $product->company_id) === (int)$co->id)>{{ $co->name }}</option>
      @endforeach
    </select>
    @error('company_id')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Category</div>
    <select name="category_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected((int)old('category_id', $product->category_id) === (int)$cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
    @error('category_id')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Discount Type</div>
    <select name="discount_type_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      @foreach($discountTypes as $dt)
        <option value="{{ $dt->id }}" @selected((int)old('discount_type_id', $product->discount_type_id) === (int)$dt->id)>{{ $dt->name }}</option>
      @endforeach
    </select>
    @error('discount_type_id')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Active</div>
    <label style="display:flex;gap:8px;align-items:center;">
      <input type="checkbox" name="is_active" value="1" @checked((int)old('is_active', $product->is_active ?? 1) === 1)>
      <span>Yes</span>
    </label>
  </div>

  {{-- ✅ pricing_mode REQUIRED (fixes your crash) --}}
  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Pricing Mode</div>
    <select name="pricing_mode" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="manual" @selected(old('pricing_mode', $product->pricing_mode ?? 'manual') === 'manual')>Manual</option>
      <option value="shell" @selected(old('pricing_mode', $product->pricing_mode) === 'shell')>Shell (w*l*h/144 * shell_rate)</option>
    </select>
    @error('pricing_mode')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Shell Rate (only for shell mode)</div>
    <input name="shell_rate" type="number" step="0.01" min="0"
           value="{{ old('shell_rate', $product->shell_rate) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('shell_rate')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Width (inches)</div>
    <input name="width_in" type="number" step="0.01" min="0"
           value="{{ old('width_in', $product->width_in) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('width_in')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Length (inches)</div>
    <input name="length_in" type="number" step="0.01" min="0"
           value="{{ old('length_in', $product->length_in) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('length_in')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Height (inches)</div>
    <input name="height_in" type="number" step="0.01" min="0"
           value="{{ old('height_in', $product->height_in) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('height_in')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">MRP</div>
    <input name="mrp" type="number" step="0.01" min="0"
           value="{{ old('mrp', $product->mrp) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('mrp')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Selling Price</div>
    <input name="selling_price_default" type="number" step="0.01" min="0"
           value="{{ old('selling_price_default', $product->selling_price_default) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('selling_price_default')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  {{-- ✅ buying hidden for cashier --}}
  @if($canSeeCost)
    <div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Manual Purchase Price (Optional)</div>
      <input name="purchase_price_manual" type="number" step="0.01" min="0"
             value="{{ old('purchase_price_manual', $product->purchase_price_manual) }}"
             style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      @error('purchase_price_manual')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
    </div>
  @endif

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Stock Qty</div>
    <input name="stock_qty" type="number" step="0.01"
           value="{{ old('stock_qty', $product->stock_qty) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('stock_qty')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Low Stock Alert Qty</div>
    <input name="low_stock_alert_qty" type="number" step="0.01" min="0"
           value="{{ old('low_stock_alert_qty', $product->low_stock_alert_qty) }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('low_stock_alert_qty')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>

</div>

@if($errors->any())
  <div style="margin-top:12px;background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
    <b>Fix errors:</b>
    <ul style="margin:8px 0 0 18px;">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif
