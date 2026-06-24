

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">
      Sizes for: <strong><?php echo e($category->name); ?></strong>
      <?php if($category->company): ?>
        <span class="text-muted">(<?php echo e($category->company->name); ?>)</span>
      <?php endif; ?>
    </h2>
    <a href="<?php echo e(route('mt.categories.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <h3 style="margin-top: 0;"><span>📐 Add New Size(s)</span></h3>

    <?php if($sizes->count() > 0): ?>
      <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px;">
        <form method="POST" action="<?php echo e(route('mt.categories.sizes.destroy_all', $category)); ?>" style="display:inline-block;">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete ALL sizes for this category? This cannot be undone.');">
            🗑️ Delete All Sizes
          </button>
        </form>
      </div>
    <?php endif; ?>

    <!-- Toggle between Single and Bulk -->
    <div style="margin-bottom: 20px; display: flex; gap: 10px; border-bottom: 2px solid #eee; padding-bottom: 10px; flex-wrap: wrap;">
      <button type="button" class="mode-toggle active" data-mode="single" style="padding: 10px 20px; border: none; background: #f0f0f0; cursor: pointer; border-bottom: 3px solid transparent;">
        ➕ Single Size
      </button>
      <button type="button" class="mode-toggle" data-mode="bulk" style="padding: 10px 20px; border: none; background: #f0f0f0; cursor: pointer; border-bottom: 3px solid transparent;">
        📋 Bulk Add (Multiple)
      </button>
    </div>

    <!-- Single Size Form -->
    <form id="single-form" method="POST" action="<?php echo e(route('mt.categories.sizes.store', $category)); ?>" class="form-grid" style="display: block;">
      <?php echo csrf_field(); ?>
      
      <div class="form-group">
        <label class="form-label">Length (inches)</label>
        <input type="number" name="length_in" step="0.01" min="0.01" class="mt-input" required>
        <?php $__errorArgs = ['length_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-group">
        <label class="form-label">Width (inches)</label>
        <input type="number" name="width_in" step="0.01" min="0.01" class="mt-input" required>
        <?php $__errorArgs = ['width_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-group">
        <label class="form-label">Height (inches)</label>
        <input type="number" name="height_in" step="0.01" min="0.01" class="mt-input" required>
        <?php $__errorArgs = ['height_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-group" style="grid-column: 1 / -1;">
        <label class="form-label">Size type</label>
        <select name="size_type" class="mt-select">
          <option value="standard">Standard</option>
          <option value="uncovered_sofa">Uncovered - Sofa Seats</option>
          <option value="uncovered_slab">Uncovered - Slab Sheets</option>
        </select>
        <?php $__errorArgs = ['size_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <button type="submit" class="btn btn-success" style="justify-self: start; align-self: end;">Add Size</button>
    </form>

    <!-- Bulk Add Form -->
    <form id="bulk-form" method="POST" action="<?php echo e(route('mt.categories.sizes.store', $category)); ?>" style="display: none;">
      <?php echo csrf_field(); ?>
      
      <div class="form-group" style="grid-column: 1 / -1;">
        <label class="form-label">Enter Sizes (one per line)</label>
        <small style="display: block; margin-bottom: 8px; color: #666;">
          Format: <code>72*36*4</code>, <code>72*39*4</code>, <code>72 36 4</code> or <code>72,39,4</code> (quotes optional)<br>
          Example:
          Dash format is also supported: <code>22-22-4</code><br>
        </small>
        <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
          <button type="button" class="btn btn-secondary btn-sm size-preset" data-size-type="uncovered_sofa">Use uncovered sizes</button>
          <button type="button" class="btn btn-secondary btn-sm size-preset" data-size-type="uncovered_slab">Use slab sheet sizes</button>
        </div>
        <textarea 
          name="bulk_sizes" 
          class="mt-input" 
          rows="15" 
          placeholder="72*36*4&#10;72*39*4&#10;72*42*4&#10;78*36*4&#10;78*39*4&#10;72 42 4&#10;72,45,4"
          required
          style="font-family: monospace; font-size: 13px;"
        ></textarea>
        <?php $__errorArgs = ['bulk_sizes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-group" style="grid-column: 1 / -1;">
        <label class="form-label">Size type</label>
        <select name="size_type" class="mt-select">
          <option value="standard">Standard</option>
          <option value="uncovered_sofa">Uncovered - Sofa Seats</option>
          <option value="uncovered_slab">Uncovered - Slab Sheets</option>
        </select>
        <?php $__errorArgs = ['size_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <button type="submit" class="btn btn-success">Add All Sizes</button>
    </form>
  </div>

  <div class="card" style="margin-top: 24px;">
    <?php
      $standardSizes = $sizes->where('size_type', 'standard');
      $sofaSizes = $sizes->where('size_type', 'uncovered_sofa');
      $slabSizes = $sizes->where('size_type', 'uncovered_slab');
    ?>

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
      <h3 style="margin-top: 0;">Sizes (<?php echo e($sizes->count()); ?>)</h3>
      <?php if($sizes->count() > 0): ?>
        <div>
          <span style="font-size: 13px; color: #555;">Type legend:</span>
          <span style="margin-left: 10px;">Standard</span>
          <span style="margin-left: 10px;">• Uncovered Sofa Seats</span>
          <span style="margin-left: 10px;">• Uncovered Slab Sheets</span>
        </div>
      <?php endif; ?>
    </div>

    <?php if($sizes->count() > 0): ?>
      <form id="delete-selected-category-sizes-form" method="POST" action="<?php echo e(route('mt.categories.sizes.destroy_selected', $category)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>

        <div style="margin-bottom: 12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
          <button id="delete-selected-sizes-btn" type="button" class="btn btn-danger btn-sm" disabled>
            Delete selected sizes
          </button>
          <label style="display:flex; align-items:center; gap:6px; font-size: 14px; color: #555;">
            <input type="checkbox" id="select-all-sizes" style="width: 14px; height: 14px;" />
            Select all sizes
          </label>
        </div>

        <div class="table-card">
          <table class="mt-table">
            <thead>
              <tr>
                <th style="width:36px;"></th>
                <th>Length</th>
                <th>Width</th>
                <th>Height</th>
                <th>Type</th>
                <th>Display</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><input class="size-row-check" type="checkbox" name="size_ids[]" value="<?php echo e($size->id); ?>" /></td>
                  <td><?php echo e($size->length_in); ?>"</td>
                  <td><?php echo e($size->width_in); ?>"</td>
                  <td><?php echo e($size->height_in); ?>"</td>
                  <td>
                    <?php if($size->size_type === 'uncovered_sofa'): ?>
                      Uncovered Sofa Seats
                    <?php elseif($size->size_type === 'uncovered_slab'): ?>
                      Uncovered Slab Sheets
                    <?php else: ?>
                      Standard
                    <?php endif; ?>
                  </td>
                  <td class="font-bold"><?php echo e($size->getDisplayName()); ?></td>
                  <td>
                    <form method="POST" action="<?php echo e(route('mt.categories.sizes.destroy', [$category, $size])); ?>" style="display: inline;">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button onclick="return confirm('Delete this size?')" class="btn btn-danger btn-xs">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </form>
    <?php else: ?>
      <div style="padding: 20px; text-align: center; color: #999;">
        No sizes defined yet. Add one above to get started!
      </div>
    <?php endif; ?>
  </div>

  <div style="margin-top: 24px; padding: 16px; background: #f0f8ff; border-radius: 4px;">
    <strong>💡 Tip:</strong> When you create a product for this category, all these sizes will automatically create variants.
  </div>

  <script>
    const uncoveredPresetSizes = [
      '22-22-4',
      '22-22-3',
      '22-22-2',
      '22-18-3',
      '18-18-3',
      '44-22-4',
      '44-22-3',
      '44-22-2',
      '44-18-4',
      '44-18-3',
      '66-22-4',
      '66-22-3',
      '66-22-2',
      '66-18-4',
      '66-18-3',
      '66-22-5',
      '44-22-5',
      '22-22-5',
      '66-22-6',
      '44-22-6',
      '22-22-6',
    ];

    document.querySelectorAll('.size-preset').forEach(btn => {
      btn.addEventListener('click', function() {
        const bulkForm = document.getElementById('bulk-form');
        const textarea = bulkForm.querySelector('textarea[name="bulk_sizes"]');
        const sizeType = bulkForm.querySelector('select[name="size_type"]');

        textarea.value = uncoveredPresetSizes.join('\n');
        sizeType.value = this.dataset.sizeType;
      });
    });

    document.querySelectorAll('.mode-toggle').forEach(btn => {
      btn.addEventListener('click', function() {
        const mode = this.dataset.mode;
        
        // Update button states
        document.querySelectorAll('.mode-toggle').forEach(b => {
          b.classList.remove('active');
          b.style.borderBottomColor = 'transparent';
        });
        this.classList.add('active');
        this.style.borderBottomColor = '#007bff';
        
        // Toggle forms
        document.getElementById('single-form').style.display = mode === 'single' ? 'block' : 'none';
        document.getElementById('bulk-form').style.display = mode === 'bulk' ? 'block' : 'none';
      });
    });

    const sizeSelectAll = document.getElementById('select-all-sizes');
    const sizeRows = () => Array.from(document.querySelectorAll('.size-row-check'));
    const deleteSelectedBtn = document.getElementById('delete-selected-sizes-btn');

    function updateSizeButtons() {
      const hasChecked = sizeRows().some(ch => ch.checked);
      if (deleteSelectedBtn) deleteSelectedBtn.disabled = !hasChecked;
    }

    if (sizeSelectAll) {
      sizeSelectAll.addEventListener('change', function() {
        sizeRows().forEach(ch => ch.checked = sizeSelectAll.checked);
        updateSizeButtons();
      });
    }

    document.addEventListener('change', function(e) {
      if (e.target && e.target.classList && e.target.classList.contains('size-row-check')) {
        updateSizeButtons();
      }
    });

    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function() {
        const checked = sizeRows().filter(c => c.checked).map(c => c.value);
        if (checked.length === 0) return;
        if (!confirm('Delete ' + checked.length + ' selected sizes? This cannot be undone.')) return;
        document.getElementById('delete-selected-category-sizes-form').submit();
      });
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\categories\sizes.blade.php ENDPATH**/ ?>