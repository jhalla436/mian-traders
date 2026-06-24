<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Expense</h2>
    <a href="<?php echo e(route('mt.expenses.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card" style="max-width:900px;">
    <form method="POST" action="<?php echo e(route('mt.expenses.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <div class="flex gap-12 flex-wrap items-end">
        <div class="form-group">
          <label class="form-label">Date</label>
          <input type="date" name="expense_date" value="<?php echo e(old('expense_date', now()->toDateString())); ?>" required class="mt-input">
          <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group" style="flex:1;min-width:240px;">
          <label class="form-label">Title</label>
          <input name="title" value="<?php echo e(old('title')); ?>" required class="mt-input" placeholder="Shop Rent / Salary / etc">
          <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="flex gap-12 flex-wrap items-end">
        <div class="form-group">
          <label class="form-label">Category</label>
          <input name="category" value="<?php echo e(old('category')); ?>" class="mt-input" placeholder="rent/salary/transport">
          <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group" style="flex:1;min-width:220px;">
          <label class="form-label">Vendor (optional)</label>
          <input name="vendor" value="<?php echo e(old('vendor')); ?>" class="mt-input" placeholder="Landlord / Worker">
          <?php $__errorArgs = ['vendor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group">
          <label class="form-label">Payment Method</label>
          <input name="payment_method" value="<?php echo e(old('payment_method')); ?>" class="mt-input" placeholder="cash/bank">
          <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" name="amount" value="<?php echo e(old('amount')); ?>" required class="mt-input" style="width:160px;text-align:right;">
          <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="<?php echo e(old('note')); ?>" class="mt-input" placeholder="Optional">
        <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <button class="btn btn-success" style="justify-self:start;">Save Expense</button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\expenses\create.blade.php ENDPATH**/ ?>