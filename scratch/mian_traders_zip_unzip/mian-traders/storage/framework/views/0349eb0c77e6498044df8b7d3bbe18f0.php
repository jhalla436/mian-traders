<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Company Orders</h2>
    <a href="<?php echo e(route('mt.company_orders.create')); ?>" style="padding:10px 12px;border-radius:10px;background:#16a34a;color:#fff;text-decoration:none;font-weight:800;">+ New Order</a>
  </div>

  <form method="GET" style="background:#fff;padding:12px;border-radius:10px;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
    <div>
      <div style="font-size:12px;color:#6b7280;">Company</div>
      <select name="company_id" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:240px;">
        <option value="">-- All --</option>
        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if((string)$companyId === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div>
      <div style="font-size:12px;color:#6b7280;">Status</div>
      <select name="status" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:180px;">
        <option value="">-- All --</option>
        <option value="open" <?php if($status==='open'): echo 'selected'; endif; ?>>Open</option>
        <option value="received" <?php if($status==='received'): echo 'selected'; endif; ?>>Received</option>
        <option value="cancelled" <?php if($status==='cancelled'): echo 'selected'; endif; ?>>Cancelled</option>
      </select>
    </div>

    <button style="padding:10px 12px;border-radius:10px;background:#2563eb;color:#fff;border:0;font-weight:800;">Filter</button>
  </form>

  <div style="background:#fff;padding:0;border-radius:10px;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Order</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Company</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Date</th>
          <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Status</th>
          <th style="text-align:right;padding:10px;border-bottom:1px solid #eee;">Goods Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
              <a href="<?php echo e(route('mt.company_orders.show', $r)); ?>" style="text-decoration:none;font-weight:800;">#<?php echo e($r->id); ?></a>
            </td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;"><?php echo e($r->company?->name); ?></td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;"><?php echo e(optional($r->order_date)->format('Y-m-d')); ?></td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;"><?php echo e(strtoupper($r->status)); ?></td>
            <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:800;"><?php echo e(number_format((float)$r->goods_total, 2)); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="5" style="padding:12px;">No orders found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;"><?php echo e($rows->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/company_orders/index.blade.php ENDPATH**/ ?>