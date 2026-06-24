<?php $__env->startSection('content'); ?>
<?php
  $q = $q ?? '';
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Products</h2>

    <form method="GET" style="display:flex;gap:8px;align-items:center;">
      <input name="q" value="<?php echo e($q); ?>" placeholder="Search name/sku/id..."
             style="padding:10px;border:1px solid #ddd;border-radius:10px;">
      <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>
    </form>
  </div>

  <?php if(auth()->user()?->role !== 'cashier'): ?>
    <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
      <a href="<?php echo e(route('mt.products.create')); ?>" style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">+ Add Product</a>

      <a href="<?php echo e(route('mt.products.import_form')); ?>" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Import Price List</a>
    </div>
  <?php endif; ?>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Category</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Company</th>
          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">MRP</th>

          
          <?php if($canSeeCost): ?>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Purchase</th>
          <?php endif; ?>

          <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Stock</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $purchase = \App\Services\PricingService::purchasePrice($p);
          ?>
          <tr>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;"><?php echo e($p->name); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($p->category?->name ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;"><?php echo e($p->company?->name ?? '-'); ?></td>
            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e(number_format((float)$p->mrp,2)); ?></td>

            <?php if($canSeeCost): ?>
              <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;font-weight:700;">
                <?php echo e(number_format((float)$purchase,2)); ?>

              </td>
            <?php endif; ?>

            <td style="border-bottom:1px solid #f2f2f2;padding:8px;text-align:right;"><?php echo e(number_format((float)$p->stock_qty,2)); ?></td>

            <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
              <?php if(auth()->user()?->role !== 'cashier'): ?>
                <a href="<?php echo e(route('mt.products.edit', $p)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>
              <?php else: ?>
                <span style="color:#6b7280;font-size:12px;">View only</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" style="padding:12px;">No products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($products->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/index.blade.php ENDPATH**/ ?>