<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Companies</h2>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <form method="GET" action="<?php echo e(route('mt.companies.index')); ?>" style="display:flex;gap:8px;align-items:center;">
        <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search company..."
               style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:220px;">
        <button style="padding:10px 12px;border-radius:10px;border:0;background:#111827;color:#fff;cursor:pointer;">Search</button>
      </form>

      <a href="<?php echo e(route('mt.companies.create')); ?>" style="padding:10px 12px;border-radius:10px;background:#2563eb;color:#fff;text-decoration:none;">Add Company</a>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Group</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Phone</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Active</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="border-bottom:1px solid #eee;padding:8px;">
              <div style="font-weight:800;"><?php echo e($c->name); ?></div>
              <?php if(!empty($c->address)): ?>
                <div style="font-size:11px;color:#6b7280;"><?php echo e($c->address); ?></div>
              <?php endif; ?>
            </td>

            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($c->group_key ?? '-'); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($c->phone_main ?? '-'); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($c->is_active ? 'Yes' : 'No'); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              <a href="<?php echo e(route('mt.company_contacts.index', $c)); ?>"
                 style="padding:6px 10px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">
                Contacts
              </a>

              <a href="<?php echo e(route('mt.company_ledger.index', $c)); ?>"
                 style="padding:6px 10px;border-radius:8px;background:#0f766e;color:#fff;text-decoration:none;">
                Ledger
              </a>

              <a href="<?php echo e(route('mt.companies.edit', $c)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>

              <form method="POST" action="<?php echo e(route('mt.companies.destroy', $c)); ?>" onsubmit="return confirm('Delete this company?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" style="padding:6px 10px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" style="padding:8px;">No companies added yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <?php echo e($companies->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/companies/index.blade.php ENDPATH**/ ?>