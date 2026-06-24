
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Edit Discount Rule</h2>
    <a href="<?php echo e(route('mt.discount_rules.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:900px;">
    <form method="POST" action="<?php echo e(route('mt.discount_rules.update', ['discount_rule' => $discount_rule->id])); ?>" class="form-stack">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="flex gap-12 flex-wrap items-center">
        <label class="form-label">Scope:</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="company" <?php if(old('scope_type', $discount_rule->scope_type)==='company'): echo 'checked'; endif; ?>> Company</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="category" <?php if(old('scope_type', $discount_rule->scope_type)==='category'): echo 'checked'; endif; ?>> Category</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="product" <?php if(old('scope_type', $discount_rule->scope_type)==='product'): echo 'checked'; endif; ?>> Product</label>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Company</label>
          <select name="company_id" class="mt-select">
            <option value="">-- select --</option>
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($c->id); ?>" <?php if((int)old('company_id', ($discount_rule->scope_type==='company' ? $discount_rule->scope_id : null))===(int)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" class="mt-select">
            <option value="">-- select --</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cat->id); ?>" <?php if((int)old('category_id', ($discount_rule->scope_type==='category' ? $discount_rule->scope_id : null))===(int)$cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Product</label>
          <select name="product_id" class="mt-select">
            <option value="">-- select --</option>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($p->id); ?>" <?php if((int)old('product_id', ($discount_rule->scope_type==='product' ? $discount_rule->scope_id : null))===(int)$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Discount Type</label>
          <select name="discount_type_id" required class="mt-select">
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->id); ?>" <?php if((int)old('discount_type_id', $discount_rule->discount_type_id)===(int)$t->id): echo 'selected'; endif; ?>><?php echo e($t->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rule Type</label>
          <select name="rule_type" required class="mt-select">
            <?php $__currentLoopData = $ruleTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($k); ?>" <?php if(old('rule_type', $discount_rule->rule_type)===$k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Percent 1</label>
          <input name="percent_1" type="number" step="0.01" value="<?php echo e(old('percent_1', $discount_rule->percent_1)); ?>" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Percent 2</label>
          <input name="percent_2" type="number" step="0.01" value="<?php echo e(old('percent_2', $discount_rule->percent_2)); ?>" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Fixed Purchase Price</label>
          <input name="fixed_purchase_price" type="number" step="0.01" value="<?php echo e(old('fixed_purchase_price', $discount_rule->fixed_purchase_price)); ?>" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="<?php echo e(old('note', $discount_rule->note)); ?>" class="mt-input">
      </div>
      <div class="flex gap-8">
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      <?php if($errors->any()): ?>
        <div class="errors-box"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div class="mt-alert mt-alert-error"><?php echo e(session('error')); ?></div>
      <?php endif; ?>
    </form>

    <form method="POST" action="<?php echo e(route('mt.discount_rules.destroy', ['discount_rule' => $discount_rule->id])); ?>" style="margin-top:16px;">
      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
      <button type="submit" class="btn btn-danger">Delete Rule</button>
    </form>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\discount_rules\edit.blade.php ENDPATH**/ ?>