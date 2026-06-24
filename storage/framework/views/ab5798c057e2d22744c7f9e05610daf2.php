<?php $__env->startSection('content'); ?>
<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>

<div class="page-header">
  <div>
    <?php $wa = \App\Support\WhatsApp::url($phone); ?>
    <h2 class="page-title">Customer Ledger</h2>
    <div class="page-subtitle flex gap-8 items-center flex-wrap">
      <span>Phone: <b><?php echo e($phone); ?></b></span>
      <?php if($wa): ?>
        <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a>
      <?php endif; ?>
      <span>All udhar bills merged by phone. Payments here are added in TOTAL (not per bill).</span>
    </div>
  </div>

  <div class="page-actions">
    <a href="<?php echo e(route('mt.udhar.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
    <a href="<?php echo e(route('mt.sales.index')); ?>" class="btn btn-outline btn-sm">Sales</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:16px;align-items:start;">

  
  <div class="table-card">
    <div style="padding:16px 20px 0;">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Bills (Unpaid/Partial)</h3>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Bill #</th>
          <th class="text-right">Total</th>
          <th class="text-right">Paid</th>
          <th class="text-right">Balance</th>
          <?php if($canSeeCost): ?>
            <th class="text-right">Profit</th>
          <?php endif; ?>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="font-bold">#<?php echo e($s->id); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$s->total_amount,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$s->paid_amount,2)); ?></td>
            <td class="text-right text-danger font-bold"><?php echo e(number_format((float)$s->balance_amount,2)); ?></td>
            <?php if($canSeeCost): ?>
              <td class="text-right"><?php echo e(number_format((float)($s->profit_total ?? 0),2)); ?></td>
            <?php endif; ?>
            <td>
              <a href="<?php echo e(route('mt.sales.show', $s)); ?>" class="btn btn-primary btn-xs">Open</a>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="<?php echo e($canSeeCost ? 6 : 5); ?>">No udhar bills for this phone.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="card">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Totals</h3>

    <div class="flex justify-between" style="margin-bottom:8px;">
      <span>Total Sales (All Time)</span>
      <b><?php echo e(number_format((float)($totalSalesAll ?? 0),2)); ?></b>
    </div>

    <?php if($canSeeCost): ?>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Total Profit (All Time)</span>
        <b class="text-success"><?php echo e(number_format((float)($totalProfitAll ?? 0),2)); ?></b>
      </div>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Realized Profit</span>
        <b class="text-success"><?php echo e(number_format((float)($totalProfitRealizedAll ?? 0),2)); ?></b>
      </div>
    <?php endif; ?>

    <div class="flex justify-between" style="margin-bottom:8px;">
      <span>Current Udhar (Total Balance)</span>
      <b class="text-danger"><?php echo e(number_format((float)($totalUdhar ?? 0),2)); ?></b>
    </div>

    <hr class="separator">

    <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Add Payment (TOTAL)</h3>

    <?php if((float)($totalUdhar ?? 0) <= 0): ?>
      <div class="badge badge-success" style="padding:10px 14px;font-size:13px;">This customer has no udhar remaining.</div>
    <?php else: ?>
      <form method="POST" action="<?php echo e(route('mt.udhar.pay_total', $phone)); ?>" class="form-stack">
        <?php echo csrf_field(); ?>
        <input name="amount" type="number" step="0.01" min="0" placeholder="Payment amount" class="mt-input">
        <select name="method" class="mt-select">
          <?php $__currentLoopData = \App\Support\PaymentMethod::options(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($methodKey); ?>" <?php if($methodKey === \App\Support\PaymentMethod::HARD_CASH): echo 'selected'; endif; ?>><?php echo e($methodLabel); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input name="note" placeholder="Note (optional)" class="mt-input">
        <button class="btn btn-success">Add Payment</button>
      </form>
      <div class="text-xs text-muted" style="margin-top:8px;">
        Payment will reduce the customer's TOTAL udhar across all bills.
      </div>
    <?php endif; ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\udhar\show.blade.php ENDPATH**/ ?>