

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Company</h2>
    <a href="<?php echo e(route('mt.companies.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.companies.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('mt.companies.form', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button class="btn btn-success">
        Save Company
      </button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/companies/create.blade.php ENDPATH**/ ?>