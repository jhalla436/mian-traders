@php
  $canSeeCost = \App\Support\Authz::canSeeCost();
  
  // Build category sizes map for JavaScript
  $categorySizesMap = [];
  foreach($categories as $cat) {
    $sizes = $cat->sizes ?? collect();
    if($sizes->count() > 0) {
      $categorySizesMap[$cat->id] = [
        'name' => $cat->name,
        'count' => $sizes->count(),
        'sizes' => $sizes->map(fn($s) => $s->getDisplayName())->toArray()
      ];
    }
  }
@endphp

<div class="form-grid">

  <div class="form-group">
    <label class="form-label">Name</label>
    <input name="name" value="{{ old('name', $product->name) }}" required class="mt-input" placeholder="e.g., DuraAir">
    @error('name')<div class="form-error">{{ $message }}</div>@enderror
    <div class="form-help text-muted">Base product name (size will be automatically appended if creating with predefined sizes)</div>
  </div>

  <div class="form-group">
    <label class="form-label">SKU (optional)</label>
    <input name="sku" value="{{ old('sku', $product->sku) }}" class="mt-input">
    @error('sku')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Company</label>
    <select name="company_id" class="mt-select">
      <option value="">-- None --</option>
      @foreach($companies as $co)
        <option value="{{ $co->id }}" @selected((int)old('company_id', $product->company_id) === (int)$co->id) data-max-discount="{{ $co->max_discount_percent ?? 0 }}">{{ $co->name }}</option>
      @endforeach
    </select>
    @error('company_id')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Category</label>
    <select name="category_id" class="mt-select" id="category-select" onchange="updateSizesList()">
      <option value="">-- None --</option>
      @foreach($categories as $cat)
        {{-- Show root categories --}}
        @if(!$cat->parent_id)
          <optgroup label="{{ $cat->company ? '📦 ' . $cat->company->name : 'Global' }}">
            <option value="{{ $cat->id }}" @selected((int)old('category_id', $product->category_id) === (int)$cat->id)
                    data-sizes="{{ json_encode($categorySizesMap[$cat->id] ?? null) }}">
              {{ $cat->name }}
            </option>
            {{-- Show sub-categories under this parent --}}
            @if($cat->children->count() > 0)
              @foreach($cat->children as $child)
                <option value="{{ $child->id }}" @selected((int)old('category_id', $product->category_id) === (int)$child->id)
                        data-sizes="{{ json_encode($categorySizesMap[$child->id] ?? null) }}">
                  &nbsp;&nbsp;└─ {{ $child->name }}
                </option>
              @endforeach
            @endif
          </optgroup>
        @endif
      @endforeach
    </select>
    @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
    <div class="form-help text-muted">Select a category or sub-category. Root categories are organized by company.</div>
    <div id="sizes-alert" style="margin-top: 8px; display: none; padding: 12px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
      <strong>📐 Predefined Sizes Found:</strong>
      <div id="sizes-controls" style="display:flex;gap:8px;align-items:center;margin-top:8px;">
        <button type="button" class="btn btn-secondary btn-xs" onclick="toggleAllSizes(true)">Select all</button>
        <button type="button" class="btn btn-secondary btn-xs" onclick="toggleAllSizes(false)">Deselect all</button>
        <span id="sizes-selected-summary" style="font-size:0.9em;color:#444;">Selected <strong id="size-count">0</strong> sizes</span>
      </div>
      <div id="sizes-list" style="margin-top: 8px; display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px;"></div>
      <div style="margin-top: 8px; font-size: 0.9em; color: #666;">
        ✓ Uncheck sizes not available now; only selected sizes will be created as variants.
      </div>
      <div style="margin-top:8px">
        <label class="form-label">Custom sizes (optional)</label>
        <input name="custom_sizes" type="text" class="mt-input" placeholder="Comma-separated sizes, e.g. 78×42, 80×44" value="{{ old('custom_sizes', '') }}">
        <div class="form-help text-muted">If your category doesn't have the exact sizes, enter custom sizes here. These will be used instead of the predefined sizes.</div>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label">Discount Type</label>
    <select name="discount_type_id" class="mt-select">
      <option value="">-- None --</option>
      @foreach($discountTypes as $dt)
        <option value="{{ $dt->id }}" @selected((int)old('discount_type_id', $product->discount_type_id) === (int)$dt->id)>{{ $dt->name }}</option>
      @endforeach
    </select>
    @error('discount_type_id')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
      <span>Sheet Design (optional)</span>
      <a href="{{ route('mt.sheet_designs.create') }}" target="_blank" style="font-size: 11px; font-weight: 600; color: #4f46e5;">+ Add Design</a>
    </label>
    <select name="sheet_design_id" class="mt-select">
      <option value="">-- None --</option>
      @foreach(($sheetDesigns ?? []) as $sd)
        <option value="{{ $sd->id }}" @selected((int)old('sheet_design_id', $product->sheet_design_id) === (int)$sd->id)>
          {{ $sd->name }} {{ $sd->finish ? "({$sd->finish})" : '' }} {{ $sd->color_group ? "[{$sd->color_group}]" : '' }}
        </option>
      @endforeach
    </select>
    @error('sheet_design_id')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Active</label>
    <label class="form-checkbox" style="padding-top: 8px;">
      <input type="checkbox" name="is_active" value="1" @checked((int)old('is_active', $product->is_active ?? 1) === 1)>
      <span>Yes</span>
    </label>
  </div>

  <div class="form-group">
    <label class="form-label">Pricing Mode</label>
    <select name="pricing_mode" class="mt-select">
      <option value="manual" @selected(old('pricing_mode', $product->pricing_mode ?? 'manual') === 'manual')>Manual</option>
      <option value="shell" @selected(old('pricing_mode', $product->pricing_mode) === 'shell')>Shell (w*l*h/144 * shell_rate)</option>
    </select>
    @error('pricing_mode')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Shell Rate (only for shell mode)</label>
    <input name="shell_rate" type="number" step="0.01" min="0" value="{{ old('shell_rate', $product->shell_rate) }}" class="mt-input">
    @error('shell_rate')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">MRP Markup / Addition (e.g. 70 for +70 markup)</label>
    <input name="mrp_markup" type="number" step="0.01" min="0" value="{{ old('mrp_markup', $product->mrp_markup) }}" class="mt-input" placeholder="e.g., 70">
    @error('mrp_markup')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Width (inches)</label>
    <input name="width_in" type="number" step="0.01" min="0" value="{{ old('width_in', $product->width_in) }}" class="mt-input">
    @error('width_in')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Length (inches)</label>
    <input name="length_in" type="number" step="0.01" min="0" value="{{ old('length_in', $product->length_in) }}" class="mt-input">
    @error('length_in')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Height (inches)</label>
    <input name="height_in" type="number" step="0.01" min="0" value="{{ old('height_in', $product->height_in) }}" class="mt-input">
    @error('height_in')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">MRP</label>
    <input name="mrp" type="number" step="0.01" min="0" value="{{ old('mrp', $product->mrp) }}" class="mt-input">
    @error('mrp')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Default Selling Price</label>
    <input name="selling_price_default" type="number" step="0.01" min="0" value="{{ old('selling_price_default', $product->selling_price_default) }}" class="mt-input">
    @error('selling_price_default')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Max POS Discount %</label>
    <input id="product-max-discount" name="max_discount_percent" type="number" step="0.01" min="0" max="100" value="{{ old('max_discount_percent', $product->max_discount_percent ?? '') }}" placeholder="{{ $product->company?->max_discount_percent ? 'Use company default ' . $product->company->max_discount_percent . '%' : '' }}" class="mt-input">
    <div class="form-help text-muted">Set a positive percentage to cap POS discount for this item. Keep `0` for no limit.</div>
    <div id="company-max-discount-hint" class="form-help text-muted">Company default will apply when this field is blank.</div>
    @error('max_discount_percent')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  @if($canSeeCost)
    <div class="form-group">
      <label class="form-label">Manual Purchase Price (Optional)</label>
      <input name="purchase_price_manual" type="number" step="0.01" min="0" value="{{ old('purchase_price_manual', $product->purchase_price_manual) }}" class="mt-input">
      @error('purchase_price_manual')<div class="form-error">{{ $message }}</div>@enderror
    </div>
  @endif

  <div class="form-group">
    <label class="form-label">Stock Qty</label>
    <input name="stock_qty" type="number" step="0.01" value="{{ old('stock_qty', $product->stock_qty) }}" class="mt-input">
    @error('stock_qty')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Low Stock Alert Qty</label>
    <input name="low_stock_alert_qty" type="number" step="0.01" min="0" value="{{ old('low_stock_alert_qty', $product->low_stock_alert_qty) }}" class="mt-input">
    @error('low_stock_alert_qty')<div class="form-error">{{ $message }}</div>@enderror
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

