

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Discount Types</h2>
    <a href="<?php echo e(route('mt.discount_types.create')); ?>" class="btn btn-primary btn-sm">+ Add</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="font-bold"><?php echo e($t->name); ?></td>
            <td><span class="badge <?php echo e($t->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($t->is_active ? 'Yes' : 'No'); ?></span></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.discount_types.edit', $t)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="<?php echo e(route('mt.discount_types.destroy', $t)); ?>">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($types->count()===0): ?>
          <tr class="empty-row"><td colspan="3">No discount types yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_types/index.blade.php ENDPATH**/ ?>