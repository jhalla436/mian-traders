<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Sales</h2>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.pos.index')); ?>" class="btn btn-primary btn-sm">Go POS</a>
      <a href="<?php echo e(route('mt.udhar.index')); ?>" class="btn btn-secondary btn-sm">Udhar List</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Sale #</th>
          <th>Customer</th>
          <th>Phone</th>
          <th class="text-right">Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="font-bold">#<?php echo e($s->id); ?></td>
            <td><?php echo e($s->customer_name ?? '-'); ?></td>
            <td>
              <?php $wa = \App\Support\WhatsApp::url($s->customer_phone); ?>
              <div class="flex gap-8 items-center flex-wrap">
                <span><?php echo e($s->customer_phone ?? '-'); ?></span>
                <?php if($wa): ?>
                  <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a>
                <?php endif; ?>
              </div>
            </td>
            <td class="text-right"><?php echo e(number_format((float)$s->total_amount,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$s->paid_amount,2)); ?></td>
            <td class="text-right font-bold" style="color:#dc2626;"><?php echo e(number_format((float)$s->balance_amount,2)); ?></td>
            <td><span class="badge badge-gray"><?php echo e($s->status ?? '-'); ?> / <?php echo e($s->sale_type ?? '-'); ?></span></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.sales.show', $s)); ?>" class="btn btn-secondary btn-xs">View</a>
                <?php if($s->customer_phone): ?>
                  <a href="<?php echo e(route('mt.customers.profile', $s->customer_phone)); ?>" class="btn btn-success btn-xs">Profile</a>
                <?php endif; ?>
                <?php if($s->customer_phone && (float)$s->balance_amount > 0): ?>
                  <a href="<?php echo e(route('mt.udhar.show', $s->customer_phone)); ?>" class="btn btn-primary btn-xs">Udhar</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="8">No sales found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($sales->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sales/index.blade.php ENDPATH**/ ?>