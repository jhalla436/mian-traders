

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Discount Rules</h2>
    <a href="<?php echo e(route('mt.discount_rules.create')); ?>" class="btn btn-primary btn-sm">+ Add</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Scope</th>
          <th>Discount Type</th>
          <th>Rule</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $scopeName = $r->scope_type.' #'.$r->scope_id;
            if($r->scope_type==='company') $scopeName = 'Company: '.($companies[$r->scope_id]->name ?? ('#'.$r->scope_id));
            if($r->scope_type==='category') $scopeName = 'Category: '.($categories[$r->scope_id]->name ?? ('#'.$r->scope_id));
            if($r->scope_type==='product') $scopeName = 'Product: '.($products[$r->scope_id]->name ?? ('#'.$r->scope_id));
            $ruleText = $r->rule_type;
            if($r->rule_type==='percent_once') $ruleText = ($r->percent_1 ?? 0).'%';
            if($r->rule_type==='percent_twostep') $ruleText = ($r->percent_1 ?? 0).'% + '.($r->percent_2 ?? 0).'%';
            if($r->rule_type==='fixed_purchase') $ruleText = 'Fixed: '.($r->fixed_purchase_price ?? 0);
            if($r->rule_type==='none') $ruleText = 'No discount (manual)';
          ?>
          <tr>
            <td class="font-bold"><?php echo e($scopeName); ?></td>
            <td><?php echo e($r->discountType?->name); ?></td>
            <td><span class="badge badge-info"><?php echo e($ruleText); ?></span></td>
            <td>
              <div class="actions">
                <a href="<?php echo e(route('mt.discount_rules.edit', $r)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="<?php echo e(route('mt.discount_rules.destroy', $r)); ?>">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($rules->count()===0): ?>
          <tr class="empty-row"><td colspan="4">No discount rules yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\discount_rules\index.blade.php ENDPATH**/ ?>