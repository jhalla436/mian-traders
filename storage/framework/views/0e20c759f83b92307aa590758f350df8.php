<?php $__env->startSection('content'); ?>
<style>
  .inline-editable {
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
  }
  .inline-editable:hover {
    background-color: #f0f9ff;
    border-radius: 4px;
  }
  .inline-editable .edit-input {
    padding: 4px;
    border: 2px solid #3b82f6;
    border-radius: 4px;
    font-size: 13px;
  }
  .inline-editable .company-default-badge {
    display: inline-block;
    margin-top: 2px;
    font-size: 10px;
    color: #334155;
    background: #e2e8f0;
    border-radius: 999px;
    padding: 1px 6px;
    vertical-align: middle;
  }
</style>
<?php
  $q = $q ?? '';
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>

<div class="page-header">
  <h2 class="page-title">Products</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input name="q" value="<?php echo e($q); ?>" placeholder="Search name/sku/id..." class="mt-input" style="width:220px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<?php if(session('import_errors')): ?>
  <div class="errors-box" style="margin-bottom: 20px; border: 1.5px solid #fca5a5; background: #fff5f5; border-radius: 12px; padding: 16px;">
    <h4 style="color: #991b1b; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Import Validation Warnings (<?php echo e(count(session('import_errors'))); ?>)
    </h4>
    <div style="max-height: 180px; overflow-y: auto; font-size: 13px; color: #7f1d1d; line-height: 1.6;">
      <ul style="margin-left: 20px; padding-left: 0;">
        <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li style="margin-bottom: 4px;"><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<?php if(auth()->user()?->role !== 'cashier'): ?>
<div class="page-actions mb-16" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
  <a href="<?php echo e(route('mt.products.create')); ?>" class="btn btn-success">+ Add Product</a>
  <a href="<?php echo e(route('mt.products.import_form')); ?>" class="btn btn-primary">Import Products</a>
  <a href="<?php echo e(route('mt.products.price_import_pdf_form')); ?>" class="btn btn-teal">Import PDF Price Sheet</a>
  <form id="bulk-delete-form" method="POST" action="<?php echo e(route('mt.products.bulk_destroy')); ?>" style="display:inline-block;margin-left:8px;">
    <?php echo csrf_field(); ?>
    <button id="bulk-delete-btn" type="button" class="btn btn-danger" disabled>Delete selected</button>
  </form>
  <button id="delete-by-keyword-btn" type="button" class="btn btn-danger" style="background: linear-gradient(135deg, #ef4444, #b91c1c); margin-left: 8px;">Delete by Keyword</button>
  <form id="delete-by-keyword-form" method="POST" action="<?php echo e(route('mt.products.bulk_destroy_by_keyword')); ?>" style="display:none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="keyword" id="delete-keyword-input">
  </form>
  <form id="bulk-clear-sizes-form" method="POST" action="<?php echo e(route('mt.products.bulk_clear_sizes')); ?>" style="display:inline-block;margin-left:8px;">
    <?php echo csrf_field(); ?>
    <button id="bulk-clear-sizes-btn" type="button" class="btn btn-secondary" disabled>Clear sizes on selected</button>
  </form>
</div>
<?php endif; ?>

