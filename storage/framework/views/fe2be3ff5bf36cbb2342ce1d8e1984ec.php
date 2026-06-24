<?php $user = $user ?? new \App\Models\User(); ?>

<div class="form-stack" style="max-width:520px;">
  <div class="form-group">
    <label class="form-label">Name</label>
    <input name="name" value="<?php echo e(old('name', $user->name)); ?>" placeholder="Name" class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Email</label>
    <input name="email" value="<?php echo e(old('email', $user->email)); ?>" placeholder="Email" class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Role</label>
    <?php $role = old('role', $user->role ?? 'cashier'); ?>
    <select name="role" class="mt-select">
      <option value="admin" <?php if($role==='admin'): echo 'selected'; endif; ?>>Admin</option>
      <option value="manager" <?php if($role==='manager'): echo 'selected'; endif; ?>>Manager</option>
      <option value="cashier" <?php if($role==='cashier'): echo 'selected'; endif; ?>>Cashier</option>
    </select>
  </div>

  <?php
    $assigned = $assigned ?? [];
    $oldShops = old('shops');
    if (is_array($oldShops)) { $assigned = array_map('intval', $oldShops); }
  ?>

  <?php if(isset($shops)): ?>
    <div class="card" style="background:#f8fafc;padding:16px;">
      <div class="font-bold" style="margin-bottom:6px;">Shop Access</div>
      <div class="text-xs text-muted" style="margin-bottom:10px;">Select which shops this user can access. (Admin can see all shops even if empty)</div>
      <div class="form-stack" style="gap:8px;">
        <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="form-checkbox">
            <input type="checkbox" name="shops[]" value="<?php echo e($s->id); ?>" <?php echo e(in_array((int)$s->id, $assigned, true) ? 'checked' : ''); ?>>
            <?php echo e($s->name ?? ('Shop #' . $s->id)); ?>

          </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="form-group">
    <label class="form-label">Password</label>
    <input name="password" type="password" placeholder="Password <?php echo e($user->exists ? '(leave blank to keep)' : ''); ?>" class="mt-input">
  </div>

  <label class="form-checkbox">
    <input type="checkbox" name="is_active" <?php if(old('is_active', (int)($user->is_active ?? 1)) == 1): echo 'checked'; endif; ?>>
    Active
  </label>

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
</div>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\users\form.blade.php ENDPATH**/ ?>