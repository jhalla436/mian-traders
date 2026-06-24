<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo e($sale->id); ?></title>
    <style>
        * { margin: 0; padding: 0; }
        body { 
            font-family: Helvetica, Arial, sans-serif; 
            font-size: 12px; 
            color: #333;
            line-height: 1.4;
        }
        .header { margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .muted { color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { 
            border: 1px solid #ddd; 
            padding: 6px 8px; 
            text-align: left;
            font-size: 11px;
        }
        th { background: #f0f0f0; font-weight: bold; }
        .right { text-align: right; }
        .totals { margin-top: 10px; width: 100%; }
        .totals td { border: none; padding: 4px 0; font-size: 11px; }
        b { font-weight: bold; }
        hr { border: none; border-top: 1px solid #ddd; margin: 8px 0; }
        div { margin-bottom: 2px; }
        p { margin-bottom: 4px; }
    </style>
</head>
<body>
    <?php ($receiptUrl = \App\Support\SaleReceipt::publicUrl($sale)); ?>
    <?php ($displayReceivedAmount = (float)($sale->received_amount ?? 0)); ?>
    <?php ($grossAmount = (float)collect($sale->items ?? [])->sum(fn ($it) => (float)($it->base_line_total ?? $it->line_total ?? 0))); ?>
    <?php ($subtotalAmount = (float)($sale->subtotal_amount ?? collect($sale->items ?? [])->sum(fn ($it) => (float)($it->line_total ?? 0)))); ?>
    <?php ($itemDiscountAmount = (float)($sale->item_discount_total ?? max($grossAmount - $subtotalAmount, 0))); ?>
    <?php ($overallDiscountAmount = (float)($sale->overall_discount_amount ?? max($subtotalAmount - (float)($sale->total_amount ?? 0), 0))); ?>
    <?php if($displayReceivedAmount <= 0 && (float)($sale->paid_amount ?? 0) > 0): ?>
        <?php ($displayReceivedAmount = (float)$sale->paid_amount); ?>
    <?php endif; ?>
    <div class="header">
        <div class="title"><?php echo e($sale->shop?->name ?? 'Shop'); ?></div>
        <div class="muted">
            <?php echo e($sale->shop?->owner_name ?? ''); ?><br>
            <?php echo e($sale->shop?->address ?? ''); ?><br>
            <?php echo e($sale->shop?->phone ?? ''); ?>

        </div>
        <hr>
        <table style="width:100%;margin-top:8px;border:none;">
            <tr>
                <td style="width:72%;border:none;padding:0 16px 0 0;vertical-align:top;">
                    <div><b>Invoice #:</b> <?php echo e($sale->id); ?></div>
                    <div><b>Date:</b> <?php echo e($sale->created_at?->format('d-m-Y h:i A')); ?></div>
                    <div><b>Customer Name:</b> <?php echo e($sale->customer_name ?? '-'); ?></div>
                    <div><b>Mobile Number:</b> <?php echo e($sale->customer_phone ?? '-'); ?></div>
                    <div><b>Address:</b> <?php echo e($sale->customer_address ?? '-'); ?></div>
                    <div style="margin-top:8px;">
                        <b>Payment Details:</b>
                        <?php echo $__env->make('mt.sales._payment_details', [
                            'sale' => $sale,
                            'lineStyle' => 'font-size:11px;color:#555;line-height:1.5;margin-top:3px;'
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </td>
                <td style="width:28%;border:none;padding:0;vertical-align:top;text-align:center;">
                    <?php echo $__env->make('mt.sales._receipt_qr', [
                        'sale' => $sale,
                        'qrSize' => 96,
                        'qrTitle' => 'Scan To Open',
                        'qrSubtitle' => 'Invoice #' . $sale->id,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Rate</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($it->product_name ?? $it->product?->name ?? 'Item'); ?>

                    <?php if((float)($it->discount_amount ?? 0) > 0): ?>
                        <div class="muted">
                            Base <?php echo e(number_format((float)($it->base_price ?? $it->price ?? 0), 2)); ?>

                            | Disc <?php echo e(number_format((float)($it->discount_percent ?? 0), 2)); ?>%
                            | Save <?php echo e(number_format((float)($it->discount_amount ?? 0), 2)); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(!empty($it->note)): ?> <div class="muted"><?php echo e($it->note); ?></div> <?php endif; ?>
                </td>
                <td class="right"><?php echo e($it->qty); ?></td>
                <td class="right"><?php echo e(number_format((float)($it->price ?? $it->unit_price ?? 0), 2)); ?></td>
                <td class="right"><?php echo e(number_format((float)($it->line_total ?? $it->total_price ?? 0), 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="right">Gross Total:</td>
            <td class="right" style="width:140px;"><?php echo e(number_format($grossAmount, 2)); ?></td>
        </tr>
        <?php if($itemDiscountAmount > 0.009): ?>
        <tr>
            <td class="right">Product Discount:</td>
            <td class="right"><?php echo e(number_format($itemDiscountAmount, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($overallDiscountAmount > 0.009): ?>
        <tr>
            <td class="right">Overall Discount:</td>
            <td class="right"><?php echo e(number_format($overallDiscountAmount, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="right"><b>Total:</b></td>
            <td class="right" style="width:140px;"><b><?php echo e(number_format((float)$sale->total_amount, 2)); ?></b></td>
        </tr>
        <tr>
            <td class="right">Paid:</td>
            <td class="right"><?php echo e(number_format((float)($sale->paid_amount ?? 0), 2)); ?></td>
        </tr>
        <tr>
            <td class="right">Received:</td>
            <td class="right"><?php echo e(number_format($displayReceivedAmount, 2)); ?></td>
        </tr>
        <?php if((float)($sale->change_returned ?? 0) > 0): ?>
        <tr>
            <td class="right">Change Returned:</td>
            <td class="right"><?php echo e(number_format((float)($sale->change_returned ?? 0), 2)); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="right">Balance:</td>
            <td class="right"><?php echo e(number_format((float)($sale->balance_amount ?? 0), 2)); ?></td>
        </tr>
    </table>

    <?php if(!empty($sale->note)): ?>
        <p><b>Note:</b> <?php echo e($sale->note); ?></p>
    <?php endif; ?>

    <p class="muted">Receipt link: <?php echo e($receiptUrl); ?></p>
    <p class="muted">Thank you for your purchase.</p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sales/invoice_pdf.blade.php ENDPATH**/ ?>