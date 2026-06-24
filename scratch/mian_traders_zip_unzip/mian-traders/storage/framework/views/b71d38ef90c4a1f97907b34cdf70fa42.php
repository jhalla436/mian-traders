<?php $__env->startSection('content'); ?>
<?php
  $companies = $companies ?? collect();
  $categories = $categories ?? collect();
  $discountTypes = $discountTypes ?? collect();
?>

<div style="background:#fff;padding:14px;border-radius:10px;max-width:860px;">
  <h2 style="margin:0 0 10px;">Import Price List (CSV)</h2>

  <div style="color:#6b7280;font-size:13px;line-height:1.5;margin-bottom:12px;">
    Upload a <b>CSV</b> file (you can export your Excel sheet as CSV). The importer can create new products and (optionally) update existing ones.
    <div style="margin-top:6px;">
      <a href="<?php echo e(route('mt.products.import_template')); ?>" style="text-decoration:none;color:#2563eb;font-weight:700;">
        Download CSV Template
      </a>
    </div>
  </div>

  <form method="POST" action="<?php echo e(route('mt.products.import_process')); ?>" enctype="multipart/form-data" style="display:grid;gap:12px;">
    <?php echo csrf_field(); ?>

    <div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">CSV File</div>
      <input type="file" name="file" accept=".csv,text/csv" required>
      <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div style="color:#b91c1c;font-size:12px;"><?php echo e($message); ?></div>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Company (optional)</div>
        <select name="default_company_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($co->id); ?>"><?php echo e($co->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Category (optional)</div>
        <select name="default_category_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Discount Type (optional)</div>
        <select name="default_discount_type_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          <?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($dt->id); ?>"><?php echo e($dt->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Mode</div>
        <select name="mode" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="upsert">Upsert (Create + Update)</option>
          <option value="create_only">Create Only (Skip existing)</option>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Active</div>
        <select name="default_is_active" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div></div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <button type="submit" style="padding:12px 14px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
        Import
      </button>
      <a href="<?php echo e(route('mt.products.index')); ?>" style="padding:12px 14px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">
        Back
      </a>
    </div>

    <div style="margin-top:6px;color:#6b7280;font-size:12px;">
      CSV columns supported: <b>name</b>, sku, mrp, selling_price_default, purchase_price_manual, stock_qty, low_stock_alert_qty, pricing_mode (manual/shell), shell_rate, width_in, length_in, height_in, sheet_full_w, sheet_full_l, category, company.
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/import.blade.php ENDPATH**/ ?>