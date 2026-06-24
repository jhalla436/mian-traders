

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Categories</h2>
    <a href="<?php echo e(route('mt.categories.create')); ?>" style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">Add Category</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Group</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f3f4f6;padding:10px;"><?php echo e($c->name); ?></td>
            <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
              <?php echo e($c->group_key ?? '-'); ?>

            </td>
            <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
              <a href="<?php echo e(route('mt.categories.edit', $c)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>

              <form method="POST" action="<?php echo e(route('mt.categories.destroy', $c)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button onclick="return confirm('Delete category?')"
                        style="padding:6px 10px;border-radius:8px;border:0;background:#b91c1c;color:#fff;cursor:pointer;margin-left:6px;">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="3" style="padding:12px;">No categories yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($categories->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/categories/index.blade.php ENDPATH**/ ?>