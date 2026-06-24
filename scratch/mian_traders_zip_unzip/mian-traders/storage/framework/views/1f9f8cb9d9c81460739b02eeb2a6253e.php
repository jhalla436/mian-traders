

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Discount Types</h2>
    <a href="<?php echo e(route('mt.discount_types.create')); ?>" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Add</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Active</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($t->name); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($t->is_active ? 'Yes' : 'No'); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              <a href="<?php echo e(route('mt.discount_types.edit', $t)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>
              <form method="POST" action="<?php echo e(route('mt.discount_types.destroy', $t)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" style="padding:6px 10px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($types->count()===0): ?>
          <tr><td colspan="3" style="padding:8px;">No discount types yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_types/index.blade.php ENDPATH**/ ?>