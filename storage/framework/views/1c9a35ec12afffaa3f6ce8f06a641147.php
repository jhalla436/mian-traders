
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Add Variant – <?php echo e($heading->name); ?></h2>
    <a href="<?php echo e(route('mt.variants.index', $heading)); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="<?php echo e(route('mt.variants.store', $heading)); ?>" enctype="multipart/form-data" class="flex gap-12 flex-wrap items-center">
      <?php echo csrf_field(); ?>
      <input name="variant_code" placeholder="Code number (e.g. 101)" required class="mt-input" style="min-width:200px;max-width:260px;">
      <input name="selling_price_default" type="number" step="0.01" placeholder="Default sell price" class="mt-input" style="min-width:200px;max-width:260px;">
      <input name="image" type="file" accept="image/*" class="mt-input" style="min-width:240px;max-width:300px;padding:8px;">
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
    <?php if($errors->any()): ?>
      <div class="errors-box" style="margin-top:16px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\variants\create.blade.php ENDPATH**/ ?>