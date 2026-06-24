

<?php $__env->startSection('content'); ?>
<?php
  $q = $q ?? '';
?>

<div class="page-header">
  <h2 class="page-title">Trashed Products</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input name="q" value="<?php echo e($q); ?>" placeholder="Search name..." class="mt-input" style="width:220px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="page-actions mb-16" style="display:flex;gap:8px;align-items:center;">
  <a href="<?php echo e(route('mt.products.index')); ?>" class="btn btn-secondary">← Back to products</a>
  <form id="bulk-restore-form" method="POST" action="<?php echo e(route('mt.products.bulk_restore')); ?>" style="display:inline-block;margin-left:8px;">
    <?php echo csrf_field(); ?>
    <button id="bulk-restore-btn" type="button" class="btn btn-success" disabled>Restore selected</button>
  </form>
  <form id="bulk-force-form" method="POST" action="<?php echo e(route('mt.products.bulk_force_destroy')); ?>" style="display:inline-block;margin-left:8px;">
    <?php echo csrf_field(); ?>
    <button id="bulk-force-btn" type="button" class="btn btn-danger" disabled>Delete permanently</button>
  </form>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th style="width:36px;"><input id="select-all" type="checkbox" /></th>
        <th>Name</th>
        <th>Company</th>
        <th>Category</th>
        <th>Deleted At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><input class="row-check" type="checkbox" value="<?php echo e($p->id); ?>" /></td>
          <td class="font-bold"><?php echo e($p->name); ?></td>
          <td><?php echo e($p->company?->name ?? '-'); ?></td>
          <td><?php echo e($p->category?->name ?? '-'); ?></td>
          <td><?php echo e($p->deleted_at?->toDateTimeString() ?? '-'); ?></td>
          <td>
            <form method="POST" action="<?php echo e(route('mt.products.restore', $p)); ?>" style="display:inline-block;">
              <?php echo csrf_field(); ?>
              <button class="btn btn-success btn-xs">Restore</button>
            </form>
            <form method="POST" action="<?php echo e(route('mt.products.force_destroy', $p)); ?>" style="display:inline-block;" onsubmit="return confirm('Permanently delete this product?');">
              <?php echo csrf_field(); ?>
              <button class="btn btn-danger btn-xs">Delete permanently</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr class="empty-row"><td colspan="6">No trashed products found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <div class="pagination-wrap"><?php echo e($products->links()); ?></div>
</div>

<script>
  (function(){
    const selectAll = document.getElementById('select-all');
    const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));
    const restoreBtn = document.getElementById('bulk-restore-btn');
    const forceBtn = document.getElementById('bulk-force-btn');
    const restoreForm = document.getElementById('bulk-restore-form');
    const forceForm = document.getElementById('bulk-force-form');

    function updateButton() {
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      restoreBtn.disabled = checked.length === 0;
      forceBtn.disabled = checked.length === 0;
    }

    if (selectAll) {
      selectAll.addEventListener('change', function(){
        rowChecks().forEach(ch => ch.checked = selectAll.checked);
        updateButton();
      });
    }

    document.addEventListener('change', function(e){
      if (e.target && e.target.classList && e.target.classList.contains('row-check')) updateButton();
    });

    restoreBtn?.addEventListener('click', function(){
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      if (checked.length === 0) return;
      Array.from(restoreForm.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
      checked.forEach(id => {
        const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; restoreForm.appendChild(inp);
      });
      restoreForm.submit();
    });

    forceBtn?.addEventListener('click', function(){
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      if (checked.length === 0) return;
      if (!confirm('Permanently delete ' + checked.length + ' products? This cannot be undone.')) return;
      Array.from(forceForm.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
      checked.forEach(id => {
        const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; forceForm.appendChild(inp);
      });
      forceForm.submit();
    });
  })();
</script>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\products\trash.blade.php ENDPATH**/ ?>