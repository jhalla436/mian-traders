
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Category</h2>
    <a href="<?php echo e(route('mt.categories.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.categories.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('mt.categories.form', [
        'category' => $category,
        'companies' => $companies,
        'selectedCompanyId' => $selectedCompanyId ?? null,
        'parentCategory' => $parentCategory ?? null,
        'parentId' => $parentId ?? null,
        'availableParents' => $availableParents ?? collect()
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button class="btn btn-success" style="justify-self:start;">Save Category</button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\categories\create.blade.php ENDPATH**/ ?>