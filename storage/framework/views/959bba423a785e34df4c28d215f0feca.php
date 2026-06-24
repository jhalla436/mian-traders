<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <div>
      <h2 class="page-title">Edit Contact — <?php echo e($company->name); ?></h2>
      <div class="page-subtitle"><b><?php echo e($contact->name); ?></b><?php if(!empty($contact->role_title)): ?> • <?php echo e($contact->role_title); ?><?php endif; ?></div>
    </div>
    <a href="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:950px;">
    <form method="POST" action="<?php echo e(route('mt.company_contacts.update', [$company, $contact])); ?>" class="form-stack">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Name</label><input name="name" value="<?php echo e(old('name', $contact->name)); ?>" required class="mt-input"><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
        <div class="form-group"><label class="form-label">Role / Title</label><input name="role_title" value="<?php echo e(old('role_title', $contact->role_title)); ?>" placeholder="CEO / Sales Head / Manager..." class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Phone (Primary)</label><input name="phone_primary" value="<?php echo e(old('phone_primary', $contact->phone_primary)); ?>" placeholder="0300xxxxxxx" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Phone (Alt)</label><input name="phone_alt" value="<?php echo e(old('phone_alt', $contact->phone_alt)); ?>" placeholder="optional" class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Email</label><input name="email" value="<?php echo e(old('email', $contact->email)); ?>" placeholder="optional" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Reports To</label>
          <select name="reports_to_contact_id" class="mt-select"><option value="">— None —</option><?php $__currentLoopData = $reportToOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>" <?php if((string)old('reports_to_contact_id', $contact->reports_to_contact_id) === (string)$r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?><?php if($r->role_title): ?> (<?php echo e($r->role_title); ?>)<?php endif; ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><input name="address" value="<?php echo e(old('address', $contact->address)); ?>" placeholder="optional" class="mt-input"></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Sort Order</label><input name="sort_order" type="number" min="0" value="<?php echo e(old('sort_order', $contact->sort_order ?? 0)); ?>" class="mt-input"></div>
        <div class="form-group" style="justify-content:flex-end;"><label class="form-checkbox"><input type="checkbox" name="is_active" value="1" <?php if((int)old('is_active', $contact->is_active ? 1 : 0) === 1): echo 'checked'; endif; ?>> Active</label></div>
      </div>
      <div class="form-group"><label class="form-label">Note</label><textarea name="note" rows="3" class="mt-input" style="resize:vertical;" placeholder="optional"><?php echo e(old('note', $contact->note)); ?></textarea></div>
      <?php if($errors->any()): ?><div class="errors-box"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
      <div class="flex gap-8"><button class="btn btn-primary">Update Contact</button><a href="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\companies\contacts\edit.blade.php ENDPATH**/ ?>