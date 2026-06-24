<?php $__env->startSection('content'); ?>
<?php
  $companyId = $companyId ?? '';
  $from = $from ?? '';
  $to = $to ?? '';
?>

  <div class="page-header">
    <h2 class="page-title">Purchases (Stock In)</h2>
    <a href="<?php echo e(route('mt.purchases.create')); ?>" class="btn btn-success btn-sm">+ New Purchase</a>
  </div>

  <div class="filter-bar">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">Company</span>
        <select name="company_id" class="mt-select" style="min-width:220px;">
          <option value="">All</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if((string)$companyId === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
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
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Company/Supplier</th>
          <th>Invoice</th>
          <th class="text-right">Goods</th>
          <th class="text-right">Transport</th>
          <th class="text-right">Paid</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($r->purchase_date?->format('Y-m-d')); ?></td>
            <td class="font-bold"><?php echo e($r->company?->name ?? ($r->supplier_name ?? '-')); ?></td>
            <td><?php echo e($r->invoice_no ?? '-'); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$r->goods_total,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$r->transport_charges,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$r->payment_made,2)); ?></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.purchases.show', $r)); ?>" class="btn btn-secondary btn-xs">Open</a>
                <?php if($r->company_id): ?>
                  <a href="<?php echo e(route('mt.company_ledger.index', $r->company_id)); ?>" class="btn btn-teal btn-xs">Ledger</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="7">No purchases found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/purchases/index.blade.php ENDPATH**/ ?>