<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo e(config('app.name', 'Mian Traders')); ?></title>
</head>

<body style="font-family:Arial;margin:0;background:#f6f6f6;">
<?php
  $role = auth()->user()?->role ?? 'cashier';
  $canSeeCost = \App\Support\Authz::canSeeCost();
  $isActive = function(string $routeName){
    try {
      return request()->routeIs($routeName) ? 'background:#1f2937;' : 'background:transparent;';
    } catch (\Throwable $e) {
      return '';
    }
  };
?>

<div style="display:flex;min-height:100vh;">

  
  <div style="width:240px;background:#111827;color:#fff;padding:16px;position:sticky;top:0;height:100vh;overflow:auto;">
    <h3 style="margin-top:0;"><?php echo e(config('app.name', 'Mian Traders')); ?></h3>

    <div style="color:#9ca3af;font-size:12px;margin-bottom:10px;">
      Role: <b style="color:#fff;"><?php echo e($role); ?></b>
    </div>

    <a href="<?php echo e(route('dashboard')); ?>"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('dashboard')); ?>">
      Dashboard
    </a>

    <a href="<?php echo e(route('mt.pos.index')); ?>"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.pos.index')); ?>">
      POS
    </a>

    <a href="<?php echo e(route('mt.products.index')); ?>"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.products.index')); ?>">
      Products
    </a>

    <?php if($canSeeCost): ?>
      <a href="<?php echo e(route('mt.products.import_form')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.products.import_form')); ?>">
        Import Price List
      </a>

      <a href="<?php echo e(route('mt.purchases.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.purchases.index')); ?>">
        Purchases (Stock In)
      </a>

      <a href="<?php echo e(route('mt.company_orders.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.company_orders.index')); ?>">
        Company Orders
      </a>

      <a href="<?php echo e(route('mt.categories.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.categories.index')); ?>">
        Categories
      </a>

      <a href="<?php echo e(route('mt.companies.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.companies.index')); ?>">
        Companies
      </a>

      <?php if(\Illuminate\Support\Facades\Route::has('mt.company_groups.index')): ?>
        <a href="<?php echo e(route('mt.company_groups.index')); ?>"
           style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.company_groups.index')); ?>">
          Company Groups
        </a>
      <?php endif; ?>

      <?php if(\Illuminate\Support\Facades\Route::has('mt.stock_movements.index')): ?>
        <a href="<?php echo e(route('mt.stock_movements.index')); ?>"
           style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.stock_movements.index')); ?>">
          Stock Movements
        </a>
      <?php endif; ?>

      <a href="<?php echo e(route('mt.discount_types.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.discount_types.index')); ?>">
        Discount Types
      </a>

      <a href="<?php echo e(route('mt.discount_rules.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.discount_rules.index')); ?>">
        Discount Rules
      </a>

      <a href="<?php echo e(route('mt.expenses.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.expenses.index')); ?>">
        Expenses
      </a>

      <a href="<?php echo e(route('mt.recurring_expenses.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.recurring_expenses.index')); ?>">
        Recurring Expenses
      </a>
    <?php endif; ?>

    <?php if(\Illuminate\Support\Facades\Route::has('mt.sales.index')): ?>
      <a href="<?php echo e(route('mt.sales.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.sales.index')); ?>">
        Sales
      </a>
    <?php endif; ?>

    <?php if(\Illuminate\Support\Facades\Route::has('mt.udhar.index')): ?>
      <a href="<?php echo e(route('mt.udhar.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.udhar.index')); ?>">
        Udhar
      </a>
    <?php endif; ?>

    <?php if(\Illuminate\Support\Facades\Route::has('mt.whatsapp.index')): ?>
      <a href="<?php echo e(route('mt.whatsapp.index')); ?>"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;<?php echo e($isActive('mt.whatsapp.index')); ?>">
        WhatsApp
      </a>
    <?php endif; ?>

    <?php if(\Illuminate\Support\Facades\Route::has('logout')): ?>
      <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin-top:14px;">
        <?php echo csrf_field(); ?>
        <button type="submit"
                style="width:100%;padding:10px;border:0;border-radius:8px;background:#374151;color:#fff;cursor:pointer;">
          Logout
        </button>
      </form>
    <?php else: ?>
      <div style="margin-top:14px;color:#9ca3af;font-size:12px;">
        Logout route not enabled.
      </div>
    <?php endif; ?>

  </div>

  
  <div style="flex:1;padding:18px;">

    <?php if(session('success')): ?>
      <div style="background:#dcfce7;border:1px solid #86efac;padding:10px;border-radius:10px;margin-bottom:12px;">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;margin-bottom:12px;">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/layouts/app.blade.php ENDPATH**/ ?>