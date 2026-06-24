

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Discount Rules</h2>
    <a href="<?php echo e(route('mt.discount_rules.create')); ?>" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Add</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Scope</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Discount Type</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Rule</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
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
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($scopeName); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($r->discountType?->name); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo e($ruleText); ?></td>
            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              <a href="<?php echo e(route('mt.discount_rules.edit', $r)); ?>" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>
              <form method="POST" action="<?php echo e(route('mt.discount_rules.destroy', $r)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" style="padding:6px 10px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($rules->count()===0): ?>
          <tr><td colspan="4" style="padding:8px;">No discount rules yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_rules/index.blade.php ENDPATH**/ ?>