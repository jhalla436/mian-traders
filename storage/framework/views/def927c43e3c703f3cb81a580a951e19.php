<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Shops</h2>
    <a href="<?php echo e(route('mt.shops.create')); ?>" class="btn btn-primary btn-sm">+ Add Shop</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Owner</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Status</th>
          <th class="text-right">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($s->id); ?></td>
            <td class="font-bold"><?php echo e($s->name); ?></td>
            <td><?php echo e($s->owner_name ?? '-'); ?></td>
            <td><?php echo e($s->phone ?? '-'); ?></td>
            <td><?php echo e($s->address ?? '-'); ?></td>
            <td><span class="badge <?php echo e($s->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($s->is_active ? 'Active' : 'Disabled'); ?></span></td>
            <td class="text-right">
              <a href="<?php echo e(route('mt.shops.edit', $s)); ?>" class="btn btn-secondary btn-xs">Edit</a>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/shops/index.blade.php ENDPATH**/ ?>