<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Name</div>
    <input name="name" value="<?php echo e(old('name', $product->name)); ?>" required
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">SKU (optional)</div>
    <input name="sku" value="<?php echo e(old('sku', $product->sku)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Company</div>
    <select name="company_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($co->id); ?>" <?php if((int)old('company_id', $product->company_id) === (int)$co->id): echo 'selected'; endif; ?>><?php echo e($co->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Category</div>
    <select name="category_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($cat->id); ?>" <?php if((int)old('category_id', $product->category_id) === (int)$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Discount Type</div>
    <select name="discount_type_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="">-- None --</option>
      <?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($dt->id); ?>" <?php if((int)old('discount_type_id', $product->discount_type_id) === (int)$dt->id): echo 'selected'; endif; ?>><?php echo e($dt->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['discount_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Active</div>
    <label style="display:flex;gap:8px;align-items:center;">
      <input type="checkbox" name="is_active" value="1" <?php if((int)old('is_active', $product->is_active ?? 1) === 1): echo 'checked'; endif; ?>>
      <span>Yes</span>
    </label>
  </div>

  
  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Pricing Mode</div>
    <select name="pricing_mode" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <option value="manual" <?php if(old('pricing_mode', $product->pricing_mode ?? 'manual') === 'manual'): echo 'selected'; endif; ?>>Manual</option>
      <option value="shell" <?php if(old('pricing_mode', $product->pricing_mode) === 'shell'): echo 'selected'; endif; ?>>Shell (w*l*h/144 * shell_rate)</option>
    </select>
    <?php $__errorArgs = ['pricing_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Shell Rate (only for shell mode)</div>
    <input name="shell_rate" type="number" step="0.01" min="0"
           value="<?php echo e(old('shell_rate', $product->shell_rate)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['shell_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Width (inches)</div>
    <input name="width_in" type="number" step="0.01" min="0"
           value="<?php echo e(old('width_in', $product->width_in)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['width_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Length (inches)</div>
    <input name="length_in" type="number" step="0.01" min="0"
           value="<?php echo e(old('length_in', $product->length_in)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['length_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Height (inches)</div>
    <input name="height_in" type="number" step="0.01" min="0"
           value="<?php echo e(old('height_in', $product->height_in)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['height_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">MRP</div>
    <input name="mrp" type="number" step="0.01" min="0"
           value="<?php echo e(old('mrp', $product->mrp)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['mrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Selling Price</div>
    <input name="selling_price_default" type="number" step="0.01" min="0"
           value="<?php echo e(old('selling_price_default', $product->selling_price_default)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['selling_price_default'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <?php if($canSeeCost): ?>
    <div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Manual Purchase Price (Optional)</div>
      <input name="purchase_price_manual" type="number" step="0.01" min="0"
             value="<?php echo e(old('purchase_price_manual', $product->purchase_price_manual)); ?>"
             style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      <?php $__errorArgs = ['purchase_price_manual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
  <?php endif; ?>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Stock Qty</div>
    <input name="stock_qty" type="number" step="0.01"
           value="<?php echo e(old('stock_qty', $product->stock_qty)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['stock_qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Low Stock Alert Qty</div>
    <input name="low_stock_alert_qty" type="number" step="0.01" min="0"
           value="<?php echo e(old('low_stock_alert_qty', $product->low_stock_alert_qty)); ?>"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <?php $__errorArgs = ['low_stock_alert_qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#b91c1c;font-size:12px;margin-top:4px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

</div>

<?php if($errors->any()): ?>
  <div style="margin-top:12px;background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
    <b>Fix errors:</b>
    <ul style="margin:8px 0 0 18px;">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($e); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/products/form.blade.php ENDPATH**/ ?>