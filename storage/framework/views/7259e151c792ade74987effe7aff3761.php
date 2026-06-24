<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <div>
      <div class="page-subtitle">Purchase #<?php echo e($purchase->id); ?></div>
      <h2 class="page-title"><?php echo e($purchase->company?->name ?? ($purchase->supplier_name ?? 'Supplier')); ?></h2>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.purchases.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
      <?php if($purchase->company_id): ?>
        <a href="<?php echo e(route('mt.company_ledger.index', $purchase->company_id)); ?>" class="btn btn-teal btn-sm">Open Ledger</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="filter-bar" style="gap:24px;">
    <div>
      <div class="filter-label">Date</div>
      <div class="font-bold"><?php echo e($purchase->purchase_date?->format('Y-m-d')); ?></div>
    </div>
    <div>
      <div class="filter-label">Invoice #</div>
      <div class="font-bold"><?php echo e($purchase->invoice_no ?? '-'); ?></div>
    </div>
    <div>
      <div class="filter-label">Goods Total</div>
      <div class="font-bold"><?php echo e(number_format((float)$purchase->goods_total,2)); ?></div>
    </div>
    <div>
      <div class="filter-label">Transport</div>
      <div class="font-bold"><?php echo e(number_format((float)$purchase->transport_charges,2)); ?></div>
    </div>
    <div>
      <div class="filter-label">Paid</div>
      <div class="font-bold"><?php echo e(number_format((float)$purchase->payment_made,2)); ?></div>
    </div>
  </div>

  <?php if($purchase->note): ?>
    <div class="card mb-16">
      <div class="filter-label">Note</div>
      <div class="font-bold"><?php echo e($purchase->note); ?></div>
    </div>
  <?php endif; ?>

  <div class="table-card">
    <div style="padding:16px 20px 0;">
      <h3 style="margin:0;font-size:16px;font-weight:800;">Items</h3>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Product</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Unit Cost</th>
          <th class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="font-bold"><?php echo e($it->product?->name ?? ('#'.$it->product_id)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$it->qty,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$it->unit_cost,2)); ?></td>
            <td class="text-right font-bold"><?php echo e(number_format((float)$it->line_total,2)); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\purchases\show.blade.php ENDPATH**/ ?>