<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Company Orders</h2>
    <a href="<?php echo e(route('mt.company_orders.create')); ?>" class="btn btn-success btn-sm">+ New Order</a>
  </div>

  <div class="filter-bar">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">Company</span>
        <select name="company_id" class="mt-select" style="min-width:240px;">
          <option value="">-- All --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if((string)$companyId === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Status</span>
        <select name="status" class="mt-select" style="min-width:180px;">
          <option value="">-- All --</option>
          <option value="open" <?php if($status==='open'): echo 'selected'; endif; ?>>Open</option>
          <option value="received" <?php if($status==='received'): echo 'selected'; endif; ?>>Received</option>
          <option value="cancelled" <?php if($status==='cancelled'): echo 'selected'; endif; ?>>Cancelled</option>
        </select>
      </div>
      <button class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Company</th>
          <th>Date</th>
          <th>Status</th>
          <th>Payment Status</th>
          <th class="text-right">Goods Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $payClass = 'badge-danger';
            if ($r->payment_status === 'paid') $payClass = 'badge-success';
            elseif ($r->payment_status === 'partial') $payClass = 'badge-info';
          ?>
          <tr>
            <td><a href="<?php echo e(route('mt.company_orders.show', $r)); ?>" class="font-bold" style="color:#6366f1;">#<?php echo e($r->id); ?></a></td>
            <td><?php echo e($r->company?->name); ?></td>
            <td><?php echo e(optional($r->order_date)->format('Y-m-d')); ?></td>
            <td><span class="badge badge-gray"><?php echo e(strtoupper($r->status)); ?></span></td>
            <td><span class="badge <?php echo e($payClass); ?>"><?php echo e(strtoupper($r->payment_status ?: 'unpaid')); ?></span></td>
            <td class="text-right font-bold"><?php echo e(number_format((float)$r->goods_total, 2)); ?></td>
            <td class="text-right text-success font-semibold"><?php echo e(number_format((float)$r->paid_amount, 2)); ?></td>
            <td class="text-right text-danger font-semibold"><?php echo e(number_format((float)$r->balance, 2)); ?></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.company_orders.show', $r)); ?>" class="btn btn-primary btn-xs">View</a>
                <?php if($r->status !== 'received'): ?>
                  <form method="POST" action="<?php echo e(route('mt.company_orders.destroy', $r)); ?>" onsubmit="return confirm('Delete order #<?php echo e($r->id); ?>?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-danger btn-xs">Delete</button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="9">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\company_orders\index.blade.php ENDPATH**/ ?>