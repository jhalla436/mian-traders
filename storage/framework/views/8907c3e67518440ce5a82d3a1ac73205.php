<?php $__env->startSection('content'); ?>
<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>
  <div class="page-header">
    <div>
      <h2 class="page-title">Customer Profile</h2>
      <div class="page-subtitle">
        <b>Name:</b> <?php echo e($customerName); ?> |
        <b>Phone:</b> <?php echo e($phone); ?>

        <?php if($lastPurchaseAt): ?> | <b>Last Purchase:</b> <?php echo e($lastPurchaseAt); ?> <?php endif; ?>
      </div>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.udhar.show', $phone)); ?>" class="btn btn-primary btn-sm">Ledger (Udhar)</a>
      <?php if(!empty($waNumber)): ?>
        <a href="https://wa.me/<?php echo e($waNumber); ?>" target="_blank" class="btn btn-success btn-sm">WhatsApp</a>
      <?php endif; ?>
      <a href="<?php echo e(route('mt.whatsapp.index', ['type'=>'all','q'=>$phone])); ?>" class="btn btn-secondary btn-sm">WhatsApp List</a>
      <a href="<?php echo e(route('mt.udhar.index')); ?>" class="btn btn-outline btn-sm">← Back</a>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

    <div class="card">
      <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Lifetime Totals</h3>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Total Sales (from start)</span>
        <b><?php echo e(number_format((float)$totalSales, 2)); ?></b>
      </div>
      <?php if($canSeeCost): ?>
        <div class="flex justify-between" style="margin-bottom:8px;">
          <span>Total Profit (from start)</span>
          <b class="text-success"><?php echo e(number_format((float)$totalProfit, 2)); ?></b>
        </div>
        <div class="flex justify-between" style="margin-bottom:8px;">
          <span>Realized Profit</span>
          <b class="text-success"><?php echo e(number_format((float)$profitRealized, 2)); ?></b>
        </div>
      <?php endif; ?>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Current Udhar</span>
        <b class="text-danger"><?php echo e(number_format((float)$totalUdhar, 2)); ?></b>
      </div>

      <hr class="separator">
      <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Outstanding Bills</h3>

      <?php if($outstandingBills->count() === 0): ?>
        <div class="text-muted text-sm">No outstanding bills.</div>
      <?php else: ?>
        <table class="mt-table">
          <thead>
            <tr><th>Bill</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Balance</th><th>Open</th></tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $outstandingBills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td class="font-bold">#<?php echo e($b->id); ?></td>
                <td><?php echo e($b->created_at); ?></td>
                <td class="text-right"><?php echo e(number_format((float)$b->total_amount,2)); ?></td>
                <td class="text-right text-danger font-bold"><?php echo e(number_format((float)$b->balance_amount,2)); ?></td>
                <td><a href="<?php echo e(route('mt.sales.show', $b->id)); ?>" class="btn btn-primary btn-xs">Open</a></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Top Products</h3>
      <?php if($topProducts->count() === 0): ?>
        <div class="text-muted text-sm">No product history found.</div>
      <?php else: ?>
        <table class="mt-table">
          <thead><tr><th>Product</th><th class="text-right">Qty</th><th class="text-right">Amount</th></tr></thead>
          <tbody>
            <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($p->product_name); ?></td>
                <td class="text-right"><?php echo e(number_format((float)$p->total_qty, 2)); ?></td>
                <td class="text-right font-bold"><?php echo e(number_format((float)$p->total_amount, 2)); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      <?php endif; ?>

      <?php if($canSeeCost): ?>
        <hr class="separator">
        <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Profit By Month</h3>
        <?php if($profitByMonth->count() === 0): ?>
          <div class="text-muted text-sm">No monthly data.</div>
        <?php else: ?>
          <table class="mt-table">
            <thead><tr><th>Month</th><th class="text-right">Sales</th><th class="text-right">Profit</th><th class="text-right">Realized</th></tr></thead>
            <tbody>
              <?php $__currentLoopData = $profitByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($m->ym); ?></td>
                  <td class="text-right"><?php echo e(number_format((float)$m->total_sales,2)); ?></td>
                  <td class="text-right"><?php echo e(number_format((float)$m->total_profit,2)); ?></td>
                  <td class="text-right"><?php echo e(number_format((float)$m->realized_profit,2)); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="table-card" style="margin-top:16px;">
    <div style="padding:16px 20px 0;">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Recent Bills (Last 15)</h3>
    </div>
    <?php if($recentBills->count() === 0): ?>
      <div class="text-muted text-sm" style="padding:16px 20px;">No bills found.</div>
    <?php else: ?>
      <table class="mt-table" style="margin-top:12px;">
        <thead>
          <tr><th>Bill</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th class="text-right">Balance</th><th>Status</th><th>Open</th></tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $recentBills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="font-bold">#<?php echo e($b->id); ?></td>
              <td><?php echo e($b->created_at); ?></td>
              <td class="text-right"><?php echo e(number_format((float)$b->total_amount,2)); ?></td>
              <td class="text-right"><?php echo e(number_format((float)$b->paid_amount,2)); ?></td>
              <td class="text-right text-danger font-bold"><?php echo e(number_format((float)$b->balance_amount,2)); ?></td>
              <td><span class="badge badge-gray"><?php echo e($b->status); ?></span></td>
              <td><a href="<?php echo e(route('mt.sales.show', $b->id)); ?>" class="btn btn-primary btn-xs">Open</a></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\customers\profile.blade.php ENDPATH**/ ?>