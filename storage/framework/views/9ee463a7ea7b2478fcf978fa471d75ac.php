<?php $__env->startSection('content'); ?>
<div class="page-header">
  <div>
    <h2 class="page-title">Backup & Reset</h2>
    <div class="page-subtitle">Create a backup, then clear selected dummy data.</div>
  </div>
</div>

<?php if(session('success')): ?>
  <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
  <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
  <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:16px;align-items:start;">
  <div class="card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;">
      <div>
        <h3 style="margin:0 0 6px;font-size:18px;font-weight:800;color:#0f172a;">Reset Sections</h3>
        <p class="text-muted" style="margin:0;font-size:13px;line-height:1.5;">
          A JSON backup is saved automatically before anything is deleted. Users, shops, sessions, cache, jobs and migration records are kept.
        </p>
      </div>
      <button type="button" class="btn btn-secondary btn-sm" id="selectAllResetGroups">Select All</button>
    </div>

    <form method="POST" action="<?php echo e(route('mt.system_reset.store')); ?>" id="systemResetForm">
      <?php echo csrf_field(); ?>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px;margin-bottom:18px;">
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label style="display:flex;gap:10px;align-items:flex-start;border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#fff;cursor:pointer;">
            <input type="checkbox" name="groups[]" value="<?php echo e($group['key']); ?>" class="reset-group-checkbox" style="margin-top:3px;width:16px;height:16px;">
            <span>
              <span style="display:block;font-weight:800;color:#0f172a;font-size:14px;"><?php echo e($group['label']); ?></span>
              <span style="display:block;color:#64748b;font-size:12px;line-height:1.45;margin-top:3px;"><?php echo e($group['description']); ?></span>
              <span style="display:inline-block;margin-top:8px;font-size:11px;font-weight:800;color:#334155;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:999px;padding:3px 8px;">
                <?php echo e(number_format($group['count'])); ?> affected row(s)
              </span>
            </span>
          </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div style="border:1px solid #fecaca;background:#fef2f2;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-weight:900;color:#991b1b;margin-bottom:6px;">Backup is automatic, reset is permanent.</div>
        <div style="font-size:13px;color:#7f1d1d;line-height:1.5;">
          Type <b>RESET</b> below to confirm. The backup file can be downloaded from the Recent Backups panel.
        </div>
      </div>

      <div class="form-grid" style="align-items:end;">
        <div class="form-group">
          <label class="form-label">Confirmation</label>
          <input name="confirm_text" class="mt-input" placeholder="Type RESET" autocomplete="off">
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-danger" style="width:100%;">Backup and Reset Selected</button>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;color:#0f172a;">Recent Backups</h3>
    <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div style="border-bottom:1px solid #e2e8f0;padding:10px 0;">
        <div style="font-size:12px;font-weight:800;color:#0f172a;word-break:break-all;"><?php echo e($backup['filename']); ?></div>
        <div style="font-size:11px;color:#64748b;margin-top:3px;"><?php echo e($backup['modified']); ?> · <?php echo e($backup['size']); ?></div>
        <a href="<?php echo e(route('mt.system_reset.download', $backup['filename'])); ?>" class="btn btn-secondary btn-sm" style="margin-top:8px;">Download</a>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="text-muted" style="font-size:13px;">No reset backups yet.</div>
    <?php endif; ?>
  </div>
</div>

<script>
  document.getElementById('selectAllResetGroups')?.addEventListener('click', function() {
    const boxes = Array.from(document.querySelectorAll('.reset-group-checkbox'));
    const shouldCheck = boxes.some(box => !box.checked);
    boxes.forEach(box => box.checked = shouldCheck);
    this.textContent = shouldCheck ? 'Clear All' : 'Select All';
  });

  document.getElementById('systemResetForm')?.addEventListener('submit', function(event) {
    const checked = document.querySelectorAll('.reset-group-checkbox:checked').length;
    if (!checked) {
      event.preventDefault();
      alert('Select at least one reset section.');
      return;
    }

    if (!confirm('Backup will be created first. Continue with reset?')) {
      event.preventDefault();
    }
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/system_reset/index.blade.php ENDPATH**/ ?>