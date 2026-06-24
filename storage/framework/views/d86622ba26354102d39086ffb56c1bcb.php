

<?php $__env->startSection('content'); ?>
  <div class="page-header">
    <h2 class="page-title">Categories</h2>
    <a href="<?php echo e(route('mt.categories.create')); ?>" class="btn btn-success btn-sm">+ Add Category</a>
  </div>

  <?php if(count($companies) > 0): ?>
    <div class="filter-section" style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border-radius: 4px;">
      <form method="GET" action="<?php echo e(route('mt.categories.index')); ?>" style="display: flex; gap: 10px; align-items: center;">
        <label for="company_filter" style="margin: 0;">Filter by Company:</label>
        <select name="company_id" id="company_filter" class="mt-select" style="flex: 1; max-width: 300px;">
          <option value="">-- All Companies --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($comp->id); ?>" <?php if($companyId == $comp->id): echo 'selected'; endif; ?>><?php echo e($comp->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
      </form>
    </div>
  <?php endif; ?>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Company</th>
          <th>Type</th>
          <th>Group</th>
          <th>Sub-Categories</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="font-bold"><?php echo e($c->name); ?></td>
            <td><?php echo e($c->company?->name ?? '-'); ?></td>
            <td><?php echo e($c->parent_id ? '📁 Sub-category' : '📦 Root'); ?></td>
            <td><?php echo e($c->group_key ?? '-'); ?></td>
            <td>
              <?php if($c->children->count() > 0): ?>
                <span class="badge badge-info"><?php echo e($c->children->count()); ?></span>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td>
              <div class="actions">
                <?php if(!$c->parent_id): ?>
                  <a href="<?php echo e(route('mt.categories.create', ['parent_id' => $c->id])); ?>" class="btn btn-sm btn-primary" title="Add sub-category">+ Sub</a>
                  <a href="<?php echo e(route('mt.categories.sizes', $c)); ?>" class="btn btn-sm btn-info" title="Manage sizes">📐 Sizes (<?php echo e($c->sizes->count()); ?>)</a>
                <?php endif; ?>
                <a href="<?php echo e(route('mt.categories.edit', $c)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                <a href="<?php echo e(route('mt.categories.confirm_delete', $c)); ?>" class="btn btn-danger btn-xs" title="Delete category">Delete</a>
              </div>
            </td>
          </tr>
          <?php if($c->children->count() > 0): ?>
            <?php $__currentLoopData = $c->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr style="background: #fafafa;">
                <td style="padding-left: 40px;">└─ <?php echo e($child->name); ?></td>
                <td><?php echo e($child->company?->name ?? '-'); ?></td>
                <td>📁 Sub-category</td>
                <td><?php echo e($child->group_key ?? '-'); ?></td>
                <td>-</td>
                <td>
                  <div class="actions">
                    <a href="<?php echo e(route('mt.categories.edit', $child)); ?>" class="btn btn-secondary btn-xs">Edit</a>
                    <a href="<?php echo e(route('mt.categories.confirm_delete', $child)); ?>" class="btn btn-danger btn-xs" title="Delete category">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="empty-row"><td colspan="6">No categories yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-wrap"><?php echo e($categories->links()); ?></div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\categories\index.blade.php ENDPATH**/ ?>