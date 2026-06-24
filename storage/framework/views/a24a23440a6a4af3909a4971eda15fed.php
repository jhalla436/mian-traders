<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <div>
      <h2 class="page-title"><?php echo e($company->name); ?></h2>
      <div class="page-subtitle">
        <span class="font-bold">Company</span>
        <?php if(!empty($company->phone_main)): ?> • <?php echo e($company->phone_main); ?> <?php endif; ?>
        <?php if(!empty($company->address)): ?> • <?php echo e($company->address); ?> <?php endif; ?>
      </div>
    </div>
    <a href="<?php echo e(route('mt.companies.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="filter-bar" style="margin-bottom:16px;">
    <div class="mt-tabs">
      <span class="mt-tab active">Details</span>
      <a href="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="mt-tab">Contacts</a>
    </div>
  </div>

  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.companies.update', $company)); ?>" class="form-stack">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('mt.companies.form', ['company' => $company], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button class="btn btn-primary" style="justify-self:start;">Update Company</button>
    </form>
    <hr class="separator">
    <div class="flex justify-end">
      <a href="<?php echo e(route('mt.company_contacts.index', $company)); ?>" class="btn btn-secondary btn-sm">Manage Contacts →</a>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\companies\edit.blade.php ENDPATH**/ ?>