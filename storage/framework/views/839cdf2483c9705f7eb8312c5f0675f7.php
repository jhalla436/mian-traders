<?php
  $groupKey = old('group_key', $company->group_key ?? '');
  $isActive = (int)old('is_active', isset($company->is_active) ? (int)$company->is_active : 1) === 1;
?>

<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Company Name</label>
    <input name="name" value="<?php echo e(old('name', $company->name ?? '')); ?>" required class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Company Type (Group)</label>
    <select name="group_key" class="mt-select">
      <option value="">-- Select --</option>
      <option value="foam" <?php if($groupKey==='foam'): echo 'selected'; endif; ?>>Foam</option>
      <option value="uncovered_foam" <?php if($groupKey==='uncovered_foam'): echo 'selected'; endif; ?>>Uncovered Foam</option>
      <option value="hardware" <?php if($groupKey==='hardware'): echo 'selected'; endif; ?>>Hardware</option>
      <option value="fabric" <?php if($groupKey==='fabric'): echo 'selected'; endif; ?>>Fabric</option>
      <option value="spring" <?php if($groupKey==='spring'): echo 'selected'; endif; ?>>Spring</option>
      <option value="accessories" <?php if($groupKey==='accessories'): echo 'selected'; endif; ?>>Accessories</option>
      <option value="other" <?php if($groupKey==='other'): echo 'selected'; endif; ?>>Other</option>
    </select>
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Main Phone</label>
    <input name="phone_main" value="<?php echo e(old('phone_main', $company->phone_main ?? '')); ?>" class="mt-input" placeholder="e.g. 0300xxxxxxx">
  </div>
  <div class="form-group">
    <label class="form-label">Email</label>
    <input name="email" value="<?php echo e(old('email', $company->email ?? '')); ?>" class="mt-input" placeholder="optional">
  </div>
</div>

<div class="form-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
  <div class="form-group">
    <label class="form-label">Max POS Discount %</label>
    <input name="max_discount_percent" type="number" step="0.01" min="0" max="100" value="<?php echo e(old('max_discount_percent', $company->max_discount_percent ?? 0)); ?>" class="mt-input">
    <?php $__errorArgs = ['max_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
  <div class="form-group">
    <label class="form-label">Default Original Discount %</label>
    <input name="default_original_discount" type="number" step="0.01" min="0" max="100" value="<?php echo e(old('default_original_discount', $company->default_original_discount ?? 0)); ?>" class="mt-input">
    <?php $__errorArgs = ['default_original_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
  <div class="form-group">
    <label class="form-label">Default Extra Discount %</label>
    <input name="default_extra_discount" type="number" step="0.01" min="0" max="100" value="<?php echo e(old('default_extra_discount', $company->default_extra_discount ?? 0)); ?>" class="mt-input">
    <?php $__errorArgs = ['default_extra_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
  <div class="form-group">
    <label class="form-label">Default Lamination Rate (MRP)</label>
    <input name="default_lamination_rate" type="number" step="0.01" min="0" value="<?php echo e(old('default_lamination_rate', $company->default_lamination_rate ?? '')); ?>" class="mt-input" placeholder="e.g. 4000">
    <?php $__errorArgs = ['default_lamination_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
</div>
<div class="text-xs text-muted" style="margin-top: 4px; margin-bottom: 16px; line-height:1.4;">
  • <b>Max POS Discount %</b> is the maximum retail discount limit for sales cashiers.<br>
  • <b>Default Original & Extra Discount %</b> will automatically prefill when creating new purchase orders for this company.<br>
  • <b>Default Lamination Rate (MRP)</b> will automatically apply as the base price (mrp) when importing or adding lamination sheets for this company without a price.
</div>

<div class="form-group">
  <label class="form-label">Address</label>
  <input name="address" value="<?php echo e(old('address', $company->address ?? '')); ?>" class="mt-input" placeholder="Company address (optional)">
</div>

<div class="form-group">
  <label class="form-label">Note</label>
  <textarea name="note" rows="3" class="mt-input" style="resize:vertical;" placeholder="Any notes about the company (optional)"><?php echo e(old('note', $company->note ?? '')); ?></textarea>
</div>

<label class="form-checkbox">
  <input type="checkbox" name="is_active" value="1" <?php if($isActive): echo 'checked'; endif; ?>>
  Active
</label>

<hr class="separator">

<div>
  <div class="font-bold" style="font-size:15px;margin-bottom:4px;">Company Discount Rules (Purchase Cost)</div>
  <div class="text-xs text-muted" style="margin-bottom:14px;">
    These rules control how buying cost is calculated from MRP when you create <b>Company Orders</b> or <b>Purchases</b>.
    Example: Master Covered 12.5%, Style Hard = 25% + 5% (two-step).
  </div>

  <div class="table-card" style="overflow:auto;">
    <table class="mt-table" style="min-width:760px;">
      <thead>
        <tr>
          <th>Discount Type</th>
          <th>Rule</th>
          <th class="text-right">Percent 1</th>
          <th class="text-right">Percent 2</th>
          <th class="text-right">Fixed Purchase</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $r = $companyDiscountRules[$dt->id] ?? null;
            $baseKey = "discount_rules.{$dt->id}.";
            $ruleType = old($baseKey.'rule_type', $r->rule_type ?? 'none');
            $p1 = old($baseKey.'percent_1', $r->percent_1 ?? '');
            $p2 = old($baseKey.'percent_2', $r->percent_2 ?? '');
            $fx = old($baseKey.'fixed_purchase_price', $r->fixed_purchase_price ?? '');
          ?>
          <tr>
            <td>
              <div class="font-bold"><?php echo e($dt->name); ?></div>
              <?php if($dt->description): ?>
                <div class="text-xs text-muted"><?php echo e($dt->description); ?></div>
              <?php endif; ?>
            </td>
            <td>
              <select name="discount_rules[<?php echo e($dt->id); ?>][rule_type]" class="mt-select" style="min-width:200px;">
                <option value="none" <?php if($ruleType==='none'): echo 'selected'; endif; ?>>No rule</option>
                <option value="percent_once" <?php if($ruleType==='percent_once'): echo 'selected'; endif; ?>>Percent (once)</option>
                <option value="percent_twostep" <?php if($ruleType==='percent_twostep'): echo 'selected'; endif; ?>>Percent (two-step)</option>
                <option value="fixed_purchase" <?php if($ruleType==='fixed_purchase'): echo 'selected'; endif; ?>>Fixed purchase price</option>
              </select>
            </td>
            <td class="text-right">
              <input name="discount_rules[<?php echo e($dt->id); ?>][percent_1]" value="<?php echo e($p1); ?>" type="number" step="0.01" placeholder="e.g. 12.5" class="mt-input" style="width:120px;text-align:right;">
            </td>
            <td class="text-right">
              <input name="discount_rules[<?php echo e($dt->id); ?>][percent_2]" value="<?php echo e($p2); ?>" type="number" step="0.01" placeholder="e.g. 5" class="mt-input" style="width:120px;text-align:right;">
            </td>
            <td class="text-right">
              <input name="discount_rules[<?php echo e($dt->id); ?>][fixed_purchase_price]" value="<?php echo e($fx); ?>" type="number" step="0.01" placeholder="optional" class="mt-input" style="width:140px;text-align:right;">
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>

<?php if($errors->any()): ?>
  <div class="errors-box">
    <b>Fix errors:</b>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($e); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/companies/form.blade.php ENDPATH**/ ?>