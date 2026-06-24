

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Product</h2>
    <a href="<?php echo e(route('mt.products.index')); ?>" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="<?php echo e(route('mt.products.store')); ?>" style="display:grid;gap:12px;">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('mt.products.form', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button style="padding:12px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
        Save Product
      </button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/create.blade.php ENDPATH**/ ?>