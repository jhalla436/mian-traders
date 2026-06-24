

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Product</h2>
    <a href="<?php echo e(route('mt.products.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.products.store')); ?>" class="form-stack" id="product-form">
      <?php echo csrf_field(); ?>
      <div id="form-content">
        <?php echo $__env->make('mt.products.form', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
      <div style="height:100px"></div>
    </form>
    <div style="position:fixed;bottom:20px;left:0;right:0;display:flex;justify-content:center;z-index:9999;pointer-events:none;">
      <button type="submit" form="product-form" class="btn btn-success btn-lg" style="padding:14px 32px;border-radius:8px;cursor:pointer;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);pointer-events:all;">✓ Save Product</button>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/create.blade.php ENDPATH**/ ?>