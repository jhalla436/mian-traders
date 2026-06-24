

<?php $__env->startSection('content'); ?>
<div style="max-width:900px;margin:0 auto;padding:20px;">
  <h1 style="font-size:24px;font-weight:700;margin-bottom:20px;">Add Prices - <?php echo e($name); ?></h1>
  
  <p style="color:#666;margin-bottom:20px;font-size:14px;">
    Set MRP, purchase price, and selling price for each size/quality. Leave blank to skip.
  </p>

  <form method="POST" action="<?php echo e(route('mt.products.bulk_price_save')); ?>" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:20px;">
    <?php echo csrf_field(); ?>

    <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
      <thead>
        <tr style="background:#f5f5f5;border-bottom:2px solid #ddd;">
          <th style="text-align:left;padding:12px;font-weight:600;border-right:1px solid #ddd;">Product Size</th>
          <th style="text-align:center;padding:12px;font-weight:600;border-right:1px solid #ddd;">MRP</th>
          <th style="text-align:center;padding:12px;font-weight:600;border-right:1px solid #ddd;">Purchase Price</th>
          <th style="text-align:center;padding:12px;font-weight:600;">Selling Price</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr style="border-bottom:1px solid #eee;">
            <td style="padding:12px;border-right:1px solid #ddd;">
              <?php echo e($product->name); ?>

              <div style="font-size:12px;color:#999;margin-top:4px;">
                <?php echo e($product->length_in ?? '-'); ?> × <?php echo e($product->width_in ?? '-'); ?> × <?php echo e($product->height_in ?? '-'); ?>

              </div>
            </td>
            <td style="padding:12px;border-right:1px solid #ddd;">
              <input 
                type="number"
                name="prices[<?php echo e($idx); ?>][id]"
                value="<?php echo e($product->id); ?>"
                style="display:none;">
              <input 
                type="number"
                name="prices[<?php echo e($idx); ?>][mrp]"
                value="<?php echo e($product->mrp ?? ''); ?>"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
            <td style="padding:12px;border-right:1px solid #ddd;">
              <input 
                type="number"
                name="prices[<?php echo e($idx); ?>][purchase_price_manual]"
                value="<?php echo e($product->purchase_price_manual ?? ''); ?>"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
            <td style="padding:12px;">
              <input 
                type="number"
                name="prices[<?php echo e($idx); ?>][selling_price_default]"
                value="<?php echo e($product->selling_price_default ?? ''); ?>"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="4" style="text-align:center;padding:20px;color:#999;">No products found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="display:flex;gap:10px;justify-content:center;">
      <button type="submit" style="padding:10px 24px;background:#080;color:#fff;border:1px solid #060;border-radius:4px;font-weight:600;cursor:pointer;font-size:14px;">
        Save Prices
      </button>
      <a href="<?php echo e(route('mt.products.index')); ?>" style="padding:10px 24px;background:#999;color:#fff;border:1px solid #777;border-radius:4px;font-weight:600;cursor:pointer;font-size:14px;text-decoration:none;display:inline-block;">
        Skip
      </a>
    </div>
  </form>
</div>

<style>
  input[type="number"]:focus {
    outline: none;
    border-color: #333;
    box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\products\bulk_price.blade.php ENDPATH**/ ?>