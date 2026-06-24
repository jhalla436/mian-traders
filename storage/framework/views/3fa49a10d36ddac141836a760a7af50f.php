<?php $__env->startSection('content'); ?>
<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
  $hour = (int)now()->format('H');
  $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
  $userName = auth()->user()->name ?? 'User';
?>

<style>
  /* ── Dashboard-specific styles ────────────── */
  .dash-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
    border-radius: 20px; padding: 32px 36px; color: #fff;
    position: relative; overflow: hidden; margin-bottom: 20px;
  }
  .dash-hero::before {
    content: ''; position: absolute; top: -40%; right: -15%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
    border-radius: 50%;
  }
  .dash-hero::after {
    content: ''; position: absolute; bottom: -30%; left: 20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(168,85,247,0.12) 0%, transparent 70%);
    border-radius: 50%;
  }
  .dash-hero-content { position: relative; z-index: 1; }
  .dash-greeting { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 4px; }
  .dash-greeting-sub { font-size: 14px; color: #94a3b8; font-weight: 500; }
  .dash-hero-actions { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; }
  .dash-hero-btn {
    padding: 10px 20px; border-radius: 12px; text-decoration: none;
    font-size: 13px; font-weight: 700; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .dash-hero-btn.primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff;
    box-shadow: 0 4px 16px rgba(99,102,241,0.3);
  }
  .dash-hero-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99,102,241,0.45); color:#fff; }
  .dash-hero-btn.ghost {
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #e2e8f0;
  }
  .dash-hero-btn.ghost:hover { background: rgba(255,255,255,0.14); color:#fff; }
  .dash-hero-btn.danger {
    background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5;
  }
  .dash-hero-btn.danger:hover { background: rgba(239,68,68,0.25); color:#fff; }

  /* ── Metric Cards ────────────────────────── */
  .metrics-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
  @media (max-width: 1100px) { .metrics-row { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr; } }

  .metric-card {
    background: #fff; border-radius: 16px; padding: 22px 24px;
    border: 1px solid rgba(0,0,0,0.04);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    position: relative; overflow: hidden;
    transition: all 0.3s cubic-bezier(.4,0,.2,1);
  }
  .metric-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.08); }
  .metric-card .accent-bar {
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: 16px 16px 0 0;
  }
  .metric-card .metric-icon {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 14px;
  }
  .metric-card .metric-label {
    font-size: 12px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
  }
  .metric-card .metric-value {
    font-size: 24px; font-weight: 900; color: #0f172a;
    letter-spacing: -0.5px; line-height: 1.1;
  }
  .metric-card .metric-footer {
    margin-top: 12px; display: flex; align-items: center; justify-content: space-between;
  }
  .metric-card .metric-sub { font-size: 12px; color: #94a3b8; font-weight: 500; }
  .metric-card .metric-link {
    font-size: 12px; font-weight: 700; color: #6366f1;
    display: inline-flex; align-items: center; gap: 4px;
    transition: gap 0.2s; text-decoration: none;
  }
  .metric-card .metric-link:hover { gap: 8px; }

  .metric-card.red .accent-bar { background: linear-gradient(90deg, #ef4444, #f97316); }
  .metric-card.red .metric-icon { background: #fef2f2; color: #ef4444; }
  .metric-card.red .metric-value { color: #dc2626; }
  .metric-card.blue .accent-bar { background: linear-gradient(90deg, #6366f1, #818cf8); }
  .metric-card.blue .metric-icon { background: #eef2ff; color: #6366f1; }
  .metric-card.green .accent-bar { background: linear-gradient(90deg, #22c55e, #4ade80); }
  .metric-card.green .metric-icon { background: #f0fdf4; color: #22c55e; }
  .metric-card.green .metric-value { color: #16a34a; }
  .metric-card.purple .accent-bar { background: linear-gradient(90deg, #a855f7, #c084fc); }
  .metric-card.purple .metric-icon { background: #faf5ff; color: #a855f7; }
  .metric-card.teal .accent-bar { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
  .metric-card.teal .metric-icon { background: #f0fdfa; color: #14b8a6; }
  .metric-card.teal .metric-value { color: #0d9488; }

  /* ── Dashboard Grid ──────────────────────── */
  .dash-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
  @media (max-width: 1024px) { .dash-grid { grid-template-columns: 1fr; } }

  .dash-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.04);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
  }
  .dash-card-header {
    padding: 20px 24px; display: flex; justify-content: space-between;
    align-items: center; border-bottom: 1px solid #f1f5f9;
  }
  .dash-card-title { font-size: 15px; font-weight: 800; color: #0f172a; }
  .dash-card-badge {
    font-size: 11px; font-weight: 700; padding: 4px 12px;
    border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px;
  }

  .sales-table { width: 100%; border-collapse: collapse; }
  .sales-table th {
    text-align: left; padding: 10px 24px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.6px; color: #94a3b8; background: #fafbfc;
  }
  .sales-table th.right { text-align: right; }
  .sales-table td {
    padding: 14px 24px; border-top: 1px solid #f8fafc; font-size: 13px; color: #334155; vertical-align: middle;
  }
  .sales-table td.right { text-align: right; }
  .sales-table tbody tr { transition: background 0.15s; }
  .sales-table tbody tr:hover { background: #f8fafc; }
  .sales-table .customer-name { font-weight: 700; color: #0f172a; }
  .sales-table .customer-phone { font-size: 11px; color: #94a3b8; margin-top: 1px; }
  .sales-table .amount { font-weight: 700; font-variant-numeric: tabular-nums; }
  .sales-table .balance-red { color: #dc2626; font-weight: 800; }
  .sales-table .balance-green { color: #16a34a; font-weight: 600; }
  .sales-table .status-badge {
    padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.3px;
  }
  .sales-table .status-paid { background: #dcfce7; color: #15803d; }
  .sales-table .status-partial { background: #fef3c7; color: #a16207; }
  .sales-table .status-pending { background: #fee2e2; color: #b91c1c; }
  .sales-table .view-btn {
    padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700;
    color: #6366f1; background: #eef2ff; text-decoration: none;
    transition: all 0.15s; border: 1px solid transparent;
  }
  .sales-table .view-btn:hover { background: #6366f1; color: #fff; }

  .side-stack { display: flex; flex-direction: column; gap: 20px; }
  .alert-card {
    border-radius: 16px; padding: 24px; position: relative; overflow: hidden;
    border: 1px solid rgba(0,0,0,0.04);
  }
  .alert-card.stock-alert { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; }
  .alert-card .alert-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; margin-bottom: 14px;
  }
  .alert-card.stock-alert .alert-icon { background: rgba(245,158,11,0.15); color: #f59e0b; }
  .alert-card .alert-title { font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 4px; }
  .alert-card .alert-value { font-size: 36px; font-weight: 900; color: #78350f; letter-spacing: -1px; }
  .alert-card .alert-link {
    display: inline-flex; align-items: center; gap: 4px;
    margin-top: 12px; font-size: 12px; font-weight: 700; color: #b45309;
    text-decoration: none; transition: gap 0.2s;
  }
  .alert-card .alert-link:hover { gap: 8px; }

  .quick-actions-card { background: #fff; border-radius: 16px; padding: 24px; border: 1px solid rgba(0,0,0,0.04); box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
  .quick-actions-title { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
  .quick-action-list { display: flex; flex-direction: column; gap: 8px; }
  .quick-action {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px; border-radius: 12px; text-decoration: none;
    border: 1px solid #f1f5f9; transition: all 0.2s; color: #334155;
  }
  .quick-action:hover { border-color: #6366f1; background: rgba(99,102,241,0.03); transform: translateX(4px); color:#334155; }
  .quick-action .qa-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
  }
  .quick-action .qa-text { font-size: 13px; font-weight: 600; }
  .quick-action .qa-arrow { margin-left: auto; color: #cbd5e1; font-size: 14px; transition: color 0.2s; }
  .quick-action:hover .qa-arrow { color: #6366f1; }
</style>


<div class="dash-hero">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
    <div class="dash-hero-content">
      <div class="dash-greeting"><?php echo e($greeting); ?>, <?php echo e($userName); ?></div>
      <div class="dash-greeting-sub">Here's what's happening with your business today</div>
    </div>
    <div class="dash-hero-actions">
      <a href="<?php echo e(route('mt.pos.index')); ?>" class="dash-hero-btn primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        Open POS
      </a>
      <a href="<?php echo e(route('mt.sales.index')); ?>" class="dash-hero-btn ghost">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        Sales
      </a>
      <a href="<?php echo e(route('mt.udhar.index')); ?>" class="dash-hero-btn danger">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        Udhar
      </a>
    </div>
  </div>
</div>


<div class="metrics-row">
  <div class="metric-card red">
    <div class="accent-bar"></div>
    <div class="metric-icon">⚠</div>
    <div class="metric-label">Total Udhar</div>
    <div class="metric-value"><?php echo e(number_format((float)($totalUdharAll ?? 0), 0)); ?></div>
    <div class="metric-footer">
      <span class="metric-sub">All customers</span>
      <a href="<?php echo e(route('mt.udhar.index')); ?>" class="metric-link">View <span>→</span></a>
    </div>
  </div>
  <div class="metric-card blue">
    <div class="accent-bar"></div>
    <div class="metric-icon">📊</div>
    <div class="metric-label">Sales Today</div>
    <div class="metric-value"><?php echo e(number_format((float)($salesToday ?? 0), 0)); ?></div>
    <div class="metric-footer">
      <span class="metric-sub"><?php echo e(now()->format('D, M j')); ?></span>
      <?php if($canSeeCost): ?>
        <span class="metric-sub" style="color:#16a34a;font-weight:700;">Realized: <?php echo e(number_format((float)($realizedToday ?? 0), 0)); ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="metric-card purple">
    <div class="accent-bar"></div>
    <div class="metric-icon">📈</div>
    <div class="metric-label">Sales This Week</div>
    <div class="metric-value"><?php echo e(number_format((float)($salesThisWeek ?? 0), 0)); ?></div>
    <div class="metric-footer">
      <span class="metric-sub">Mon – Sun</span>
      <?php if($canSeeCost): ?>
        <span class="metric-sub" style="color:#16a34a;font-weight:700;">Realized: <?php echo e(number_format((float)($realizedThisWeek ?? 0), 0)); ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="metric-card teal">
    <div class="accent-bar"></div>
    <div class="metric-icon">🗓</div>
    <div class="metric-label">Sales This Month</div>
    <div class="metric-value"><?php echo e(number_format((float)($salesThisMonth ?? 0), 0)); ?></div>
    <div class="metric-footer">
      <span class="metric-sub"><?php echo e(now()->format('F Y')); ?></span>
      <?php if($canSeeCost): ?>
        <span class="metric-sub" style="color:#16a34a;font-weight:700;">Realized: <?php echo e(number_format((float)($realizedThisMonth ?? 0), 0)); ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>


<div class="dash-grid">
  
  <div class="dash-card">
    <div class="dash-card-header">
      <div class="dash-card-title">Recent Sales</div>
      <a href="<?php echo e(route('mt.sales.index')); ?>" class="dash-card-badge" style="background:#eef2ff;color:#6366f1;text-decoration:none;">View All →</a>
    </div>
    <?php if(isset($recentSales) && $recentSales->count() > 0): ?>
      <table class="sales-table">
        <thead>
          <tr><th>Bill</th><th>Customer</th><th class="right">Total</th><th class="right">Paid</th><th class="right">Balance</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="amount">#<?php echo e($s->id); ?></td>
              <td>
                <div class="customer-name"><?php echo e($s->customer_name ?: 'Walk-in'); ?></div>
                <?php if($s->customer_phone): ?><div class="customer-phone"><?php echo e($s->customer_phone); ?></div><?php endif; ?>
              </td>
              <td class="right amount"><?php echo e(number_format((float)$s->total_amount, 0)); ?></td>
              <td class="right amount"><?php echo e(number_format((float)$s->paid_amount, 0)); ?></td>
              <td class="right <?php echo e((float)$s->balance_amount > 0 ? 'balance-red' : 'balance-green'); ?>">
                <?php echo e(number_format((float)$s->balance_amount, 0)); ?>

              </td>
              <td>
                <?php $st = strtolower($s->status ?? 'paid'); ?>
                <span class="status-badge status-<?php echo e($st); ?>"><?php echo e($st); ?></span>
              </td>
              <td><a href="<?php echo e(route('mt.sales.show', $s)); ?>" class="view-btn">View</a></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    <?php else: ?>
      <div style="padding:40px 24px;text-align:center;color:#94a3b8;font-size:13px;">No sales yet today.</div>
    <?php endif; ?>
  </div>

  
  <div class="side-stack">
    <div class="alert-card stock-alert">
      <div class="alert-icon">📦</div>
      <div class="alert-title">Low Stock Items</div>
      <div class="alert-value"><?php echo e((int)($lowStockCount ?? 0)); ?></div>
      <a href="<?php echo e(route('mt.products.index')); ?>" class="alert-link">View Products <span>→</span></a>
    </div>

    <?php if($canSeeCost): ?>
    <div class="dash-card" style="padding:21px 24px;">
      <div class="dash-card-header" style="padding:0 0 16px 0; border:none; display:block;">
        <div class="dash-card-title">Profit Summary</div>
        <div style="font-size:11px; color:#94a3b8; font-weight:500; margin-top:2px;">Cash Collected Profit</div>
      </div>
      <div style="display:flex; flex-direction:column; gap:16px;">
        
        <div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;">Today</span>
            <span style="font-size:14px; font-weight:800; color:#16a34a;"><?php echo e(number_format((float)($realizedToday ?? 0), 0)); ?></span>
          </div>
          <div style="font-size:10px; color:#94a3b8; text-align:right;">
            Total: <?php echo e(number_format((float)($profitToday ?? 0), 0)); ?>

          </div>
        </div>
        
        <div style="background:#f1f5f9; height:1px;"></div>

        
        <div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;">This Week</span>
            <span style="font-size:14px; font-weight:800; color:#16a34a;"><?php echo e(number_format((float)($realizedThisWeek ?? 0), 0)); ?></span>
          </div>
          <div style="font-size:10px; color:#94a3b8; text-align:right;">
            Total: <?php echo e(number_format((float)($profitThisWeek ?? 0), 0)); ?>

          </div>
        </div>

        <div style="background:#f1f5f9; height:1px;"></div>

        
        <div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;">This Month</span>
            <span style="font-size:14px; font-weight:800; color:#16a34a;"><?php echo e(number_format((float)($realizedThisMonth ?? 0), 0)); ?></span>
          </div>
          <div style="font-size:10px; color:#94a3b8; text-align:right;">
            Total: <?php echo e(number_format((float)($profitThisMonth ?? 0), 0)); ?>

          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="quick-actions-card">
      <div class="quick-actions-title">Quick Actions</div>
      <div class="quick-action-list">
        <a href="<?php echo e(route('mt.pos.index')); ?>" class="quick-action">
          <div class="qa-icon" style="background:#eef2ff;color:#6366f1;">🛒</div>
          <span class="qa-text">New Sale (POS)</span>
          <span class="qa-arrow">→</span>
        </a>
        <a href="<?php echo e(route('mt.products.index')); ?>" class="quick-action">
          <div class="qa-icon" style="background:#f0fdf4;color:#22c55e;">📦</div>
          <span class="qa-text">Manage Products</span>
          <span class="qa-arrow">→</span>
        </a>
        <a href="<?php echo e(route('mt.expenses.create')); ?>" class="quick-action">
          <div class="qa-icon" style="background:#fef2f2;color:#ef4444;">💸</div>
          <span class="qa-text">Add Expense</span>
          <span class="qa-arrow">→</span>
        </a>
        <a href="<?php echo e(route('mt.purchases.create')); ?>" class="quick-action">
          <div class="qa-icon" style="background:#f0fdfa;color:#14b8a6;">📥</div>
          <span class="qa-text">New Purchase</span>
          <span class="qa-arrow">→</span>
        </a>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\dashboard.blade.php ENDPATH**/ ?>