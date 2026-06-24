
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Heading</h2>
    <a href="<?php echo e(route('mt.headings.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.headings.store')); ?>" class="flex gap-12 flex-wrap items-center">
      <?php echo csrf_field(); ?>
      <input name="name" placeholder="e.g. Lamination, UV Sheets, Lasani Simple" required class="mt-input" style="min-width:280px;max-width:400px;">
      <select name="category_id" required class="mt-select" style="min-width:220px;max-width:300px;">
        <option value="">Select Category</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?> (<?php echo e($c->unit_type ?? 'unit'); ?>)</option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
    <?php if($errors->any()): ?>
      <div class="errors-box" style="margin-top:16px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\headings\create.blade.php ENDPATH**/ ?>