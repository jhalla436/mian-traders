


<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title"><?php echo e($heading->name); ?></h2>
    <a href="<?php echo e(route('mt.pos.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <p>Variants page will show pictures + codes here.</p>
  </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\pos\heading.blade.php ENDPATH**/ ?>