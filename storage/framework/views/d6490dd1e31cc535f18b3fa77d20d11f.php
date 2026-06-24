
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">New Stock Movement</h2>
    <a href="<?php echo e(route('mt.stock_movements.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <?php if(session('error')): ?>
    <div class="mt-alert mt-alert-error"><?php echo e(session('error')); ?></div>
  <?php endif; ?>
  <div class="card" style="max-width:720px;">
    <form method="POST" action="<?php echo e(route('mt.stock_movements.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label class="form-label">Product</label>
        <select name="product_id" required class="mt-select">
          <option value="">-- select --</option>
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->id); ?>" <?php if((int)old('product_id', $product?->id) === (int)$p->id): echo 'selected'; endif; ?>>
              <?php echo e($p->name); ?> (Stock: <?php echo e(number_format((float)($p->shop_stock_qty ?? $p->stock_qty ?? 0),2)); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="type" required class="mt-select">
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(old('type') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Quantity</label>
        <input name="qty" type="number" step="0.01" required value="<?php echo e(old('qty')); ?>" class="mt-input">
        <span class="text-xs text-muted">If type is <b>SET</b>, this becomes the exact stock value.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="<?php echo e(old('note')); ?>" placeholder="purchase, transport, damaged, etc." class="mt-input">
      </div>
      <button type="submit" class="btn btn-primary" style="justify-self:start;">Save</button>
      <?php if($errors->any()): ?>
        <div class="errors-box">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\stock_movements\create.blade.php ENDPATH**/ ?>