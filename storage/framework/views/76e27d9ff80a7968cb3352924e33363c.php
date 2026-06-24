<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Shop</h2>
    <a href="<?php echo e(route('mt.shops.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:720px;">
    <form method="POST" action="<?php echo e(route('mt.shops.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label class="form-label">Shop Name</label>
        <input name="name" placeholder="Shop name" required class="mt-input">
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Owner Name</label>
          <input name="owner_name" placeholder="Owner name" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input name="phone" placeholder="Phone" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Address</label>
        <input name="address" placeholder="Address" class="mt-input">
      </div>
      <label class="form-checkbox">
        <input type="checkbox" name="is_active" checked>
        Active
      </label>
      <button class="btn btn-success" style="justify-self:start;">Save Shop</button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\shops\create.blade.php ENDPATH**/ ?>