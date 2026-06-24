

<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h2 class="page-title">My Account</h2>
</div>

<div class="card" style="max-width:620px;">
  <form method="POST" action="<?php echo e(route('mt.account.update')); ?>" class="form-stack">
    <?php echo csrf_field(); ?>

    <div class="badge badge-info" style="align-self:start;">Role: <?php echo e(auth()->user()?->role); ?></div>

    <div class="form-group">
      <label class="form-label">Name</label>
      <input name="name" value="<?php echo e(old('name', $user->name)); ?>" placeholder="Name" class="mt-input">
    </div>
    <div class="form-group">
      <label class="form-label">Email</label>
      <input name="email" value="<?php echo e(old('email', $user->email)); ?>" placeholder="Email" class="mt-input">
    </div>

    <hr class="separator">

    <div class="font-bold">Change Password (Optional)</div>

    <div class="form-group">
      <label class="form-label">Current Password</label>
      <input name="current_password" type="password" placeholder="Current Password" class="mt-input">
    </div>
    <div class="form-group">
      <label class="form-label">New Password</label>
      <input name="new_password" type="password" placeholder="New Password (min 4)" class="mt-input">
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

    <button class="btn btn-primary" style="justify-self:start;">Save Changes</button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\account\edit.blade.php ENDPATH**/ ?>