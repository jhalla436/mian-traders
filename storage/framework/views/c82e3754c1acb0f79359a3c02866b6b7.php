
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Edit Discount Type</h2>
    <a href="<?php echo e(route('mt.discount_types.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.discount_types.update', $discountType)); ?>" class="flex gap-12 flex-wrap items-center">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <input name="name" value="<?php echo e($discountType->name); ?>" required class="mt-input" style="min-width:300px;max-width:400px;">
      <label class="form-checkbox"><input type="checkbox" name="is_active" value="1" <?php if($discountType->is_active): echo 'checked'; endif; ?>> Active</label>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <?php if($errors->any()): ?>
      <div class="errors-box" style="margin-top:16px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_types/edit.blade.php ENDPATH**/ ?>