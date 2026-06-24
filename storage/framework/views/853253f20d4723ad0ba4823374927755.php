
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Discount Rule</h2>
    <a href="<?php echo e(route('mt.discount_rules.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:900px;">
    <form method="POST" action="<?php echo e(route('mt.discount_rules.store')); ?>" class="form-stack">
      <?php echo csrf_field(); ?>
      <div class="flex gap-12 flex-wrap items-center">
        <label class="form-label">Scope:</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="company" checked> Company</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="category"> Category</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="product"> Product</label>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Company</label>
          <select name="company_id" class="mt-select">
            <option value="">-- select (if scope=company) --</option>
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" class="mt-select">
            <option value="">-- select (if scope=category) --</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Product</label>
          <select name="product_id" class="mt-select">
            <option value="">-- select (if scope=product) --</option>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Discount Type</label>
          <select name="discount_type_id" required class="mt-select">
            <option value="">Select Discount Type</option>
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rule Type</label>
          <select name="rule_type" required class="mt-select">
            <?php $__currentLoopData = $ruleTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($k); ?>"><?php echo e($label); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Percent 1</label>
          <input name="percent_1" type="number" step="0.001" placeholder="e.g. 12.5" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Percent 2</label>
          <input name="percent_2" type="number" step="0.001" placeholder="e.g. 5 (two-step)" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Fixed Purchase Price</label>
          <input name="fixed_purchase_price" type="number" step="0.01" placeholder="If fixed" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" placeholder="Note (optional)" class="mt-input">
      </div>
      <button type="submit" class="btn btn-primary" style="justify-self:start;">Save Rule</button>
      <?php if(session('error')): ?>
        <div class="mt-alert mt-alert-error"><?php echo e(session('error')); ?></div>
      <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="errors-box">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/discount_rules/create.blade.php ENDPATH**/ ?>