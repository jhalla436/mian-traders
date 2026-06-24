<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Sheet Designs</h2>
    <div class="page-actions">
      <form method="GET" action="<?php echo e(route('mt.sheet_designs.index')); ?>" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search design..." class="mt-input" style="width:220px;">
        <button class="btn btn-secondary btn-sm">Search</button>
      </form>
      <a href="<?php echo e(route('mt.sheet_designs.bulk_form')); ?>" class="btn btn-secondary btn-sm" style="margin-right: 4px;">Bulk Creator</a>
      <a href="<?php echo e(route('mt.sheet_designs.create')); ?>" class="btn btn-primary btn-sm">+ Add Design</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Finish</th>
          <th>Color Group</th>
          <th>Company Codes</th>
          <th class="text-center">Linked Products</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $sheetDesigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <div class="font-bold"><?php echo e($sd->name); ?></div>
            </td>
            <td><?php echo e($sd->finish ?? '-'); ?></td>
            <td><?php echo e($sd->color_group ?? '-'); ?></td>
            <td>
              <?php if(is_array($sd->sheet_codes) && count($sd->sheet_codes) > 0): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                  <?php $__currentLoopData = $sd->sheet_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compId => $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $compName = \App\Models\Company::find($compId)->name ?? 'Company #' . $compId;
                      $compNameClean = str_ireplace([' lamination', ' board'], '', $compName);
                    ?>
                    <span class="badge badge-gray" style="background-color: #f1f1f1; color: #333; border: 1px solid #ccc; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; display: inline-block;" title="<?php echo e($compName); ?>">
                      <strong><?php echo e($compNameClean); ?>:</strong> <?php echo e($code); ?>

                    </span>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              <?php else: ?>
                <span class="text-muted" style="font-size: 0.85rem; color: #999;">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <span class="badge <?php echo e($sd->products_count > 0 ? 'badge-info' : 'badge-gray'); ?>">
                <?php echo e($sd->products_count); ?>

              </span>
            </td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.sheet_designs.edit', $sd)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="<?php echo e(route('mt.sheet_designs.destroy', $sd)); ?>" onsubmit="return confirm('Are you sure you want to delete this sheet design? All linked products will be unlinked (set to no design) but not deleted.')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No sheet designs added yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap">
      <?php echo e($sheetDesigns->appends(request()->query())->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sheet_designs/index.blade.php ENDPATH**/ ?>