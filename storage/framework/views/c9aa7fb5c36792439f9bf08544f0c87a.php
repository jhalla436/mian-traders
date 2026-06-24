<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order #<?php echo e($order->id); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #0f172a;
        }
        .header-meta {
            margin-top: 5px;
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .total-section td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total-label {
            font-weight: bold;
            color: #475569;
        }
        .grand-total {
            font-size: 16px;
            font-weight: 900;
            color: #4f46e5;
            background-color: #f8fafc;
        }
        .note-box {
            margin-top: 30px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
        }
        .note-title {
            font-weight: bold;
            color: #475569;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <div class="header-meta">Order ID: #<?php echo e($order->id); ?></div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Company:</td>
            <td><strong><?php echo e($order->company?->name); ?></strong></td>
            <td class="meta-label">Order Date:</td>
            <td><?php echo e(optional($order->order_date)->format('Y-m-d')); ?></td>
        </tr>
        <tr>
            <td class="meta-label">Created By:</td>
            <td><?php echo e($order->createdBy ? $order->createdBy->name : '-'); ?></td>
            <td class="meta-label">Status:</td>
            <td><strong><?php echo e(strtoupper($order->status)); ?></strong></td>
        </tr>
        <?php if($order->company?->phone_main): ?>
        <tr>
            <td class="meta-label">Phone:</td>
            <td><?php echo e($order->company->phone_main); ?></td>
            <td class="meta-label">Payment Status:</td>
            <td><?php echo e(strtoupper($order->payment_status)); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Category</th>
                <th>Product Name & SKU</th>
                <th class="text-right">Base Price</th>
                <th class="text-right" style="width: 50px;">Disc 1 (%)</th>
                <th class="text-right" style="width: 50px;">Disc 2 (%)</th>
                <th class="text-right">Net Cost</th>
                <th class="text-right" style="width: 60px;">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isShell = ($it->product?->pricing_mode === 'shell');
                    $basePrice = 0.0;
                    if ($isShell) {
                        $w = (float)($it->product->width_in ?? 0);
                        $l = (float)($it->product->length_in ?? 0);
                        $h = (float)($it->product->height_in ?? 0);
                        if ($w > 0 && $l > 0 && $h > 0) {
                            $cft = ($w * $l * $h) / 144.0;
                            $basePrice = $cft * (float)($it->product->shell_rate ?? 0);
                        }
                    } else {
                        $basePrice = (float)($it->product?->mrp ?? 0);
                    }
                ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td style="color:#64748b; font-size:11px;"><?php echo e($it->product?->category ? $it->product->category->name : 'Uncategorized'); ?></td>
                    <td>
                        <strong><?php echo e($it->product?->name); ?></strong>
                        <?php if($it->product?->sku): ?>
                            <div style="font-size:10px; color:#94a3b8;">SKU: <?php echo e($it->product->sku); ?></div>
                        <?php endif; ?>
                        <?php if($isShell): ?>
                            <div style="font-size:10px; color:#6366f1;">Shell Mode: (<?php echo e($it->product->width_in); ?>x<?php echo e($it->product->length_in); ?>x<?php echo e($it->product->height_in); ?>)</div>
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><?php echo e(number_format($basePrice, 2)); ?></td>
                    <td class="text-right"><?php echo e($it->original_discount > 0 ? number_format($it->original_discount, 1) . '%' : '-'); ?></td>
                    <td class="text-right"><?php echo e($it->extra_discount > 0 ? number_format($it->extra_discount, 1) . '%' : '-'); ?></td>
                    <td class="text-right"><?php echo e(number_format((float)$it->unit_cost, 2)); ?></td>
                    <td class="text-right"><?php echo e(rtrim(rtrim(number_format((float)$it->qty, 3), '0'), '.')); ?></td>
                    <td class="text-right font-bold"><?php echo e(number_format((float)$it->line_total, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="total-section">
        <tr>
            <td class="total-label">Goods Total:</td>
            <td class="text-right"><strong>Rs. <?php echo e(number_format((float)$order->goods_total, 2)); ?></strong></td>
        </tr>
        <tr>
            <td class="total-label">Paid Amount:</td>
            <td class="text-right">Rs. <?php echo e(number_format((float)$order->paid_amount, 2)); ?></td>
        </tr>
        <tr class="grand-total">
            <td class="total-label" style="color:#4f46e5;">Remaining Balance:</td>
            <td class="text-right">Rs. <?php echo e(number_format((float)$order->balance, 2)); ?></td>
        </tr>
    </table>

    <?php if($order->note): ?>
    <div class="note-box">
        <div class="note-title">Order Note:</div>
        <div><?php echo e($order->note); ?></div>
    </div>
    <?php endif; ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\company_orders\pdf.blade.php ENDPATH**/ ?>