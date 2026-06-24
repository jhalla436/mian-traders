<?php $__env->startSection('content'); ?>
  <?php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'manager']);
  ?>

  <div class="page-header">
    <div>
      <h2 class="page-title"><?php echo e($company->name); ?></h2>
      <div class="page-subtitle">
        <span class="font-bold">Company</span>
        <?php if(!empty($company->phone_main)): ?>
          <?php $waCompany = \App\Support\WhatsApp::url($company->phone_main); ?>
          • Phone: <b><?php echo e($company->phone_main); ?></b>
          <?php if($waCompany): ?> <a href="<?php echo e($waCompany); ?>" target="_blank" rel="noopener" class="badge-wa" style="margin-left:4px;">WA</a> <?php endif; ?>
        <?php endif; ?>
        <?php if(!empty($company->email)): ?> • Email: <b><?php echo e($company->email); ?></b> <?php endif; ?>
        <?php if(!empty($company->address)): ?> • Address: <b><?php echo e($company->address); ?></b> <?php endif; ?>
      </div>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.companies.edit', $company)); ?>" class="btn btn-secondary btn-sm">Company Settings</a>
      <?php if($canManage): ?>
        <a href="<?php echo e(route('mt.company_contacts.create', $company)); ?>" class="btn btn-success btn-sm">+ Add Contact</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="filter-bar" style="justify-content:space-between;">
    <div class="flex gap-8 items-center flex-wrap">
      <div class="mt-tabs">
        <a href="<?php echo e(route('mt.companies.edit', $company)); ?>" class="mt-tab">Details</a>
        <span class="mt-tab active">Contacts</span>
      </div>
      <form method="GET" action="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="flex gap-8 items-center">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search name / role / phone..." class="mt-input" style="min-width:240px;">
        <button class="btn btn-secondary btn-sm">Search</button>
        <?php if(!empty($q)): ?>
          <a href="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="btn btn-outline btn-sm">Clear</a>
        <?php endif; ?>
      </form>
    </div>
    <span class="text-xs text-muted">Total: <b><?php echo e($contacts->count()); ?></b><?php if(!empty($q)): ?> • Filter: <b><?php echo e($q); ?></b><?php endif; ?></span>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Role</th>
          <th>Phone</th>
          <th>Reports To</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <div class="font-bold"><?php echo e($ct->name); ?></div>
              <div class="text-xs text-muted">
                <?php if(!empty($ct->email)): ?> <?php echo e($ct->email); ?> <?php endif; ?>
                <?php if(!empty($ct->address)): ?> • <?php echo e($ct->address); ?> <?php endif; ?>
              </div>
              <?php if(!empty($ct->note)): ?>
                <div class="text-xs text-muted" style="margin-top:2px;"><?php echo e($ct->note); ?></div>
              <?php endif; ?>
            </td>
            <td><?php echo e($ct->role_title ?? '-'); ?></td>
            <td>
              <?php $p1 = $ct->phone_primary ?? ''; $p2 = $ct->phone_alt ?? ''; $wa1 = \App\Support\WhatsApp::url($p1); $wa2 = \App\Support\WhatsApp::url($p2); ?>
              <div class="flex gap-8 items-center flex-wrap">
                <span><?php echo e($p1 !== '' ? $p1 : '-'); ?></span>
                <?php if($wa1): ?> <a href="<?php echo e($wa1); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a> <?php endif; ?>
              </div>
              <?php if(!empty($p2)): ?>
                <div class="text-xs text-muted flex gap-8 items-center flex-wrap" style="margin-top:4px;">
                  <span>Alt: <?php echo e($p2); ?></span>
                  <?php if($wa2): ?> <a href="<?php echo e($wa2); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a> <?php endif; ?>
                </div>
              <?php endif; ?>
            </td>
            <td><?php echo e($ct->reportsTo?->name ?? '-'); ?></td>
            <td><span class="badge <?php echo e($ct->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($ct->is_active ? 'Yes' : 'No'); ?></span></td>
            <td>
              <div class="actions">
                <?php if($canManage): ?>
                  <a href="<?php echo e(route('mt.company_contacts.edit', [$company, $ct])); ?>" class="btn btn-primary btn-xs">Edit</a>
                  <form method="POST" action="<?php echo e(route('mt.company_contacts.destroy', [$company, $ct])); ?>" onsubmit="return confirm('Delete this contact?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted text-xs">View only</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No contacts found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap">
      <a href="<?php echo e(route('mt.companies.index')); ?>" class="btn btn-secondary btn-sm">← Back to Companies</a>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\companies\contacts\index.blade.php ENDPATH**/ ?>