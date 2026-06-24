<?php $__env->startSection('title', 'PDF/CSV Price Import'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
  <div>
    <h2 class="page-title">Import Company Price List</h2>
    <div class="page-subtitle">Automatically read and extract products, sizes, and rates directly from supplier PDF, CSV, or TXT price sheets.</div>
  </div>
  <a href="<?php echo e(route('mt.products.index')); ?>" class="btn btn-secondary btn-sm">← Back to Products</a>
</div>


<div class="mt-tabs" style="margin-bottom: 20px;">
  <a href="<?php echo e(route('mt.products.price_import_form')); ?>" class="mt-tab">CSV / Text Import</a>
  <a href="<?php echo e(route('mt.products.price_import_pdf_form')); ?>" class="mt-tab active">PDF Price List Parser</a>
</div>

<?php if(session('success')): ?>
  <div class="mt-alert mt-alert-success" style="margin-bottom: 20px;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <div>
      <strong>Success!</strong> <?php echo e(session('success')); ?>

    </div>
  </div>
<?php endif; ?>

<?php if($errors->any()): ?>
  <div class="errors-box" style="margin-bottom: 20px;">
    <b>Please correct the following errors:</b>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card" style="max-width:100%; margin-bottom: 20px;">
  <div class="card-body">
    <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:12px;">Step 1: Upload Supplier PDF, CSV, or TXT List</h3>
    <p class="text-muted" style="font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
      Select the supplier company and category context, then upload the raw PDF, CSV, or TXT list (e.g. Master, Dura, Diamond, etc.). 
      Our system will extract all product text, match dimension patterns, and let you inspect the results before saving.
    </p>

    <form method="POST" action="<?php echo e(route('mt.products.price_import_pdf_process')); ?>" enctype="multipart/form-data" class="form-stack">
      <?php echo csrf_field(); ?>

      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Supplier / Company</label>
          <select name="default_company_id" class="mt-select" required>
            <option value="">-- Select Company --</option>
            <?php $__currentLoopData = $companies ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($c->id); ?>" <?php if(isset($selectedCompanyId) && (int)$selectedCompanyId === (int)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Default Category Scoping</label>
          <select name="default_category_id" class="mt-select" required>
            <option value="">-- Select Category --</option>
            <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cat->id); ?>" <?php if(isset($selectedCategoryId) && (int)$selectedCategoryId === (int)$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-top: 10px;">
        <label class="form-label">Select Price List File(s) (PDF, CSV, or TXT)</label>
        
        <div class="file-uploader-box" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 24px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s; position: relative;">
          <input type="file" id="multi-file-input" name="files[]" accept=".pdf,.csv,.txt" required style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" multiple>
          <div class="upload-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-bottom: 8px; display: inline-block;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
            </svg>
            <p style="font-weight: 600; color: #334155; margin-bottom: 4px;">Click to select multiple PDF/CSV/TXT files, or drag them here</p>
            <p class="text-xs text-muted">Supports selecting multiple files at once</p>
          </div>
        </div>

        <div id="file-list-container" style="margin-top: 16px; display: none;">
          <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 8px;">Selected Files:</div>
          <div id="selected-files-list" style="display: flex; flex-direction: column; gap: 8px;"></div>
          
          <button type="button" id="add-more-btn" class="btn btn-outline btn-xs" style="margin-top: 12px; display: inline-flex; align-items: center; gap: 4px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Add More Files (from other folders)
          </button>
        </div>

        <div id="additional-inputs-container" style="display: none;"></div>
      </div>

      <div style="margin-top: 16px;">
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload and Parse File
        </button>
      </div>
    </form>
  </div>
</div>

