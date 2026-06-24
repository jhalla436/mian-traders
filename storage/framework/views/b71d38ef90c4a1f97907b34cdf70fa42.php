<?php $__env->startSection('content'); ?>
<?php
  $companies = $companies ?? collect();
  $categories = $categories ?? collect();
  $discountTypes = $discountTypes ?? collect();
?>

<div class="page-header">
  <h2 class="page-title">Import Price List (CSV)</h2>
  <a href="<?php echo e(route('mt.products.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
</div>

<div class="card" style="max-width:860px;">
  <div class="text-muted text-sm" style="margin-bottom:16px;line-height:1.6;">
    Upload a <b>CSV</b> file (you can export your Excel sheet as CSV). The importer can create new products and (optionally) update existing ones.
    <div style="margin-top:6px;">
      <a href="<?php echo e(route('mt.products.import_template')); ?>" style="color:#6366f1;font-weight:700;">Download CSV Template</a>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('mt.products.import_process')); ?>" enctype="multipart/form-data" class="form-stack">
    <?php echo csrf_field(); ?>
    <div class="form-group">
      <label class="form-label">CSV File(s)</label>
      
      <div class="file-uploader-box" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 24px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s; position: relative;">
        <input type="file" id="multi-file-input" name="files[]" accept=".csv,text/csv" required style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" multiple>
        <div class="upload-placeholder">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-bottom: 8px; display: inline-block;">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
          </svg>
          <p style="font-weight: 600; color: #334155; margin-bottom: 4px;">Click to select multiple CSV files, or drag them here</p>
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

      <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      <?php $__errorArgs = ['files'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      <?php $__errorArgs = ['files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Default Company (optional)</label>
        <select name="default_company_id" class="mt-select">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($co->id); ?>"><?php echo e($co->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Default Category (optional)</label>
        <select name="default_category_id" class="mt-select">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Default Discount Type (optional)</label>
        <select name="default_discount_type_id" class="mt-select">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($dt->id); ?>"><?php echo e($dt->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Mode</label>
        <select name="mode" class="mt-select">
          <option value="upsert">Upsert (Create + Update)</option>
          <option value="create_only">Create Only (Skip existing)</option>
        </select>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Default Active</label>
        <select name="default_is_active" class="mt-select">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div></div>
    </div>

    <div class="flex gap-12 items-center flex-wrap">
      <button type="submit" class="btn btn-success">Import</button>
    </div>

    <div class="text-xs text-muted" style="margin-top:8px;">
      CSV columns: <b>name</b>, sku, mrp, selling_price_default, purchase_price_manual, stock_qty, low_stock_alert_qty, pricing_mode, shell_rate, width_in, length_in, height_in, sheet_full_w, sheet_full_l, category, company.
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainInput = document.getElementById('multi-file-input');
    const uploaderBox = document.querySelector('.file-uploader-box');
    const listContainer = document.getElementById('file-list-container');
    const filesList = document.getElementById('selected-files-list');
    const addMoreBtn = document.getElementById('add-more-btn');
    const additionalContainer = document.getElementById('additional-inputs-container');

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
                    
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#16a34a" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16" style="flex-shrink: 0;">
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
            newInput.accept = '.csv,text/csv';
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

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/import.blade.php ENDPATH**/ ?>