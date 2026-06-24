

<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h2 class="page-title">Add User</h2>
  <a href="<?php echo e(route('mt.users.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
</div>

<div class="card">
  <form method="POST" action="<?php echo e(route('mt.users.store')); ?>" class="form-stack">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('mt.users.form', ['user'=>$user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button class="btn btn-success">Save User</button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\users\create.blade.php ENDPATH**/ ?>