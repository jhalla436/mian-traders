

<?php $__env->startSection('content'); ?>
<?php
  $q = $q ?? '';
  $role = $role ?? '';
  $roles = ['' => 'All', 'admin'=>'Admin', 'manager'=>'Manager', 'cashier'=>'Cashier'];
?>

<div class="page-header">
  <h2 class="page-title">Users</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input type="hidden" name="role" value="<?php echo e($role); ?>">
      <input name="q" value="<?php echo e($q); ?>" placeholder="Search name/email..." class="mt-input" style="width:200px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="filter-bar" style="justify-content:space-between;">
  <div class="mt-tabs">
    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('mt.users.index', ['role'=>$k])); ?>" class="mt-tab<?php echo e($role===$k ? ' active' : ''); ?>"><?php echo e($label); ?></a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <a href="<?php echo e(route('mt.users.create')); ?>" class="btn btn-success btn-sm">+ Add User</a>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="font-bold"><?php echo e($u->name); ?></td>
          <td><?php echo e($u->email); ?></td>
          <td><span class="badge badge-info"><?php echo e($u->role); ?></span></td>
          <td><span class="badge <?php echo e((int)$u->is_active === 1 ? 'badge-success' : 'badge-danger'); ?>"><?php echo e((int)$u->is_active === 1 ? 'Active' : 'Disabled'); ?></span></td>
          <td>
            <div class="actions">
              <a href="<?php echo e(route('mt.users.edit', $u)); ?>" class="btn btn-secondary btn-xs">Edit / Reset</a>
              <form method="POST" action="<?php echo e(route('mt.users.destroy', $u)); ?>" onsubmit="return confirm('Delete user?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-danger btn-xs">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr class="empty-row"><td colspan="5">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <div class="pagination-wrap"><?php echo e($users->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\users\index.blade.php ENDPATH**/ ?>