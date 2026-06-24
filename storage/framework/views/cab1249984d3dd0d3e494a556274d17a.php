<div class="form-stack">
  <div class="form-group">
    <label class="form-label">Category Name</label>
    <input name="name" value="<?php echo e(old('name', $category->name ?? '')); ?>" class="mt-input" placeholder="e.g., Dura Air, Spring Collection">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Company</label>
      <select name="company_id" class="mt-select">
        <option value="">-- None (Global) --</option>
        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($comp->id); ?>" <?php if(old('company_id', $category->company_id ?? $selectedCompanyId) == $comp->id): echo 'selected'; endif; ?>>
            <?php echo e($comp->name); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      <div class="text-xs text-muted">Assign to a specific company (e.g., Dura)</div>
    </div>

    <div class="form-group">
      <label class="form-label">Parent Category</label>
      <select name="parent_id" class="mt-select">
        <option value="">-- Root Category --</option>
        <?php if(isset($availableParents)): ?>
          <?php $__currentLoopData = $availableParents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($parent->id); ?>" <?php if(old('parent_id', $category->parent_id ?? ($parentId ?? null)) == $parent->id): echo 'selected'; endif; ?>>
              <?php echo e($parent->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      </select>
      <?php $__errorArgs = ['parent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      <div class="text-xs text-muted">Make this a sub-category under another category</div>
    </div>
  </div>

  <?php if(($parentCategory ?? null) || ($category->parent_id ?? null)): ?>
    <?php
      $displayParent = $parentCategory ?? ($category->parent_id ? \App\Models\Category::find($category->parent_id) : null);
    ?>
    <?php if($displayParent): ?>
      <div style="padding: 12px; background: #e3f2fd; border-radius: 4px; margin-bottom: 16px;">
        <strong>Parent:</strong> <?php echo e($displayParent->name); ?> 
        <?php if($displayParent->company): ?>
          from <strong><?php echo e($displayParent->company->name); ?></strong>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Group</label>
      <select name="group_key" class="mt-select">
        <option value="">-- Select --</option>
        <option value="foam" <?php if(old('group_key', $category->group_key ?? '')==='foam'): echo 'selected'; endif; ?>>Foam</option>
        <option value="spring" <?php if(old('group_key', $category->group_key ?? '')==='spring'): echo 'selected'; endif; ?>>Spring</option>
        <option value="fabric" <?php if(old('group_key', $category->group_key ?? '')==='fabric'): echo 'selected'; endif; ?>>Fabric</option>
        <option value="hardware" <?php if(old('group_key', $category->group_key ?? '')==='hardware'): echo 'selected'; endif; ?>>Hardware</option>
        <option value="accessories" <?php if(old('group_key', $category->group_key ?? '')==='accessories'): echo 'selected'; endif; ?>>Accessories</option>
        <option value="other" <?php if(old('group_key', $category->group_key ?? '')==='other'): echo 'selected'; endif; ?>>Other</option>
      </select>
      <?php $__errorArgs = ['group_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
      <label class="form-label">Unit Type (how you sell)</label>
      <select name="unit_type" class="mt-select">
        <option value="unit" <?php if(old('unit_type', $category->unit_type ?? 'unit')==='unit'): echo 'selected'; endif; ?>>Unit / Piece</option>
        <option value="meter" <?php if(old('unit_type', $category->unit_type ?? '')==='meter'): echo 'selected'; endif; ?>>Meter</option>
        <option value="kg" <?php if(old('unit_type', $category->unit_type ?? '')==='kg'): echo 'selected'; endif; ?>>Kilogram (KG)</option>
        <option value="sqft" <?php if(old('unit_type', $category->unit_type ?? '')==='sqft'): echo 'selected'; endif; ?>>Square Feet</option>
      </select>
      <?php $__errorArgs = ['unit_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
  </div>

  <div class="text-xs text-muted">
    💡 <b>Example:</b> Create company "Dura" → then create sub-categories like "Dura Air", "Dura Memory 2in1", "Dura Spine" for easy product organization.
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/categories/form.blade.php ENDPATH**/ ?>