<script>
function updateSizesList() {
  const select = document.getElementById('category-select');
  const selectedOption = select.options[select.selectedIndex];
  const sizesData = selectedOption.getAttribute('data-sizes');
  const alert = document.getElementById('sizes-alert');
  const sizesList = document.getElementById('sizes-list');
  const sizeCount = document.getElementById('size-count');
  const summary = document.getElementById('sizes-selected-summary');

  if (sizesData) {
    const sizes = JSON.parse(sizesData);
    const useOldSelection = oldSelectedSizes.length && selectedOption.value === initialCategoryId;
    const selectedSizes = new Set(useOldSelection ? oldSelectedSizes : sizes.sizes);

    alert.style.display = 'block';
    sizeCount.textContent = selectedSizes.size;
    summary.innerHTML = 'Selected <strong id="size-count">' + selectedSizes.size + '</strong> sizes';

    sizesList.innerHTML = sizes.sizes.map(s => {
      const escaped = htmlEscape(s);
      const checked = selectedSizes.has(s) ? ' checked' : '';
      return `<label style="display:flex;align-items:center;gap:8px;padding:8px;background:#fff;border:1px solid #ddd;border-radius:8px;cursor:pointer;">
                <input type="checkbox" name="selected_sizes[]" value="${escaped}"${checked}>
                <span style="font-size:0.92em;color:#0f172a;">${escaped}</span>
              </label>`;
    }).join('');

    sizesList.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', updateSizeSummary);
    });
    // Ensure summary is correct and explicitly select all sizes by default
    updateSizeSummary();
    if (!useOldSelection) {
      toggleAllSizes(true);
    }
  } else {
    alert.style.display = 'none';
    sizesList.innerHTML = '';
  }
}

