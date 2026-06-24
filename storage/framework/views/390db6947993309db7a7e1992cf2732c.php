

<?php $__env->startSection('content'); ?>
<div class="card row" style="justify-content:space-between;">
  <h2 style="margin:0;"><?php echo e($heading->name); ?> (Select by picture/code)</h2>
  <a class="btn btn-gray" href="<?php echo e(route('mt.pos.index')); ?>">Back to POS</a>
</div>

<div class="card grid">
  <?php $__currentLoopData = $variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="tile">
      <?php if($v->image_path): ?>
        <img src="<?php echo e(asset('storage/'.$v->image_path)); ?>" />
      <?php else: ?>
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect width='400' height='300' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%236b7280' font-size='22'%3ENo Image%3C/text%3E%3C/svg%3E" />
      <?php endif; ?>
      <div style="font-size:22px; font-weight:700; margin-top:8px;">#<?php echo e($v->variant_code); ?></div>
      <div>Sell: <?php echo e($v->selling_price_default ?? '-'); ?></div>

      <form method="POST" action="<?php echo e(route('mt.pos.add', $v)); ?>">
        <?php echo csrf_field(); ?>
        <button class="btn btn-primary" style="width:100%; margin-top:10px;">Add</button>
      </form>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <?php if($variants->count() === 0): ?>
    <div class="tile">No variants yet. Add from Headings → Variants.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\heading.blade.php ENDPATH**/ ?>