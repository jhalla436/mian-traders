<?php $__env->startSection('content'); ?>
<?php
  $from = $from ?? '';
  $to = $to ?? '';
  $total = $total ?? 0;
?>

  <div class="page-header">
    <h2 class="page-title">Expenses</h2>
    <a href="<?php echo e(route('mt.expenses.create')); ?>" class="btn btn-success btn-sm">+ Add Expense</a>
  </div>

  <div class="filter-bar">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">From</span>
        <input type="date" name="from" value="<?php echo e($from); ?>" class="mt-input">
      </div>
      <div class="filter-group">
        <span class="filter-label">To</span>
        <input type="date" name="to" value="<?php echo e($to); ?>" class="mt-input">
      </div>
      <button class="btn btn-primary btn-sm">Filter</button>
    </form>
    <div class="ml-auto">
      <div class="total-badge">Total: <?php echo e(number_format((float)$total,2)); ?></div>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Title</th>
          <th>Category</th>
          <th>Vendor</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($r->expense_date?->format('Y-m-d')); ?></td>
            <td class="font-bold"><?php echo e($r->title); ?></td>
            <td><?php echo e($r->category ?? '-'); ?></td>
            <td><?php echo e($r->vendor ?? '-'); ?></td>
            <td class="text-right font-bold"><?php echo e(number_format((float)$r->amount,2)); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="5">No expenses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\expenses\index.blade.php ENDPATH**/ ?>