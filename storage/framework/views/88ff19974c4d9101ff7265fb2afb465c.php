<?php $__env->startSection('content'); ?>
<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
?>

<div class="page-header">
  <h2 class="page-title">Udhar Customers</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search phone/name..." class="mt-input" style="width:220px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th>Phone</th>
        <th>Name</th>
        <th class="text-right">Total Sales</th>
        <?php if($canSeeCost): ?>
          <th class="text-right">Total Profit</th>
          <th class="text-right">Realized Profit</th>
        <?php endif; ?>
        <th class="text-right">Current Udhar</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td>
            <?php $wa = \App\Support\WhatsApp::url($r->customer_phone); ?>
            <div class="flex gap-8 items-center flex-wrap">
              <span><?php echo e($r->customer_phone); ?></span>
              <?php if($wa): ?>
                <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a>
              <?php endif; ?>
            </div>
          </td>
          <td><?php echo e($r->customer_name ?? '-'); ?></td>
          <td class="text-right"><?php echo e(number_format((float)$r->total_sales,2)); ?></td>
          <?php if($canSeeCost): ?>
            <td class="text-right"><?php echo e(number_format((float)$r->total_profit,2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float)$r->total_profit_realized,2)); ?></td>
          <?php endif; ?>
          <td class="text-right text-danger font-bold"><?php echo e(number_format((float)$r->total_udhar,2)); ?></td>
          <td>
            <a href="<?php echo e(route('mt.udhar.show', $r->customer_phone)); ?>" class="btn btn-primary btn-xs">Open</a>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
  <div class="pagination-wrap"><?php echo e($rows->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\udhar\index.blade.php ENDPATH**/ ?>