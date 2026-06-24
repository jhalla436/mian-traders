<?php $__env->startSection('content'); ?>
<?php
  $from = $from ?? '';
  $to = $to ?? '';
  $balance = $balance ?? 0;
?>

  <div class="page-header">
    <div>
      <div class="page-subtitle">Company Ledger</div>
      <h2 class="page-title"><?php echo e($company->name); ?></h2>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.companies.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
      <div class="total-badge">
        Balance: <?php echo e(number_format((float)$balance,2)); ?>

        <span class="text-xs" style="opacity:0.7;">(Debit - Credit)</span>
      </div>
    </div>
  </div>

  <div class="filter-bar" style="justify-content:space-between;">
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
    <a href="<?php echo e(route('mt.purchases.create', ['company_id'=>$company->id])); ?>" class="btn btn-success btn-sm">+ Stock In (Purchase)</a>
  </div>

  <div class="card mb-16">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Add Entry</h3>
    <form method="POST" action="<?php echo e(route('mt.company_ledger.store', $company)); ?>" class="flex gap-12 flex-wrap items-end">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label class="form-label">Date</label>
        <input type="date" name="entry_date" value="<?php echo e(old('entry_date', now()->toDateString())); ?>" required class="mt-input">
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="entry_type" class="mt-select">
          <option value="payment">Payment</option>
          <option value="transport">Transport</option>
          <option value="adjustment">Adjustment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Direction</label>
        <select name="direction" class="mt-select">
          <option value="credit">Credit (reduces payable)</option>
          <option value="debit">Debit (increases payable)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" value="<?php echo e(old('amount')); ?>" required class="mt-input" style="width:140px;text-align:right;">
      </div>
      <div class="form-group" style="flex:1;min-width:200px;">
        <label class="form-label">Description</label>
        <input name="description" value="<?php echo e(old('description')); ?>" class="mt-input" placeholder="Optional">
      </div>
      <button class="btn btn-teal btn-sm">Save</button>
    </form>
    <?php if($errors->any()): ?>
      <div class="form-error" style="margin-top:10px;"><?php echo e(implode(' | ', $errors->all())); ?></div>
    <?php endif; ?>
  </div>

  <div class="table-card">
    <div style="padding:16px 20px 0;display:flex;gap:12px;flex-wrap:wrap;">
      <div class="badge badge-info">Debit: <?php echo e(number_format((float)$debitTotal,2)); ?></div>
      <div class="badge badge-success">Credit: <?php echo e(number_format((float)$creditTotal,2)); ?></div>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Date</th>
          <th>Type</th>
          <th>Description</th>
          <th class="text-right">Debit</th>
          <th class="text-right">Credit</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($r->entry_date?->format('Y-m-d')); ?></td>
            <td class="font-bold"><?php echo e($r->entry_type); ?></td>
            <td><?php echo e($r->description ?? '-'); ?></td>
            <td class="text-right"><?php echo e($r->direction==='debit' ? number_format((float)$r->amount,2) : '-'); ?></td>
            <td class="text-right"><?php echo e($r->direction==='credit' ? number_format((float)$r->amount,2) : '-'); ?></td>
            <td>
              <form method="POST" action="<?php echo e(route('mt.company_ledger.destroy', [$company, $r])); ?>" onsubmit="return confirm('Delete this ledger entry?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-danger btn-xs">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No ledger entries.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\companies\ledger\index.blade.php ENDPATH**/ ?>