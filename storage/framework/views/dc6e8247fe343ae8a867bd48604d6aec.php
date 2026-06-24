

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 class="page-title">Business Analytics</h2>
        <div class="page-subtitle">Detailed insights into your sales performance</div>
    </div>
    <div class="page-actions">
        <form action="<?php echo e(route('mt.analytics.index')); ?>" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <select name="days" class="mt-input" style="width: 150px; padding: 6px 12px;" onchange="this.form.submit()">
                <option value="7" <?php echo e($days == 7 ? 'selected' : ''); ?>>Last 7 Days</option>
                <option value="30" <?php echo e($days == 30 ? 'selected' : ''); ?>>Last 30 Days</option>
                <option value="90" <?php echo e($days == 90 ? 'selected' : ''); ?>>Last 90 Days</option>
                <option value="365" <?php echo e($days == 365 ? 'selected' : ''); ?>>Last Year</option>
            </select>
        </form>
    </div>
</div>

<style>
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }
    .analytics-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .analytics-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .analytics-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .analytics-list {
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .analytics-item {
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .analytics-item:last-child {
        border-bottom: none;
    }
    .item-rank {
        width: 28px;
        height: 28px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .item-rank.top-1 { background: #fef3c7; color: #92400e; }
    .item-rank.top-2 { background: #e2e8f0; color: #475569; }
    .item-rank.top-3 { background: #ffedd5; color: #9a3412; }

    .item-info {
        flex-grow: 1;
    }
    .item-name {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .item-stats {
        font-size: 12px;
        color: #64748b;
        display: flex;
        gap: 12px;
    }
    .item-value {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        text-align: right;
        flex-shrink: 0;
    }
    .progress-bar-container {
        height: 6px;
        background: #f1f5f9;
        border-radius: 3px;
        margin-top: 8px;
        overflow: hidden;
    }
    .progress-bar {
        height: 100%;
        border-radius: 3px;
    }
    .progress-blue { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .progress-purple { background: linear-gradient(90deg, #a855f7, #c084fc); }
    .progress-teal { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
</style>

<div class="analytics-grid">
    
    <div class="analytics-card">
        <div class="analytics-card-header">
            <h3 class="analytics-card-title">Top Selling Products</h3>
            <span class="badge badge-info">By Revenue</span>
        </div>
        <div class="analytics-list">
            <?php $maxRevenue = $topProducts->max('total_revenue') ?: 1; ?>
            <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="analytics-item">
                    <div class="item-rank top-<?php echo e($index + 1); ?>"><?php echo e($index + 1); ?></div>
                    <div class="item-info">
                        <div class="item-name"><?php echo e($item->product_name); ?></div>
                        <div class="item-stats">
                            <span>Qty: <?php echo e(number_format($item->total_qty, 0)); ?></span>
                            <span>•</span>
                            <span>Avg Price: <?php echo e(number_format($item->total_revenue / ($item->total_qty ?: 1), 0)); ?></span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar progress-blue" style="width: <?php echo e(($item->total_revenue / $maxRevenue) * 100); ?>%"></div>
                        </div>
                    </div>
                    <div class="item-value"><?php echo e(number_format($item->total_revenue, 0)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($topProducts->isEmpty()): ?>
                <div style="padding: 40px; text-align: center; color: #94a3b8;">No data available for this period.</div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="analytics-card">
        <div class="analytics-card-header">
            <h3 class="analytics-card-title">Best Categories</h3>
            <span class="badge badge-success">Top Performing</span>
        </div>
        <div class="analytics-list">
            <?php $maxCatRevenue = $topCategories->max('total_revenue') ?: 1; ?>
            <?php $__currentLoopData = $topCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="analytics-item">
                    <div class="item-rank top-<?php echo e($index + 1); ?>"><?php echo e($index + 1); ?></div>
                    <div class="item-info">
                        <div class="item-name"><?php echo e($item->name); ?></div>
                        <div class="item-stats">
                            <span>Units Sold: <?php echo e(number_format($item->total_qty, 0)); ?></span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar progress-purple" style="width: <?php echo e(($item->total_revenue / $maxCatRevenue) * 100); ?>%"></div>
                        </div>
                    </div>
                    <div class="item-value"><?php echo e(number_format($item->total_revenue, 0)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($topCategories->isEmpty()): ?>
                <div style="padding: 40px; text-align: center; color: #94a3b8;">No data available for this period.</div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="analytics-card">
        <div class="analytics-card-header">
            <h3 class="analytics-card-title">Top Companies</h3>
            <span class="badge badge-warning">Vendor Analysis</span>
        </div>
        <div class="analytics-list">
            <?php $maxCompRevenue = $topCompanies->max('total_revenue') ?: 1; ?>
            <?php $__currentLoopData = $topCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="analytics-item">
                    <div class="item-rank top-<?php echo e($index + 1); ?>"><?php echo e($index + 1); ?></div>
                    <div class="item-info">
                        <div class="item-name"><?php echo e($item->name); ?></div>
                        <div class="item-stats">
                            <span>Contribution: <?php echo e(number_format(($item->total_revenue / $maxCompRevenue) * 100, 1)); ?>% of top</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar progress-teal" style="width: <?php echo e(($item->total_revenue / $maxCompRevenue) * 100); ?>%"></div>
                        </div>
                    </div>
                    <div class="item-value"><?php echo e(number_format($item->total_revenue, 0)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($topCompanies->isEmpty()): ?>
                <div style="padding: 40px; text-align: center; color: #94a3b8;">No data available for this period.</div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="analytics-card">
        <div class="analytics-card-header">
            <h3 class="analytics-card-title">Daily Sales Trend</h3>
            <span class="badge badge-primary">Last <?php echo e($days); ?> Days</span>
        </div>
        <div style="padding: 0;">
            <table class="mt-table" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Date</th>
                        <th style="text-align: right; padding-right: 24px;">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $salesTrend->reverse()->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="padding-left: 24px; font-weight: 600;"><?php echo e(\Carbon\Carbon::parse($trend->date)->format('D, M j')); ?></td>
                            <td style="text-align: right; padding-right: 24px; font-weight: 700; color: #16a34a;"><?php echo e(number_format($trend->total_revenue, 0)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php if($salesTrend->isEmpty()): ?>
                <div style="padding: 40px; text-align: center; color: #94a3b8;">No trend data available.</div>
            <?php endif; ?>
            <?php if($salesTrend->count() > 10): ?>
                <div style="padding: 12px 24px; text-align: center; font-size: 12px; color: #64748b; background: #f8fafc;">
                    Showing latest 10 days of record
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/analytics/index.blade.php ENDPATH**/ ?>