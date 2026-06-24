

<?php $__env->startSection('content'); ?>
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Discount Rule</h2>
    <a href="<?php echo e(route('mt.discount_rules.index')); ?>" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="<?php echo e(route('mt.discount_rules.store')); ?>" style="display:grid;gap:12px;max-width:900px;">
      <?php echo csrf_field(); ?>

      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <label><b>Scope:</b></label>
        <label><input type="radio" name="scope_type" value="company" checked> Company</label>
        <label><input type="radio" name="scope_type" value="category"> Category</label>
        <label><input type="radio" name="scope_type" value="product"> Product</label>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <select name="company_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Company (if scope=company)</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="category_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Category (if scope=category)</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="product_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Product (if scope=product)</option>
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <select name="discount_type_id" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Discount Type</option>
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="rule_type" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <?php $__currentLoopData = $ruleTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>"><?php echo e($label); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <input name="percent_1" type="number" step="0.001" placeholder="%1 (e.g. 12.5 / 29.7)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
        <input name="percent_2" type="number" step="0.001" placeholder="%2 (only for two-step e.g. 5)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
        <input name="fixed_purchase_price" type="number" step="0.01" placeholder="Fixed purchase price (if fixed)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
      </div>

      <input name="note" placeholder="Note (optional)" style="padding:8px;border:1px solid #ddd;border-radius:8px;">

      <button type="submit" style="padding:10px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Save Rule
      </button>

      <?php if(session('error')): ?>
        <div style="background:#fee2e2;padding:10px;border-radius:10px;"><?php echo e(session('error')); ?></div>
      <?php endif; ?>

      <?php if($errors->any()): ?>
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_rules/create.blade.php ENDPATH**/ ?>