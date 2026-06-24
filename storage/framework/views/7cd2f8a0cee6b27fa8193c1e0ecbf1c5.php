<?php
  $group = $group ?? new \App\Models\CompanyGroup();
?>

<div class="form-stack" style="max-width:520px;">
  <div class="form-group">
    <label class="form-label">Group Name</label>
    <input name="name" value="<?php echo e(old('name', $group->name)); ?>" placeholder="e.g. Almari" class="mt-input">
  </div>
  <div class="form-group">
    <label class="form-label">Key</label>
    <input name="key" value="<?php echo e(old('key', $group->key)); ?>" placeholder="e.g. almari (only a-z 0-9 _)" class="mt-input">
  </div>
  <div class="form-group">
    <label class="form-label">Sort Order</label>
    <input name="sort_order" type="number" value="<?php echo e(old('sort_order', $group->sort_order ?? 0)); ?>" class="mt-input">
  </div>
  <label class="form-checkbox">
    <input type="checkbox" name="is_active" <?php if(old('is_active', (int)($group->is_active ?? 1)) == 1): echo 'checked'; endif; ?>>
    Active
  </label>
  <?php if($errors->any()): ?>
    <div class="errors-box">
      <b>Fix errors:</b>
      <ul>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($e); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\company_groups\form.blade.php ENDPATH**/ ?>