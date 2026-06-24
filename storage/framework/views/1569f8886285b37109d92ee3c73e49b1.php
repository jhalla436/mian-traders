<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Companies</h2>
    <div class="page-actions">
      <form method="GET" action="<?php echo e(route('mt.companies.index')); ?>" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search company..." class="mt-input" style="width:220px;">
        <button class="btn btn-secondary btn-sm">Search</button>
      </form>
      <a href="<?php echo e(route('mt.companies.create')); ?>" class="btn btn-primary btn-sm">+ Add Company</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Group</th>
          <th>Phone</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <div class="font-bold"><?php echo e($c->name); ?></div>
              <?php if(!empty($c->address)): ?>
                <div class="text-xs text-muted"><?php echo e($c->address); ?></div>
              <?php endif; ?>
            </td>
            <td><?php echo e($c->group_key ?? '-'); ?></td>
            <td><?php echo e($c->phone_main ?? '-'); ?></td>
            <td><span class="badge <?php echo e($c->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($c->is_active ? 'Yes' : 'No'); ?></span></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.company_contacts.index', $c)); ?>" class="btn btn-success btn-xs">Contacts</a>
                <a href="<?php echo e(route('mt.company_ledger.index', $c)); ?>" class="btn btn-teal btn-xs">Ledger</a>
                <a href="<?php echo e(route('mt.companies.edit', $c)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="<?php echo e(route('mt.companies.destroy', $c)); ?>" onsubmit="return confirm('Delete this company?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="5">No companies added yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($companies->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/companies/index.blade.php ENDPATH**/ ?>