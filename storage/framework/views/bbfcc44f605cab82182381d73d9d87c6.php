
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <div>
      <h2 class="page-title">Stock Movements</h2>
      <div class="page-subtitle">Manual stock in/out/adjust history.</div>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.stock_movements.create')); ?>" class="btn btn-success btn-sm">+ Add Movement</a>
      <form method="GET" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search product/note..." class="mt-input" style="min-width:200px;">
        <button class="btn btn-primary btn-sm">Search</button>
      </form>
    </div>
  </div>
  <div class="table-card">
    <table class="mt-table">
      <thead><tr><th>#</th><th>Product</th><th>Type</th><th class="text-right">Qty</th><th>Note</th><th>Date</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($r->id); ?></td>
            <td><?php echo e($r->product?->name ?? '-'); ?></td>
            <td>
              <?php $t = $r->type; ?>
              <span class="badge <?php echo e($t === 'in' ? 'badge-success' : ($t === 'out' ? 'badge-danger' : 'badge-info')); ?>"><?php echo e(strtoupper($t)); ?></span>
            </td>
            <td class="text-right font-bold"><?php echo e(number_format((float)$r->qty,2)); ?></td>
            <td><?php echo e($r->note ?? '-'); ?></td>
            <td><?php echo e($r->created_at); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No movements yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\stock_movements\index.blade.php ENDPATH**/ ?>