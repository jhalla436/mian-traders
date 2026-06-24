

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Stock Movements</h2>
      <div style="color:#6b7280;font-size:12px;margin-top:4px;">Manual stock in/out/adjust history.</div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="<?php echo e(route('mt.stock_movements.create')); ?>"
         style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">Add Movement</a>

      <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search product/note..."
               style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;cursor:pointer;">Search</button>
      </form>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">#</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Type</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Note</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->id); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->product?->name ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <?php
                $t = $r->type;
                $label = strtoupper($t);
                $bg = $t === 'in' ? '#dcfce7' : ($t === 'out' ? '#fee2e2' : '#e0f2fe');
              ?>
              <span style="padding:4px 8px;border-radius:999px;background:<?php echo e($bg); ?>;">
                <?php echo e($label); ?>

              </span>
            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;">
              <?php echo e(number_format((float)$r->qty,2)); ?>

            </td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->note ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->created_at); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="6" style="padding:12px;">No movements yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($rows->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/stock_movements/index.blade.php ENDPATH**/ ?>