<?php if(isset($parsedItems)): ?>
  <div class="card" style="max-width:100%; border: 1.5px solid #6366f1;">
    <div class="card-body">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
        <div>
          <h3 style="font-size:16px; font-weight:800; color:#4f46e5; display:flex; align-items:center; gap:6px; margin:0;">
            <span class="badge badge-info" style="font-size:12px; padding:4px 8px;">Step 2</span> 
            Verify and Confirm Parsed Price List
          </h3>
          <p class="text-muted" style="font-size:12px; margin: 4px 0 0 0;">Review parsed results below. You can directly edit any field or untick items you don't want to import.</p>
        </div>
        <div style="display:flex; gap:6px;">
          <button type="button" class="btn btn-outline btn-xs" onclick="toggleAllChecks(true)">Select All</button>
          <button type="button" class="btn btn-outline btn-xs" onclick="toggleAllChecks(false)">Deselect All</button>
        </div>
      </div>

      <form id="confirmImportForm" method="POST" action="<?php echo e(route('mt.products.price_import_pdf_confirm')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="company_id" value="<?php echo e($selectedCompanyId); ?>">
        <input type="hidden" name="category_id" value="<?php echo e($selectedCategoryId); ?>">
        <input type="hidden" name="items_json" id="itemsJsonInput">

        <div style="overflow-x:auto; max-height: 550px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:10px; margin-bottom: 16px;">
          <table class="mt-table" style="width:100%; margin:0;">
            <thead style="position: sticky; top:0; z-index:10; background:#f8fafc; box-shadow: 0 1px 0 #e2e8f0;">
              <tr>
                <th style="width: 40px; padding: 10px 14px; text-align:center;">Import</th>
                <th style="font-size: 10px; padding: 10px 14px;">Original File Text Line</th>
                <th style="font-size: 10px; padding: 10px 14px;">Extracted Name</th>
                <th style="font-size: 10px; padding: 10px 14px; width: 80px; text-align:right;">Length (L)</th>
                <th style="font-size: 10px; padding: 10px 14px; width: 80px; text-align:right;">Width (W)</th>
                <th style="font-size: 10px; padding: 10px 14px; width: 80px; text-align:right;">Height (H)</th>
                <th style="font-size: 10px; padding: 10px 14px; width: 110px; text-align:right;">Rate / Price (Rs)</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $parsedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td style="text-align:center; padding: 8px 14px; vertical-align:middle;">
                    <input type="checkbox" name="items[<?php echo e($idx); ?>][import]" value="1" checked class="row-checkbox" style="width:16px; height:16px; cursor:pointer;" />
                  </td>
                  <td style="font-family: monospace; font-size:11px; color:#64748b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding: 8px 14px;">
                    <?php echo e($item['raw_line']); ?>

                  </td>
                  <td style="padding: 8px 14px;">
                    <input type="text" name="items[<?php echo e($idx); ?>][name]" value="<?php echo e($item['name']); ?>" class="mt-input" style="padding:4px 8px; font-size:12px; min-height:28px;" required />
                  </td>
                  <td style="padding: 8px 14px;">
                    <input type="number" step="0.1" name="items[<?php echo e($idx); ?>][length_in]" value="<?php echo e($item['length_in']); ?>" class="mt-input" style="padding:4px 8px; font-size:12px; min-height:28px; text-align:right;" required />
                  </td>
                  <td style="padding: 8px 14px;">
                    <input type="number" step="0.1" name="items[<?php echo e($idx); ?>][width_in]" value="<?php echo e($item['width_in']); ?>" class="mt-input" style="padding:4px 8px; font-size:12px; min-height:28px; text-align:right;" required />
                  </td>
                  <td style="padding: 8px 14px;">
                    <input type="number" step="0.1" name="items[<?php echo e($idx); ?>][height_in]" value="<?php echo e($item['height_in']); ?>" class="mt-input" style="padding:4px 8px; font-size:12px; min-height:28px; text-align:right;" />
                  </td>
                  <td style="padding: 8px 14px;">
                    <input type="number" step="0.01" name="items[<?php echo e($idx); ?>][price]" value="<?php echo e($item['price']); ?>" class="mt-input" style="padding:4px 8px; font-size:12px; min-height:28px; text-align:right; font-weight:700; color:#0f172a;" required />
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="7" style="padding:40px; text-align:center; color:#64748b;">
                    <div style="font-size:14px; font-weight:600; margin-bottom:4px;">No products could be parsed</div>
                    <div style="font-size:12px; color:#94a3b8;">Make sure the file contains standard products with size descriptions (e.g. 72x36x4) and prices.</div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if(count($parsedItems) > 0): ?>
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="font-size:13px; color:#475569; font-weight:500;">
              Total parsed items: <strong style="color:#4f46e5;" id="checkedCount"><?php echo e(count($parsedItems)); ?></strong> / <?php echo e(count($parsedItems)); ?> selected.
            </div>
            <button type="submit" class="btn btn-success">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Confirm and Import Products
            </button>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>
