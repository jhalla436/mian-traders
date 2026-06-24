

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Company Groups</h2>

    <form method="GET" style="display:flex;gap:8px;align-items:center;">
      <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search name/key..."
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>
    </form>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <a href="<?php echo e(route('mt.company_groups.create')); ?>"
       style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">
      + Add Group
    </a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Key</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Sort</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;"><?php echo e($r->name); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->key); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e((int)$r->sort_order); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <?php echo e((int)$r->is_active === 1 ? 'Active' : 'Disabled'); ?>

            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <a href="<?php echo e(route('mt.company_groups.edit', $r)); ?>"
                 style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>

              <form method="POST" action="<?php echo e(route('mt.company_groups.destroy', $r)); ?>"
                    style="display:inline-block;margin-left:6px;" onsubmit="return confirm('Delete group?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button style="padding:6px 10px;border-radius:8px;border:0;background:#b91c1c;color:#fff;cursor:pointer;">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" style="padding:12px;">No groups found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($rows->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/company_groups/index.blade.php ENDPATH**/ ?>