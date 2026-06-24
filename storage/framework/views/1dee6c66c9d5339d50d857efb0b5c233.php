<?php
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
?>

<div class="form-grid">

  <div class="form-group">
    <label class="form-label">Name</label>
    <input name="name" value="<?php echo e(old('name', $product->name)); ?>" required class="mt-input" placeholder="e.g., DuraAir">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <div class="form-help text-muted">Base product name (size will be automatically appended if creating with predefined sizes)</div>
  </div>

  <div class="form-group">
    <label class="form-label">SKU (optional)</label>
    <input name="sku" value="<?php echo e(old('sku', $product->sku)); ?>" class="mt-input">
    <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Company</label>
    <select name="company_id" class="mt-select">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($co->id); ?>" <?php if((int)old('company_id', $product->company_id) === (int)$co->id): echo 'selected'; endif; ?> data-max-discount="<?php echo e($co->max_discount_percent ?? 0); ?>"><?php echo e($co->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Category</label>
    <select name="category_id" class="mt-select" id="category-select" onchange="updateSizesList()">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        
        <?php if(!$cat->parent_id): ?>
          <optgroup label="<?php echo e($cat->company ? '📦 ' . $cat->company->name : 'Global'); ?>">
            <option value="<?php echo e($cat->id); ?>" <?php if((int)old('category_id', $product->category_id) === (int)$cat->id): echo 'selected'; endif; ?>
                    data-sizes="<?php echo e(json_encode($categorySizesMap[$cat->id] ?? null)); ?>">
              <?php echo e($cat->name); ?>

            </option>
            
            <?php if($cat->children->count() > 0): ?>
              <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($child->id); ?>" <?php if((int)old('category_id', $product->category_id) === (int)$child->id): echo 'selected'; endif; ?>
                        data-sizes="<?php echo e(json_encode($categorySizesMap[$child->id] ?? null)); ?>">
                  &nbsp;&nbsp;└─ <?php echo e($child->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </optgroup>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
        <input name="custom_sizes" type="text" class="mt-input" placeholder="Comma-separated sizes, e.g. 78×42, 80×44" value="<?php echo e(old('custom_sizes', '')); ?>">
        <div class="form-help text-muted">If your category doesn't have the exact sizes, enter custom sizes here. These will be used instead of the predefined sizes.</div>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label">Discount Type</label>
    <select name="discount_type_id" class="mt-select">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($dt->id); ?>" <?php if((int)old('discount_type_id', $product->discount_type_id) === (int)$dt->id): echo 'selected'; endif; ?>><?php echo e($dt->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['discount_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
      <span>Sheet Design (optional)</span>
      <a href="<?php echo e(route('mt.sheet_designs.create')); ?>" target="_blank" style="font-size: 11px; font-weight: 600; color: #4f46e5;">+ Add Design</a>
    </label>
    <select name="sheet_design_id" class="mt-select">
      <option value="">-- None --</option>
      <?php $__currentLoopData = ($sheetDesigns ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($sd->id); ?>" <?php if((int)old('sheet_design_id', $product->sheet_design_id) === (int)$sd->id): echo 'selected'; endif; ?>>
          <?php echo e($sd->name); ?> <?php echo e($sd->finish ? "({$sd->finish})" : ''); ?> <?php echo e($sd->color_group ? "[{$sd->color_group}]" : ''); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['sheet_design_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Active</label>
    <label class="form-checkbox" style="padding-top: 8px;">
      <input type="checkbox" name="is_active" value="1" <?php if((int)old('is_active', $product->is_active ?? 1) === 1): echo 'checked'; endif; ?>>
      <span>Yes</span>
    </label>
  </div>

  <div class="form-group">
    <label class="form-label">Pricing Mode</label>
    <select name="pricing_mode" class="mt-select">
      <option value="manual" <?php if(old('pricing_mode', $product->pricing_mode ?? 'manual') === 'manual'): echo 'selected'; endif; ?>>Manual</option>
      <option value="shell" <?php if(old('pricing_mode', $product->pricing_mode) === 'shell'): echo 'selected'; endif; ?>>Shell (w*l*h/144 * shell_rate)</option>
    </select>
    <?php $__errorArgs = ['pricing_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Shell Rate (only for shell mode)</label>
    <input name="shell_rate" type="number" step="0.01" min="0" value="<?php echo e(old('shell_rate', $product->shell_rate)); ?>" class="mt-input">
    <?php $__errorArgs = ['shell_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">MRP Markup / Addition (e.g. 70 for +70 markup)</label>
    <input name="mrp_markup" type="number" step="0.01" min="0" value="<?php echo e(old('mrp_markup', $product->mrp_markup)); ?>" class="mt-input" placeholder="e.g., 70">
    <?php $__errorArgs = ['mrp_markup'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Width (inches)</label>
    <input name="width_in" type="number" step="0.01" min="0" value="<?php echo e(old('width_in', $product->width_in)); ?>" class="mt-input">
    <?php $__errorArgs = ['width_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Length (inches)</label>
    <input name="length_in" type="number" step="0.01" min="0" value="<?php echo e(old('length_in', $product->length_in)); ?>" class="mt-input">
    <?php $__errorArgs = ['length_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Height (inches)</label>
    <input name="height_in" type="number" step="0.01" min="0" value="<?php echo e(old('height_in', $product->height_in)); ?>" class="mt-input">
    <?php $__errorArgs = ['height_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">MRP</label>
    <input name="mrp" type="number" step="0.01" min="0" value="<?php echo e(old('mrp', $product->mrp)); ?>" class="mt-input">
    <?php $__errorArgs = ['mrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Default Selling Price</label>
    <input name="selling_price_default" type="number" step="0.01" min="0" value="<?php echo e(old('selling_price_default', $product->selling_price_default)); ?>" class="mt-input">
    <?php $__errorArgs = ['selling_price_default'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Max POS Discount %</label>
    <input id="product-max-discount" name="max_discount_percent" type="number" step="0.01" min="0" max="100" value="<?php echo e(old('max_discount_percent', $product->max_discount_percent ?? '')); ?>" placeholder="<?php echo e($product->company?->max_discount_percent ? 'Use company default ' . $product->company->max_discount_percent . '%' : ''); ?>" class="mt-input">
    <div class="form-help text-muted">Set a positive percentage to cap POS discount for this item. Keep `0` for no limit.</div>
    <div id="company-max-discount-hint" class="form-help text-muted">Company default will apply when this field is blank.</div>
    <?php $__errorArgs = ['max_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <?php if($canSeeCost): ?>
    <div class="form-group">
      <label class="form-label">Manual Purchase Price (Optional)</label>
      <input name="purchase_price_manual" type="number" step="0.01" min="0" value="<?php echo e(old('purchase_price_manual', $product->purchase_price_manual)); ?>" class="mt-input">
      <?php $__errorArgs = ['purchase_price_manual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
  <?php endif; ?>

  <div class="form-group">
    <label class="form-label">Stock Qty</label>
    <input name="stock_qty" type="number" step="0.01" value="<?php echo e(old('stock_qty', $product->stock_qty)); ?>" class="mt-input">
    <?php $__errorArgs = ['stock_qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Low Stock Alert Qty</label>
    <input name="low_stock_alert_qty" type="number" step="0.01" min="0" value="<?php echo e(old('low_stock_alert_qty', $product->low_stock_alert_qty)); ?>" class="mt-input">
    <?php $__errorArgs = ['low_stock_alert_qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

</div>

<?php if($errors->any()): ?>
  <div class="errors-box">
    <b>Fix errors:</b>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($e); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>

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
const oldSelectedSizes = <?php echo json_encode(old('selected_sizes', []), 512) ?>;
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

<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\products\form.blade.php ENDPATH**/ ?>