<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Sheet Designs</h2>
    <div class="page-actions">
      <form method="GET" action="<?php echo e(route('mt.sheet_designs.index')); ?>" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search design..." class="mt-input" style="width:220px;">
        <button class="btn btn-secondary btn-sm">Search</button>
      </form>
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
          <tr class="empty-row"><td colspan="5">No sheet designs added yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap">
      <?php echo e($sheetDesigns->appends(request()->query())->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\sheet_designs\index.blade.php ENDPATH**/ ?>