<div class="table-card" style="overflow-x:auto;padding:8px;">
  <table class="mt-table" style="font-size:13px;">
    <thead>
      <tr>
        <?php if(auth()->user()?->role !== 'cashier'): ?>
          <th style="width:36px;"><input id="select-all" type="checkbox" /></th>
        <?php endif; ?>
        <th>Name</th>
        <th>Category</th>
        <th>Company</th>
        <th class="text-right">MRP</th>
        <th class="text-right">Max Disc %</th>
        <?php if($canSeeCost): ?>
          <th class="text-right">Purchase</th>
        <?php endif; ?>
        <th class="text-right">Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $productGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyIndex => $companyGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $isOpen = ($q !== '');
          $displayStyle = $isOpen ? '' : 'display:none;';
          $btnSymbol = $isOpen ? '▼' : '▶';
          $dataOpenVal = $isOpen ? '1' : '0';
          $companyKey = 'company-' . $companyIndex;
          $companyVariantCount = (int)($companyGroup['variant_count'] ?? 0);
          $companyFamilyCount = count($companyGroup['families'] ?? []);
          $colspan = $canSeeCost ? (auth()->user()?->role !== 'cashier' ? 9 : 8) : (auth()->user()?->role !== 'cashier' ? 8 : 7);
          $headerColspan = auth()->user()?->role !== 'cashier' ? $colspan - 1 : $colspan;
          $companyProductIds = collect($companyGroup['families'])->flatMap(fn($fam) => collect($fam))->pluck('id')->implode(',');
        ?>

        <tr class="bg-slate-50" style="background:#f8fafc;">
          <?php if(auth()->user()?->role !== 'cashier'): ?>
            <td></td>
          <?php endif; ?>
          <td colspan="<?php echo e($headerColspan); ?>" style="padding:10px 8px;font-weight:700; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0;">
            <button type="button" class="btn btn-secondary btn-xs js-toggle-company" data-company-key="<?php echo e($companyKey); ?>" data-open="<?php echo e($dataOpenVal); ?>" style="margin:0;"><?php echo e($btnSymbol); ?> <?php echo e($companyGroup['name']); ?> (<?php echo e($companyFamilyCount); ?> main products, <?php echo e($companyVariantCount); ?> variants)</button>
            <?php if(auth()->user()?->role !== 'cashier'): ?>
              <button type="button" class="btn btn-danger btn-xs js-delete-company-btn" data-ids="<?php echo e($companyProductIds); ?>" data-company-name="<?php echo e($companyGroup['name']); ?>" style="padding:2px 6px; font-size:11px; margin-right:8px;">Delete Company</button>
            <?php endif; ?>
          </td>
        </tr>

        <?php $__currentLoopData = $companyGroup['families']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $familyName => $familyProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $familyKey = $companyKey . '-family-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim((string) $familyName)));
            $variantCount = count($familyProducts);
            $familyProductIds = $familyProducts->pluck('id')->implode(',');
          ?>

          <tr class="js-company-family-row <?php echo e($companyKey); ?>" style="<?php echo e($displayStyle); ?>background:#fff;">
            <?php if(auth()->user()?->role !== 'cashier'): ?>
              <td style="padding-left:18px;">
                <input class="family-check" type="checkbox" data-family-key="<?php echo e($familyKey); ?>" />
              </td>
            <?php endif; ?>
            <td colspan="<?php echo e($headerColspan); ?>" style="padding:8px 8px 8px 18px;font-weight:600; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9;">
              <button type="button" class="btn btn-outline btn-xs js-toggle-family" data-company-key="<?php echo e($companyKey); ?>" data-family-key="<?php echo e($familyKey); ?>" data-open="<?php echo e($dataOpenVal); ?>" style="margin:0;"><?php echo e($btnSymbol); ?> <?php echo e($familyName); ?> (<?php echo e($variantCount); ?> variants)</button>
              <?php if(auth()->user()?->role !== 'cashier'): ?>
                <button type="button" class="btn btn-danger btn-xs js-delete-family-btn" data-ids="<?php echo e($familyProductIds); ?>" data-family-name="<?php echo e($familyName); ?>" style="padding:2px 6px; font-size:11px; margin-right:8px;">Delete Category</button>
              <?php endif; ?>
            </td>
          </tr>

          <?php $__currentLoopData = $familyProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $purchase = (float)($p->buying_price ?? 0);
              if ($purchase <= 0) {
                $purchase = \App\Services\PricingService::purchasePrice($p);
              }
            ?>

            <tr class="js-family-variant-row <?php echo e($familyKey); ?>" style="<?php echo e($displayStyle); ?>">
              <?php if(auth()->user()?->role !== 'cashier'): ?>
                <td><input class="row-check" data-family-key="<?php echo e($familyKey); ?>" type="checkbox" value="<?php echo e($p->id); ?>" /></td>
              <?php endif; ?>
              <?php
                $catLower = strtolower($p->category?->name ?? '');
                $isSlab = str_contains($catLower, 'slab') || str_contains($catLower, 'sheet') || str_contains(strtolower($p->name), 'slab') || str_contains(strtolower($p->name), 'sheet');
                if ($isSlab && $p->length_in > 0 && $p->width_in > 0 && $p->height_in > 0) {
                    $len = (float)$p->length_in;
                    $wid = (float)$p->width_in;
                    $hei = (float)$p->height_in;
                    $displayName = "{$len}-{$wid}-{$hei}";
                } else {
                    $displayName = trim(str_ireplace($familyName, '', (string)$p->name));
                    if (empty($displayName)) {
                        $displayName = (string)$p->name;
                    }
                    $displayName = preg_replace('/(?<=\d)\s*[x×]\s*(?=\d)/', '*', $displayName);
                }
              ?>
              <td class="font-bold" style="padding-left: 24px; color: #475569;">
                <span style="color:#94a3b8; font-size:11px; margin-right:4px;">↳</span> <?php echo e($displayName); ?>

              </td>
              <td><?php echo e($p->category?->name ?? '-'); ?></td>
              <td><?php echo e($p->company?->name ?? '-'); ?></td>
              <td class="text-right"><?php echo e(number_format((float)$p->mrp,2)); ?></td>
              <?php
                $effectiveDiscount = $p->effectiveMaxDiscountPercent();
                $productDiscountValue = $p->max_discount_percent === null ? '' : $p->max_discount_percent;
                $usingCompanyDefault = $p->max_discount_percent === null && (float)($p->company?->max_discount_percent ?? 0) > 0;
              ?>
              <td class="text-right inline-editable" data-field="max_discount_percent" data-product-id="<?php echo e($p->id); ?>" title="Click to edit">
                <span class="display-value"><?php echo e($effectiveDiscount > 0 ? number_format($effectiveDiscount,2) . '%' : 'No limit'); ?></span>
                <?php if($usingCompanyDefault): ?>
                  <div class="company-default-badge">company default</div>
                <?php endif; ?>
                <input type="number" class="edit-input" style="width:60px;display:none;" min="0" max="100" step="0.01" value="<?php echo e($productDiscountValue); ?>">
              </td>
              <?php if($canSeeCost): ?>
                <td class="text-right font-bold"><?php echo e(number_format((float)$purchase,2)); ?></td>
              <?php endif; ?>
              <td class="text-right inline-editable" data-field="stock_qty" data-product-id="<?php echo e($p->id); ?>" title="Click to edit">
                <span class="display-value"><?php echo e(number_format((float)$p->stock_qty,2)); ?></span>
                <input type="number" class="edit-input" style="width:70px;display:none;" min="0" step="0.01" value="<?php echo e($p->stock_qty ?? 0); ?>">
              </td>
              <td>
                <div class="actions">
                  <?php if(auth()->user()?->role !== 'cashier'): ?>
                    <a href="<?php echo e(route('mt.products.edit', $p)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                  <?php else: ?>
                    <span class="text-muted text-xs">View only</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr class="empty-row"><td colspan="<?php echo e($canSeeCost ? (auth()->user()?->role !== 'cashier' ? 9 : 8) : (auth()->user()?->role !== 'cashier' ? 8 : 7)); ?>">No products found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<script>
  (function(){
    const selectAll = document.getElementById('select-all');
    const toggleCompanyButtons = document.querySelectorAll('.js-toggle-company');
    const toggleFamilyButtons = document.querySelectorAll('.js-toggle-family');
    const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));
    const familyChecks = () => Array.from(document.querySelectorAll('.family-check'));
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkClearSizesBtn = document.getElementById('bulk-clear-sizes-btn');

    function updateFamilyCheckbox(familyKey) {
      const familyCheckbox = document.querySelector(`.family-check[data-family-key="${familyKey}"]`);
      if (!familyCheckbox) return;

      const childChecks = rowChecks().filter(ch => ch.dataset.familyKey === familyKey);
      const total = childChecks.length;
      const checked = childChecks.filter(ch => ch.checked).length;

      familyCheckbox.checked = total > 0 && checked === total;
      familyCheckbox.indeterminate = checked > 0 && checked < total;
    }

    function updateSelectAllCheckbox() {
      if (!selectAll) return;
      const all = rowChecks();
      const total = all.length;
      const checked = all.filter(ch => ch.checked).length;
      selectAll.checked = total > 0 && checked === total;
      selectAll.indeterminate = checked > 0 && checked < total;
    }

    function updateButtons() {
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      const hasChecked = checked.length > 0;
      if (bulkDeleteBtn) bulkDeleteBtn.disabled = !hasChecked;
      if (bulkClearSizesBtn) bulkClearSizesBtn.disabled = !hasChecked;
      updateSelectAllCheckbox();
      const familyKeys = Array.from(new Set(rowChecks().map(ch => ch.dataset.familyKey).filter(Boolean)));
      familyKeys.forEach(updateFamilyCheckbox);
    }

    function submitIds(formId, confirmMessage) {
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      if (checked.length === 0) return;
      if (confirmMessage && !confirm(confirmMessage.replace('{count}', checked.length))) return;

      const form = document.getElementById(formId);
      Array.from(form.querySelectorAll('input[name="ids[]"][type="hidden"]')).forEach(n => n.remove());
      checked.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        form.appendChild(inp);
      });
      form.submit();
    }

    if (selectAll) {
      selectAll.addEventListener('change', function(){
        rowChecks().forEach(ch => ch.checked = selectAll.checked);
        familyChecks().forEach(ch => { ch.checked = selectAll.checked; ch.indeterminate = false; });
        updateButtons();
      });
    }

    document.addEventListener('change', function(e){
      if (e.target && e.target.classList && e.target.classList.contains('row-check')) {
        updateButtons();
      }
      if (e.target && e.target.classList && e.target.classList.contains('family-check')) {
        const familyKey = e.target.dataset.familyKey;
        const checked = e.target.checked;
        rowChecks().filter(ch => ch.dataset.familyKey === familyKey).forEach(ch => ch.checked = checked);
        updateButtons();
      }
    });

    if (bulkDeleteBtn) {
      bulkDeleteBtn.addEventListener('click', function(){
        submitIds('bulk-delete-form', 'Delete {count} selected products? This cannot be undone.');
      });
    }

    if (bulkClearSizesBtn) {
      bulkClearSizesBtn.addEventListener('click', function(){
        submitIds('bulk-clear-sizes-form', 'Clear sizes for {count} selected products?');
      });
    }

    function setVisible(selector, visible) {
      document.querySelectorAll(selector).forEach(row => {
        row.style.display = visible ? '' : 'none';
      });
    }

    toggleCompanyButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        const companyKey = this.dataset.companyKey;
        const open = this.dataset.open === '1';
        this.dataset.open = open ? '0' : '1';
        this.textContent = (open ? '▶' : '▼') + this.textContent.slice(1);

        setVisible('.js-company-family-row.' + companyKey, !open);
        document.querySelectorAll('.js-company-family-row.' + companyKey).forEach(row => {
          row.style.display = open ? 'none' : '';
        });
      });
    });

    toggleFamilyButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        const familyKey = this.dataset.familyKey;
        const open = this.dataset.open === '1';
        this.dataset.open = open ? '0' : '1';
        this.textContent = (open ? '▶' : '▼') + this.textContent.slice(1);

        setVisible('.' + familyKey, !open);
      });
    });

    // Inline editing for max_discount_percent and stock_qty
    document.querySelectorAll('.inline-editable').forEach(cell => {
      const displayValue = cell.querySelector('.display-value');
      const editInput = cell.querySelector('.edit-input');
      const field = cell.dataset.field;
      const productId = cell.dataset.productId;

      cell.addEventListener('click', function(e) {
        if (editInput.style.display === 'none') {
          displayValue.style.display = 'none';
          editInput.style.display = 'inline-block';
          editInput.focus();
          editInput.select();
        }
      });

      editInput.addEventListener('blur', function() {
        saveInlineValue(cell, field, productId, editInput.value, displayValue, editInput);
      });

      editInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          saveInlineValue(cell, field, productId, editInput.value, displayValue, editInput);
        } else if (e.key === 'Escape') {
          editInput.style.display = 'none';
          displayValue.style.display = 'inline';
        }
      });
    });

    function saveInlineValue(cell, field, productId, value, displayValue, editInput) {
      const originalValue = editInput.value;

      // For max_discount_percent, empty is valid (means inherit from company)
      if (field === 'max_discount_percent') {
        if (value === '' || value === null) {
          // Empty is allowed for discount percent
          const numeric = null;
          sendUpdateRequest(cell, field, productId, numeric, displayValue, editInput, originalValue);
          return;
        }
        if (isNaN(Number(value))) {
          alert('Please enter a valid number');
          closeEditMode(editInput, displayValue);
          return;
        }
        const numeric = parseFloat(value);
        if (numeric < 0 || numeric > 100) {
          alert('Max discount must be between 0 and 100');
          closeEditMode(editInput, displayValue);
          return;
        }
        sendUpdateRequest(cell, field, productId, numeric, displayValue, editInput, originalValue);
        return;
      }

      // For stock_qty, require a valid number
      if (field === 'stock_qty') {
        if (value === '' || value === null || isNaN(Number(value))) {
          alert('Please enter a valid number for stock');
          closeEditMode(editInput, displayValue);
          return;
        }
        const numeric = parseFloat(value);
        if (numeric < 0) {
          alert('Stock cannot be negative');
          closeEditMode(editInput, displayValue);
          return;
        }
        sendUpdateRequest(cell, field, productId, numeric, displayValue, editInput, originalValue);
        return;
      }
    }

    function closeEditMode(editInput, displayValue) {
      editInput.style.display = 'none';
      displayValue.style.display = 'inline';
    }

    function sendUpdateRequest(cell, field, productId, numeric, displayValue, editInput, originalValue) {
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

      // Send AJAX request with proper CSRF header and credentials
      fetch(`/products/${productId}/quick-update`, {
        method: 'PATCH',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ field: field, value: numeric }),
      })
      .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
          const msg = data?.message || data?.error || 'Failed to update';
          throw new Error(msg);
        }
        return data;
      })
      .then(data => {
        let displayText = field === 'max_discount_percent'
          ? (numeric === null || numeric === 0 ? 'No limit' : numeric.toFixed(2) + '%')
          : numeric.toFixed(2);
        displayValue.textContent = displayText;
        if (numeric !== null) {
          editInput.value = numeric;
        } else {
          editInput.value = '';
        }
        editInput.style.display = 'none';
        displayValue.style.display = 'inline';
        cell.style.backgroundColor = '#d4edda';
        setTimeout(() => { cell.style.backgroundColor = ''; }, 1000);
      })
      .catch(error => {
        console.error('Inline update error:', error);
        alert('Update failed: ' + (error.message || 'Unknown error'));
        editInput.value = originalValue;
        closeEditMode(editInput, displayValue);
      });
    }

    // Delete by Keyword handler
    const deleteByKeywordBtn = document.getElementById('delete-by-keyword-btn');
    const deleteByKeywordForm = document.getElementById('delete-by-keyword-form');
    const deleteKeywordInput = document.getElementById('delete-keyword-input');

    if (deleteByKeywordBtn) {
      deleteByKeywordBtn.addEventListener('click', function() {
        const keyword = prompt("Enter keyword to delete all matching products (e.g. 'Dura Foam Uncovered' or 'Dura Foam'):");
        if (keyword === null) return; // user cancelled
        
        const trimmed = keyword.trim();
        if (trimmed.length < 3) {
          alert("Keyword must be at least 3 characters long to prevent accidental deletions.");
          return;
        }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        // Fetch count of matching products
        fetch(`/products/count-by-keyword?keyword=${encodeURIComponent(trimmed)}`, {
          headers: { 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          }
        })
        .then(response => response.json())
        .then(data => {
          const count = data.count || 0;
          if (count === 0) {
            alert(`No products found matching keyword "${trimmed}".`);
            return;
          }

          const confirmed = confirm(`Are you sure you want to delete all ${count} products containing "${trimmed}"?\n\nThis action cannot be undone.`);
          if (confirmed) {
            deleteKeywordInput.value = trimmed;
            deleteByKeywordForm.submit();
          }
        })
        .catch(err => {
          console.error("Error fetching match count:", err);
          const confirmed = confirm(`Delete all products containing "${trimmed}"?\n\nThis action cannot be undone.`);
          if (confirmed) {
            deleteKeywordInput.value = trimmed;
            deleteByKeywordForm.submit();
          }
        });
      });
    }
    // Direct category/family delete handler
    document.addEventListener('click', function(e) {
      const deleteBtn = e.target.closest('.js-delete-family-btn');
      if (deleteBtn) {
        const familyName = deleteBtn.getAttribute('data-family-name');
        const idsStr = deleteBtn.getAttribute('data-ids') || '';
        const ids = idsStr.split(',').filter(Boolean);
        
        if (ids.length === 0) return;

        const confirmed = confirm(`Are you sure you want to delete the category "${familyName}" (${ids.length} products)?\n\nThis action cannot be undone.`);
        if (confirmed) {
          // Uncheck all row-checks first
          rowChecks().forEach(ch => ch.checked = false);
          
          // Check only the matching IDs
          ids.forEach(id => {
            const checkbox = document.querySelector(`.row-check[value="${id}"]`);
            if (checkbox) {
              checkbox.checked = true;
            }
          });
          
          // Submit the bulk delete form
          submitIds('bulk-delete-form', '');
        }
      }
    });

    // Direct company delete handler
    document.addEventListener('click', function(e) {
      const deleteBtn = e.target.closest('.js-delete-company-btn');
      if (deleteBtn) {
        const companyName = deleteBtn.getAttribute('data-company-name');
        const idsStr = deleteBtn.getAttribute('data-ids') || '';
        const ids = idsStr.split(',').filter(Boolean);
        
        if (ids.length === 0) return;

        const confirmed = confirm(`Are you sure you want to delete all products for the company "${companyName}" (${ids.length} products)?\n\nThis action cannot be undone.`);
        if (confirmed) {
          // Uncheck all row-checks first
          rowChecks().forEach(ch => ch.checked = false);
          
          // Check only the matching IDs
          ids.forEach(id => {
            const checkbox = document.querySelector(`.row-check[value="${id}"]`);
            if (checkbox) {
              checkbox.checked = true;
            }
          });
          
          // Submit the bulk delete form
          submitIds('bulk-delete-form', '');
        }
      }
    });
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\products\index.blade.php ENDPATH**/ ?>