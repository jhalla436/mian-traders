<?php $__env->startSection('content'); ?>
<?php
  $from = $from ?? '';
  $to = $to ?? '';
  $total = $total ?? 0;
?>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Expenses</h2>
    <a href="<?php echo e(route('mt.expenses.create')); ?>" style="padding:10px 12px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;">+ Add Expense</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      <div>
        <div style="font-size:12px;color:#6b7280;">From</div>
        <input type="date" name="from" value="<?php echo e($from); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">To</div>
        <input type="date" name="to" value="<?php echo e($to); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Filter</button>
    </form>

    <div style="margin-left:auto;padding:10px 12px;border-radius:10px;background:#0f172a;color:#fff;">
      Total: <b><?php echo e(number_format((float)$total,2)); ?></b>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Title</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Category</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Vendor</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->expense_date?->format('Y-m-d')); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;"><?php echo e($r->title); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->category ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->vendor ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:800;"><?php echo e(number_format((float)$r->amount,2)); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" style="padding:12px;">No expenses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/expenses/index.blade.php ENDPATH**/ ?>