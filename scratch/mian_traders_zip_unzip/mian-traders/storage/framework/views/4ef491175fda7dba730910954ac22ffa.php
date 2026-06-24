<?php $__env->startSection('content'); ?>
<?php
  $companyId = $companyId ?? '';
  $from = $from ?? '';
  $to = $to ?? '';
?>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Purchases (Stock In)</h2>
    <a href="<?php echo e(route('mt.purchases.create')); ?>" style="padding:10px 12px;border:0;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;">+ New Purchase</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Company</div>
        <select name="company_id" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:220px;">
          <option value="">All</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if((string)$companyId === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">From</div>
        <input type="date" name="from" value="<?php echo e($from); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">To</div>
        <input type="date" name="to" value="<?php echo e($to); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>

      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Filter</button>
    </form>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Date</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Company/Supplier</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Invoice</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Goods</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Transport</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Paid</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->purchase_date?->format('Y-m-d')); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:700;"><?php echo e($r->company?->name ?? ($r->supplier_name ?? '-')); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($r->invoice_no ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e(number_format((float)$r->goods_total,2)); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e(number_format((float)$r->transport_charges,2)); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e(number_format((float)$r->payment_made,2)); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <a href="<?php echo e(route('mt.purchases.show', $r)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Open</a>
              <?php if($r->company_id): ?>
                <a href="<?php echo e(route('mt.company_ledger.index', $r->company_id)); ?>" style="padding:6px 10px;border-radius:8px;background:#0f766e;color:#fff;text-decoration:none;">Ledger</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" style="padding:12px;">No purchases found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($rows->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/purchases/index.blade.php ENDPATH**/ ?>