function updateSizeSummary() {
  const checkedCount = document.querySelectorAll('#sizes-list input[type="checkbox"]:checked').length;
  const countEl = document.getElementById('size-count');
  const summary = document.getElementById('sizes-selected-summary');
  if (countEl) countEl.textContent = checkedCount;
  if (summary) summary.innerHTML = 'Selected <strong id="size-count">' + checkedCount + '</strong> sizes';
}

function toggleAllSizes(selectAll) {
  document.querySelectorAll('#sizes-list input[type="checkbox"]').forEach(checkbox => {
    checkbox.checked = selectAll;
  });
  updateSizeSummary();
}

function htmlEscape(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// Call on page load if category is already selected
const oldSelectedSizes = @json(old('selected_sizes', []));
const initialCategoryId = document.getElementById('category-select') ? document.getElementById('category-select').value : '';

// Auto-select all sizes by default on page load
window.addEventListener('DOMContentLoaded', function() {
  updateSizesList();
  // Auto-select all sizes if category has them
  setTimeout(() => {
    const sizesList = document.getElementById('sizes-list');
    if (sizesList && sizesList.innerHTML.trim() !== '') {
      const checkboxes = sizesList.querySelectorAll('input[type="checkbox"]');
      if (checkboxes.length > 0 && oldSelectedSizes.length === 0) {
        checkboxes.forEach(cb => cb.checked = true);
        updateSizeSummary();
      }
    }
  }, 50);
  const companySelect = document.querySelector('select[name="company_id"]');
  const productMaxDiscount = document.getElementById('product-max-discount');

  const updateDiscountPlaceholder = () => {
    if (!productMaxDiscount || !companySelect) {
      return;
    }
    const selected = companySelect.options[companySelect.selectedIndex];
    const companyDefault = selected?.dataset?.maxDiscount ? parseFloat(selected.dataset.maxDiscount) : 0;
    if (companyDefault > 0) {
      productMaxDiscount.placeholder = `Use company default ${companyDefault}%`;
      document.getElementById('company-max-discount-hint').textContent = 'Company default applies when this field is blank.';
    } else {
      productMaxDiscount.placeholder = '';
      document.getElementById('company-max-discount-hint').textContent = 'Set a max discount for this item or leave blank for no product-specific cap.';
    }
  };

  if (companySelect) {
    companySelect.addEventListener('change', function() {
      const selected = companySelect.options[companySelect.selectedIndex];
      const companyDefault = selected?.dataset?.maxDiscount ? parseFloat(selected.dataset.maxDiscount) : 0;
      updateDiscountPlaceholder();
    });
    updateDiscountPlaceholder();
  }
});
</script>

