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

<div style="margin-top: 24px; border-top: 1px solid #eee; padding-top: 20px;">
  <h3 style="margin-bottom: 12px; font-size: 1.1rem; font-weight: 600; color: #333;">Company-Specific Sheet Numbers / Design Codes</h3>
  <p style="font-size: 0.85rem; color: #666; margin-bottom: 16px;">
    Enter the code/number used by each company for this design (e.g. 135 for KMI, 4321 for Al-Noor). When importing products or sheets, this will automatically resolve to this design.
  </p>

  <div class="form-grid">
    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $mappedCode = $sheetDesign->getCodeForCompany($company->id);
      ?>
      <div class="form-group">
        <label class="form-label"><?php echo e($company->name); ?></label>
        <input name="sheet_codes[<?php echo e($company->id); ?>]" value="<?php echo e(old('sheet_codes.' . $company->id, $mappedCode)); ?>" class="mt-input" placeholder="e.g., 135 or 4321">
        <?php $__errorArgs = ['sheet_codes.' . $company->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>

<?php if($errors->any()): ?>
  <div class="errors-box" style="margin-top: 20px;">
    <b>Fix errors:</b>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($e); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sheet_designs/form.blade.php ENDPATH**/ ?>