<?php endif; ?>

<?php if(isset($report)): ?>
  <div class="card" style="max-width:100%; border: 1.5px solid #22c55e;">
    <div class="card-body">
      <h3 style="font-size:16px; font-weight:800; color:#15803d; display:flex; align-items:center; gap:6px; margin-bottom:12px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Price List Import Successfully Completed!
      </h3>
      <p class="text-muted" style="font-size:13px; margin-bottom:16px;">The uploaded price sheet has been fully written to your database. Here is the operational summary:</p>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:20px;">
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:10px; text-align:center;">
          <div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase;">Total Items Processed</div>
          <div style="font-size:24px; font-weight:900; color:#166534; margin-top:4px;"><?php echo e($report['total']); ?></div>
        </div>
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:10px; text-align:center;">
          <div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase;">Products Auto-Created</div>
          <div style="font-size:24px; font-weight:900; color:#166534; margin-top:4px;"><?php echo e($report['created_products']); ?></div>
        </div>
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:10px; text-align:center;">
          <div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase;">Shop Prices Created</div>
          <div style="font-size:24px; font-weight:900; color:#166534; margin-top:4px;"><?php echo e($report['created_shop_prices']); ?></div>
        </div>
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:10px; text-align:center;">
          <div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase;">Shop Prices Updated</div>
          <div style="font-size:24px; font-weight:900; color:#166534; margin-top:4px;"><?php echo e($report['updated_shop_prices']); ?></div>
        </div>
      </div>

      <?php if(count($report['failed'] ?? []) > 0): ?>
        <h4 style="font-size:14px; font-weight:700; color:#991b1b; margin-bottom:8px;">Failed rows / validation errors</h4>
        <div style="overflow-x:auto; border:1px solid #fca5a5; border-radius:10px;">
          <table class="mt-table" style="width:100%; margin:0;">
            <thead>
              <tr style="background:#fef2f2;">
                <th style="color:#991b1b;">Row #</th>
                <th style="color:#991b1b;">Failure Reason</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $report['failed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td style="color:#991b1b; font-weight:700;"><?php echo e($f['row']); ?></td>
                  <td style="color:#7f1d1d;"><?php echo e($f['reason']); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<script>
function toggleAllChecks(status) {
  const checkboxes = document.querySelectorAll('.row-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = status;
  });
  updateCheckedCount();
}

function updateCheckedCount() {
  const countEl = document.getElementById('checkedCount');
  if (countEl) {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    countEl.innerText = checked;
  }
}

document.addEventListener('change', function(event) {
  if (event.target.classList.contains('row-checkbox')) {
    updateCheckedCount();
  }
});

// Intercept form submission to serialize fields into JSON and prevent exceeding PHP max_input_vars limit
const importForm = document.getElementById('confirmImportForm');
if (importForm) {
  importForm.addEventListener('submit', function(e) {
    const items = [];
    const rows = this.querySelectorAll('tbody tr');
    
    rows.forEach((row) => {
      const checkbox = row.querySelector('.row-checkbox');
      if (checkbox && checkbox.checked) {
        const nameInput = row.querySelector('input[name$="[name]"]');
        const lenInput = row.querySelector('input[name$="[length_in]"]');
        const widInput = row.querySelector('input[name$="[width_in]"]');
        const heiInput = row.querySelector('input[name$="[height_in]"]');
        const priceInput = row.querySelector('input[name$="[price]"]');
        
        items.push({
          import: 1,
          name: nameInput ? nameInput.value : '',
          length_in: lenInput ? lenInput.value : '',
          width_in: widInput ? widInput.value : '',
          height_in: heiInput ? heiInput.value : '',
          price: priceInput ? priceInput.value : ''
        });
      }
    });
    
    // Store JSON in hidden input
    document.getElementById('itemsJsonInput').value = JSON.stringify(items);
    
    // Strip individual form input names so they are not sent to the server (reducing variables count from 1000s to 3)
    this.querySelectorAll('tbody input').forEach(input => {
      input.removeAttribute('name');
    });
  });
}

