
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Headings</h2>
    <a href="<?php echo e(route('mt.headings.create')); ?>" class="btn btn-primary btn-sm">+ Add Heading</a>
  </div>
  <div class="table-card">
    <table class="mt-table">
      <thead><tr><th>Heading</th><th>Category</th><th>Variants</th><th>Actions</th></tr></thead>
      <tbody>
        <?php $__currentLoopData = $headings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="font-bold"><?php echo e($h->name); ?></td>
            <td><?php echo e($h->category?->name); ?></td>
            <td><?php echo e($h->variants()->count()); ?></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.variants.index', $h)); ?>" class="btn btn-secondary btn-xs">Variants</a>
                <form method="POST" action="<?php echo e(route('mt.headings.destroy', $h)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($headings->count() === 0): ?>
          <tr class="empty-row"><td colspan="4">No headings yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\headings\index.blade.php ENDPATH**/ ?>