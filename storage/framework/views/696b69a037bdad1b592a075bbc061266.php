
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Edit Category</h2>
    <a href="<?php echo e(route('mt.categories.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.categories.update', $category)); ?>" class="form-stack">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('mt.categories.form', [
        'category' => $category,
        'companies' => $companies,
        'availableParents' => $availableParents
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button class="btn btn-primary" style="justify-self:start;">Update Category</button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\categories\edit.blade.php ENDPATH**/ ?>