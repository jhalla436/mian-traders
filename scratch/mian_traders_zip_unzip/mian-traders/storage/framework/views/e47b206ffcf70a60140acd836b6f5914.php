<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Recurring Expenses</h2>
    <div style="font-size:12px;color:#6b7280;max-width:520px;">
      Example: Shop Rent every 21st. On the day, when you open Dashboard, the system auto-creates that expense once per month.
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <h3 style="margin:0 0 10px 0;">Add New</h3>
    <form method="POST" action="<?php echo e(route('mt.recurring_expenses.store')); ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      <?php echo csrf_field(); ?>
      <div style="flex:1;min-width:220px;">
        <div style="font-size:12px;color:#6b7280;">Title</div>
        <input name="title" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Shop Rent">
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Category</div>
        <input name="category" style="padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="rent">
      </div>
      <div style="flex:1;min-width:200px;">
        <div style="font-size:12px;color:#6b7280;">Vendor</div>
        <input name="vendor" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Landlord">
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Day of Month</div>
        <input type="number" min="1" max="28" name="day_of_month" value="21" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:120px;text-align:right;">
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Use 1-28 (safe)</div>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Amount</div>
        <input type="number" step="0.01" name="amount" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:160px;text-align:right;">
      </div>
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;">Save</button>
    </form>
    <?php if($errors->any()): ?>
      <div style="margin-top:10px;color:#dc2626;font-size:12px;"><?php echo e(implode(' | ', $errors->all())); ?></div>
    <?php endif; ?>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <h3 style="margin:0 0 10px 0;">List</h3>
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Title</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Vendor</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Day</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Amount</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;"><?php echo e($r->title); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->vendor ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->day_of_month); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:800;"><?php echo e(number_format((float)$r->amount,2)); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <?php if($r->is_active): ?>
                <span style="padding:4px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-weight:800;">Active</span>
              <?php else: ?>
                <span style="padding:4px 8px;border-radius:999px;background:#fee2e2;color:#991b1b;font-weight:800;">Disabled</span>
              <?php endif; ?>
              <div style="font-size:12px;color:#6b7280;">Last: <?php echo e($r->last_generated_month ?? '-'); ?></div>
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <form method="POST" action="<?php echo e(route('mt.recurring_expenses.update', $r)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="is_active" value="<?php echo e($r->is_active ? 0 : 1); ?>">
                <button style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;border:0;">
                  <?php echo e($r->is_active ? 'Disable' : 'Enable'); ?>

                </button>
              </form>

              <form method="POST" action="<?php echo e(route('mt.recurring_expenses.destroy', $r)); ?>" style="display:inline;" onsubmit="return confirm('Delete this recurring expense?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button style="padding:6px 10px;border-radius:8px;background:#ef4444;color:#fff;border:0;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" style="padding:12px;">No recurring expenses.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/recurring_expenses/index.blade.php ENDPATH**/ ?>