// Multi-file selection controller
document.addEventListener('DOMContentLoaded', function() {
    const mainInput = document.getElementById('multi-file-input');
    const uploaderBox = document.querySelector('.file-uploader-box');
    const listContainer = document.getElementById('file-list-container');
    const filesList = document.getElementById('selected-files-list');
    const addMoreBtn = document.getElementById('add-more-btn');
    const additionalContainer = document.getElementById('additional-inputs-container');

    if (!mainInput) return; // Only run on step 1 form

    let allInputs = [mainInput];

    if (uploaderBox) {
        uploaderBox.addEventListener('dragover', () => {
            uploaderBox.style.borderColor = '#6366f1';
            uploaderBox.style.background = '#f0f2fe';
        });
        uploaderBox.addEventListener('dragleave', () => {
            uploaderBox.style.borderColor = '#cbd5e1';
            uploaderBox.style.background = '#f8fafc';
        });
        uploaderBox.addEventListener('drop', () => {
            uploaderBox.style.borderColor = '#cbd5e1';
            uploaderBox.style.background = '#f8fafc';
        });
    }

    function renderFileList() {
        filesList.innerHTML = '';
        let totalFiles = 0;

        allInputs.forEach((input, inputIdx) => {
            const files = input.files;
            if (files && files.length > 0) {
                totalFiles += files.length;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const sizeKB = (file.size / 1024).toFixed(1);
                    
                    const item = document.createElement('div');
                    item.style.cssText = "display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; background: #fff;";
                    
                    // Choose icon color based on file type
                    let iconColor = "#64748b";
                    if (file.name.endsWith('.pdf')) iconColor = "#dc2626";
                    else if (file.name.endsWith('.csv')) iconColor = "#16a34a";
                    
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="${iconColor}" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16" style="flex-shrink: 0;">
                                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                <path d="M3 12v1h2v-1H3zm3 0v1h3v-1H6zm4 0v1h3v-1h-3zm-7-2v1h2v-1H3zm3 0v1h3v-1H6zm4 0v1h3v-1h-3zm-7-2v1h2V8H3zm3 0v1h3V8H6zm4 0v1h3V8h-3zm-7-2v1h2V6H3zm3 0v1h3V6H6zm4 0v1h3V6h-3z"/>
                            </svg>
                            <span style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px;" title="${file.name}">${file.name}</span>
                            <span style="font-size: 11px; color: #64748b; flex-shrink: 0;">(${sizeKB} KB)</span>
                        </div>
                        <button type="button" class="btn-remove-file" data-input-idx="${inputIdx}" data-file-name="${file.name}" style="background: transparent; border: none; color: #ef4444; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 6px; flex-shrink: 0;" title="Remove file">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                            </svg>
                        </button>
                    `;
                    filesList.appendChild(item);
                }
            }
        });

        if (totalFiles > 0) {
            listContainer.style.display = 'block';
            mainInput.removeAttribute('required');
        } else {
            listContainer.style.display = 'none';
            mainInput.setAttribute('required', 'required');
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'files[]') {
            renderFileList();
        }
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-file');
        if (btn) {
            const inputIdx = parseInt(btn.getAttribute('data-input-idx'));
            const fileName = btn.getAttribute('data-file-name');
            const targetInput = allInputs[inputIdx];
            
            if (targetInput) {
                const dt = new DataTransfer();
                const files = targetInput.files;
                for (let i = 0; i < files.length; i++) {
                    if (files[i].name !== fileName) {
                        dt.items.add(files[i]);
                    }
                }
                targetInput.files = dt.files;
                
                if (targetInput !== mainInput && targetInput.files.length === 0) {
                    allInputs.splice(inputIdx, 1);
                    targetInput.remove();
                }
                
                renderFileList();
            }
        }
    });

    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            const newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.name = 'files[]';
            newInput.accept = '.pdf,.csv,.txt';
            newInput.multiple = true;
            newInput.style.display = 'none';
            
            additionalContainer.appendChild(newInput);
            allInputs.push(newInput);
            newInput.click();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\import\price_import_pdf.blade.php ENDPATH**/ ?>