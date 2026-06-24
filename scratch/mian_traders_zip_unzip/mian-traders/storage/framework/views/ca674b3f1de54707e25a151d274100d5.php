

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Discount Type</h2>
    <a href="<?php echo e(route('mt.discount_types.index')); ?>" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="<?php echo e(route('mt.discount_types.store')); ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      <?php echo csrf_field(); ?>
      <input name="name" placeholder="e.g. covered, uncovered, spring, jumbolon, hardware" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:320px;" />
      <label style="display:flex;gap:8px;align-items:center;">
        <input type="checkbox" name="is_active" value="1" checked />
        Active
      </label>
      <button type="submit" style="padding:8px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;cursor:pointer;">Save</button>
    </form>

    <?php if($errors->any()): ?>
      <div style="margin-top:12px;background:#fee2e2;padding:10px;border-radius:10px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_types/create.blade.php ENDPATH**/ ?>