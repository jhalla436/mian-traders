

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Company Groups</h2>
    <div class="page-actions">
      <form method="GET" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search name/key..." class="mt-input" style="width:200px;">
        <button class="btn btn-primary btn-sm">Search</button>
      </form>
      <a href="<?php echo e(route('mt.company_groups.create')); ?>" class="btn btn-success btn-sm">+ Add Group</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Key</th>
          <th class="text-right">Sort</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="font-bold"><?php echo e($r->name); ?></td>
            <td><?php echo e($r->key); ?></td>
            <td class="text-right"><?php echo e((int)$r->sort_order); ?></td>
            <td><span class="badge <?php echo e((int)$r->is_active === 1 ? 'badge-success' : 'badge-danger'); ?>"><?php echo e((int)$r->is_active === 1 ? 'Active' : 'Disabled'); ?></span></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.company_groups.edit', $r)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="<?php echo e(route('mt.company_groups.destroy', $r)); ?>" onsubmit="return confirm('Delete group?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="5">No groups found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/company_groups/index.blade.php ENDPATH**/ ?>