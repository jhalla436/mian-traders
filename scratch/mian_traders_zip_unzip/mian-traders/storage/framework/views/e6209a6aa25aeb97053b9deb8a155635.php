<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Add Expense</h2>
    <a href="<?php echo e(route('mt.expenses.index')); ?>" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('mt.expenses.store')); ?>" style="background:#fff;padding:14px;border-radius:10px;max-width:900px;">
    <?php echo csrf_field(); ?>

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Date</div>
        <input type="date" name="expense_date" value="<?php echo e(old('expense_date', now()->toDateString())); ?>" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div style="flex:1;min-width:260px;">
        <div style="font-size:12px;color:#6b7280;">Title</div>
        <input name="title" value="<?php echo e(old('title')); ?>" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Shop Rent / Salary / etc">
        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Category</div>
        <input name="category" value="<?php echo e(old('category')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="rent/salary/transport">
        <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div style="flex:1;min-width:240px;">
        <div style="font-size:12px;color:#6b7280;">Vendor (optional)</div>
        <input name="vendor" value="<?php echo e(old('vendor')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Landlord / Worker / Transporter">
        <?php $__errorArgs = ['vendor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Payment Method</div>
        <input name="payment_method" value="<?php echo e(old('payment_method')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="cash/bank">
        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Amount</div>
        <input type="number" step="0.01" name="amount" value="<?php echo e(old('amount')); ?>" required style="padding:10px;border:1px solid #ddd;border-radius:10px;text-align:right;width:180px;">
        <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div style="margin-bottom:12px;">
      <div style="font-size:12px;color:#6b7280;">Note</div>
      <input name="note" value="<?php echo e(old('note')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Optional">
      <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <button style="padding:12px 14px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;">Save</button>
  </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/expenses/create.blade.php ENDPATH**/ ?>