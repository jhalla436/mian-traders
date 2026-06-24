<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <div>
      <h2 class="page-title">Recurring Expenses</h2>
      <div class="page-subtitle">Example: Shop Rent every 21st. On the day, the system auto-creates that expense once per month.</div>
    </div>
  </div>

  <div class="card mb-16">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Add New</h3>
    <form method="POST" action="<?php echo e(route('mt.recurring_expenses.store')); ?>" class="flex gap-12 flex-wrap items-end">
      <?php echo csrf_field(); ?>
      <div class="form-group" style="flex:1;min-width:200px;">
        <label class="form-label">Title</label>
        <input name="title" required class="mt-input" placeholder="Shop Rent">
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <input name="category" class="mt-input" placeholder="rent">
      </div>
      <div class="form-group" style="flex:1;min-width:180px;">
        <label class="form-label">Vendor</label>
        <input name="vendor" class="mt-input" placeholder="Landlord">
      </div>
      <div class="form-group">
        <label class="form-label">Day of Month</label>
        <input type="number" min="1" max="28" name="day_of_month" value="21" required class="mt-input" style="width:100px;text-align:right;">
        <span class="text-xs text-muted">Use 1–28</span>
      </div>
      <div class="form-group">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" required class="mt-input" style="width:140px;text-align:right;">
      </div>
      <button class="btn btn-success btn-sm">Save</button>
    </form>
    <?php if($errors->any()): ?>
      <div class="form-error" style="margin-top:10px;"><?php echo e(implode(' | ', $errors->all())); ?></div>
    <?php endif; ?>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Vendor</th>
          <th>Day</th>
          <th class="text-right">Amount</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="font-bold"><?php echo e($r->title); ?></td>
            <td><?php echo e($r->vendor ?? '-'); ?></td>
            <td><?php echo e($r->day_of_month); ?></td>
            <td class="text-right font-bold"><?php echo e(number_format((float)$r->amount,2)); ?></td>
            <td>
              <span class="badge <?php echo e($r->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($r->is_active ? 'Active' : 'Disabled'); ?></span>
              <div class="text-xs text-muted" style="margin-top:4px;">Last: <?php echo e($r->last_generated_month ?? '-'); ?></div>
            </td>
            <td>
              <div class="actions">
                <form method="POST" action="<?php echo e(route('mt.recurring_expenses.update', $r)); ?>">
                  <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                  <input type="hidden" name="is_active" value="<?php echo e($r->is_active ? 0 : 1); ?>">
                  <button class="btn btn-primary btn-xs"><?php echo e($r->is_active ? 'Disable' : 'Enable'); ?></button>
                </form>
                <form method="POST" action="<?php echo e(route('mt.recurring_expenses.destroy', $r)); ?>" onsubmit="return confirm('Delete this recurring expense?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No recurring expenses.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\recurring_expenses\index.blade.php ENDPATH**/ ?>