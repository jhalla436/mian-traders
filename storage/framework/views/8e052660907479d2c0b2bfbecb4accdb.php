
<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Variants – <?php echo e($heading->name); ?></h2>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.headings.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
      <a href="<?php echo e(route('mt.variants.create', $heading)); ?>" class="btn btn-primary btn-sm">+ Add Variant</a>
    </div>
  </div>
  <div class="card">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
      <?php $__currentLoopData = $variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="cart-item" style="text-align:center;">
          <?php if($v->image_path): ?>
            <img src="<?php echo e(asset('storage/'.$v->image_path)); ?>" style="width:100%;height:120px;object-fit:cover;border-radius:10px;background:#f1f5f9;" />
          <?php else: ?>
            <div style="height:120px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;" class="text-muted text-sm">No Image</div>
          <?php endif; ?>
          <div style="font-size:20px;font-weight:800;margin-top:10px;color:#0f172a;">#<?php echo e($v->variant_code); ?></div>
          <div class="text-sm text-muted">Sell: <?php echo e($v->selling_price_default ?? '-'); ?></div>
          <form method="POST" action="<?php echo e(route('mt.variants.destroy', [$heading, $v])); ?>" style="margin-top:10px;">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-danger btn-xs" style="width:100%;">Delete</button>
          </form>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($variants->count() === 0): ?>
        <div class="text-muted text-sm" style="padding:24px;border:1px dashed #e2e8f0;border-radius:12px;">No variants yet.</div>
      <?php endif; ?>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\variants\index.blade.php ENDPATH**/ ?>