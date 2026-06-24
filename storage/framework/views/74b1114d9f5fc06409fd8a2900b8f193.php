

<?php $__env->startSection('content'); ?>
<div class="card row" style="justify-content:space-between;">
  <h2 style="margin:0;">Mian Traders – POS</h2>
  <form method="POST" action="<?php echo e(route('mt.pos.clear')); ?>">
    <?php echo csrf_field(); ?>
    <button class="btn btn-gray" type="submit">New Invoice</button>
  </form>
</div>

<div class="card">
  <form class="row" method="POST" action="<?php echo e(route('mt.pos.search')); ?>">
    <?php echo csrf_field(); ?>
    <input name="code" placeholder="Search by sheet code (e.g. 101)" required />
    <button class="btn btn-primary" type="submit">Add</button>
  </form>
</div>

<div class="card">
  <h3>Quick Headings</h3>
  <div class="row">
    <?php $__currentLoopData = $headings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a class="btn btn-primary" href="<?php echo e(route('mt.pos.heading', $h)); ?>"><?php echo e($h->name); ?></a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($headings->count() === 0): ?>
      <div>No headings yet. Add from Headings menu.</div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Cart</h3>
  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Rate</th>
        <th>Total</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td><?php echo e($item['title']); ?></td>
        <td style="width:160px;">
          <form class="row" method="POST" action="<?php echo e(route('mt.pos.update', $key)); ?>">
            <?php echo csrf_field(); ?>
            <input name="qty" type="number" step="0.001" value="<?php echo e($item['qty']); ?>" style="width:90px;" />
        </td>
        <td style="width:240px;">
            <input name="price" type="number" step="0.01" value="<?php echo e($item['price']); ?>" style="width:110px;" />
            <button class="btn btn-primary" type="submit">Update</button>
          </form>
        </td>
        <td><?php echo e(number_format($item['qty'] * $item['price'], 2)); ?></td>
        <td style="width:120px;">
          <form method="POST" action="<?php echo e(route('mt.pos.remove', $key)); ?>">
            <?php echo csrf_field(); ?>
            <button class="btn btn-danger" type="submit">Remove</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <?php if(count($cart) === 0): ?>
        <tr><td colspan="5">Cart empty.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h3 style="text-align:right; margin-top:12px;">Total: <?php echo e(number_format($total, 2)); ?></h3>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\index.blade.php ENDPATH**/ ?>