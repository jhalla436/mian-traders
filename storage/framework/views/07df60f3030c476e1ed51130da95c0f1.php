<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Design Name</label>
    <input name="name" value="<?php echo e(old('name', $sheetDesign->name ?? '')); ?>" required class="mt-input" placeholder="e.g., Oak Wood Grain, Cherry Wood">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-group">
    <label class="form-label">Finish</label>
    <input name="finish" value="<?php echo e(old('finish', $sheetDesign->finish ?? '')); ?>" class="mt-input" placeholder="e.g., Glossy, Matt, Textured">
    <?php $__errorArgs = ['finish'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
</div>

<div class="form-grid" style="margin-top: 16px;">
  <div class="form-group">
    <label class="form-label">Color Group</label>
    <input name="color_group" value="<?php echo e(old('color_group', $sheetDesign->color_group ?? '')); ?>" class="mt-input" placeholder="e.g., Brown, White, Gray, Yellow">
    <?php $__errorArgs = ['color_group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\sheet_designs\form.blade.php ENDPATH**/ ?>