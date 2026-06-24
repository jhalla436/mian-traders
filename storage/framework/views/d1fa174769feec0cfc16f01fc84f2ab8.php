<?php
  $payments = collect($sale->payments ?? []);
  $lineStyle = $lineStyle ?? 'font-size:12px;color:#334155;line-height:1.5;margin-top:4px;';
  $receivedAmount = (float)($sale->received_amount ?? 0);
  if ($receivedAmount <= 0 && (float)($sale->paid_amount ?? 0) > 0) {
      $receivedAmount = (float)$sale->paid_amount;
  }
  $changeReturned = (float)($sale->change_returned ?? 0);
  $primaryMethod = \App\Support\PaymentMethod::normalize($payments->first()->method ?? null);
?>

<?php if($receivedAmount > 0): ?>
  <div style="<?php echo e($lineStyle); ?>">
    <?php if($primaryMethod === \App\Support\PaymentMethod::HARD_CASH): ?>
      Cash Received From Customer: Rs <?php echo e(number_format($receivedAmount, 2)); ?>

    <?php else: ?>
      Amount Transferred: Rs <?php echo e(number_format($receivedAmount, 2)); ?>

    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if($changeReturned > 0): ?>
  <div style="<?php echo e($lineStyle); ?>">Change Returned To Customer: Rs <?php echo e(number_format($changeReturned, 2)); ?></div>
<?php endif; ?>

<?php if($payments->isNotEmpty()): ?>
  <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="<?php echo e($lineStyle); ?>">
      <?php echo e(\App\Support\PaymentMethod::label($pay->method ?? null)); ?>: Rs <?php echo e(number_format((float)($pay->amount ?? 0), 2)); ?>

      <?php if(!empty($pay->note)): ?>
        | <?php echo e($pay->note); ?>

      <?php endif; ?>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php elseif((float)($sale->paid_amount ?? 0) > 0): ?>
  <div style="<?php echo e($lineStyle); ?>">
    Payment Method Not Recorded: Rs <?php echo e(number_format((float)($sale->paid_amount ?? 0), 2)); ?>

  </div>
<?php else: ?>
  <div style="<?php echo e($lineStyle); ?>">No payment received yet (Udhar / Unpaid).</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\sales\_payment_details.blade.php ENDPATH**/ ?>