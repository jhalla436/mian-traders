<?php $__env->startSection('content'); ?>
<?php
  $canSeeCost = \App\Support\Authz::canSeeCost();
  $paymentMethodOptions = \App\Support\PaymentMethod::options();
  $displayReceivedAmount = (float)($sale->received_amount ?? 0);
  $grossAmount = (float)collect($sale->items ?? [])->sum(fn ($it) => (float)($it->base_line_total ?? $it->line_total ?? 0));
  $subtotalAmount = (float)($sale->subtotal_amount ?? collect($sale->items ?? [])->sum(fn ($it) => (float)($it->line_total ?? 0)));
  $itemDiscountAmount = (float)($sale->item_discount_total ?? max($grossAmount - $subtotalAmount, 0));
  $overallDiscountAmount = (float)($sale->overall_discount_amount ?? max($subtotalAmount - (float)($sale->total_amount ?? 0), 0));
  if ($displayReceivedAmount <= 0 && (float)($sale->paid_amount ?? 0) > 0) {
    $displayReceivedAmount = (float)$sale->paid_amount;
  }
?>

  <div class="page-header">
    <div>
      <h2 class="page-title">Sale #<?php echo e($sale->id); ?></h2>
      <div class="page-subtitle">
        <?php echo e($sale->status ?? '-'); ?> / <?php echo e($sale->sale_type ?? '-'); ?>

        <?php if($sale->due_date): ?> | Due: <?php echo e($sale->due_date); ?> <?php endif; ?>
      </div>
      <?php if($sale->shop): ?>
        <div class="card" style="margin-top:10px;padding:12px;background:#f8fafc;max-width:420px;">
          <div class="font-bold"><?php echo e($sale->shop->name); ?></div>
          <?php if($sale->shop->owner_name): ?>
            <div class="text-sm">Owner: <?php echo e($sale->shop->owner_name); ?></div>
          <?php endif; ?>
          <div class="text-xs text-muted" style="line-height:1.6;">
            <?php if($sale->shop->address): ?> <div><?php echo e($sale->shop->address); ?></div> <?php endif; ?>
            <?php if($sale->shop->phone): ?> <div>Phone: <?php echo e($sale->shop->phone); ?></div> <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="page-actions">
      <a href="<?php echo e(route('mt.sales.index')); ?>" class="btn btn-secondary btn-sm no-print">← Back</a>
      <?php if($sale->customer_phone): ?>
        <a href="<?php echo e(route('mt.udhar.show', $sale->customer_phone)); ?>" class="btn btn-primary btn-sm no-print">Customer Udhar</a>
        <form method="POST" action="<?php echo e(route('mt.sales.sms', $sale)); ?>" class="d-inline no-print" style="display:inline;" id="smsForm">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-warning btn-sm">SMS Invoice</button>
        </form>
        <a href="<?php echo e(route('mt.sales.whatsapp', $sale)); ?>" class="btn btn-success btn-sm no-print" target="_blank" id="waLink">WhatsApp Invoice</a>

        
        <button type="button" class="btn btn-teal btn-sm no-print" id="allActionsBtn">All</button>

        <script>
          document.getElementById('allActionsBtn').addEventListener('click', function() {
            // open whatsapp in new named popup
            const waUrl = document.getElementById('waLink').href;
            const popup = window.open(waUrl, 'mtPopup');

            // send SMS via fetch so we don't navigate away
            fetch('<?php echo e(route('mt.sales.sms', $sale)); ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify({})
            });

            // trigger print after tiny delay
            setTimeout(function() { window.print(); }, 100);
            // close popup after delay as well
            setTimeout(function() {
              if (popup && !popup.closed) popup.close();
            }, 20000);
          });
        </script>
      <?php endif; ?>
      <a href="<?php echo e(route('mt.sales.pdf', $sale)); ?>" class="btn btn-secondary btn-sm no-print" target="_blank">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        PDF
      </a>
      <button onclick="window.print()" class="btn btn-teal btn-sm no-print">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print Invoice
      </button>
    </div>
  </div>

  <style>
    .print-only {
      display: none;
    }

    .sale-layout {
      display:grid;
      grid-template-columns:1.2fr 0.8fr;
      gap:16px;
      align-items:start;
    }

    @media (max-width:768px) {
      .sale-layout {
        display:block;
      }
    }

    /* ── Print Styles ─────────────── */
    @media print {
      @page {
        margin: 6mm;
      }

      body { background: #fff !important; font-size: 12px !important; }
      .mt-sidebar, .mt-mobile-toggle, .mt-overlay, .no-print,
      .page-actions, .pos-total-bar, .checkout-section,
      .mt-alert, .mt-alert-success, .mt-alert-error,
      .page-header .card { display: none !important; }
      .mt-app { display: block !important; }
      .mt-main { padding: 0 !important; }
      .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; margin-bottom: 8px !important; }
      .sale-layout { display: block !important; }
      .sale-layout > .card + .card { margin-top: 8px; }
      .cost-col, .profit-section, .print-hide, .screen-qr-only { display: none !important; }
      .print-only { display: block !important; }
      .page-header { box-shadow: none !important; border: none !important; padding: 0 !important; margin-bottom: 12px !important; }
      .page-title { font-size: 18px !important; margin-bottom: 2px !important; }
      .page-subtitle { font-size: 10px !important; }
      .mt-table th, .mt-table td { padding: 4px 6px !important; font-size: 10px !important; }
      .print-invoice-top { margin-bottom: 8px !important; }
      .print-invoice-top .receipt-qr-card { max-width: 180px !important; margin: 0 auto !important; padding: 8px !important; border-radius: 10px !important; }
      .print-invoice-top .receipt-qr-image { width: 72px !important; margin: 8px auto 6px !important; }
      .print-invoice-top .receipt-qr-image svg { width: 72px !important; height: 72px !important; display: block !important; }
    }
  </style>

  <div class="card print-only print-invoice-top">
    <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap;">
      <div style="flex:1 1 150px;min-width:0;">
        <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;">Invoice Summary</div>
        <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:4px;">Sale #<?php echo e($sale->id); ?></div>
        <div style="font-size:10px;color:#64748b;margin-top:2px;"><?php echo e($sale->created_at?->format('d M Y, h:i A')); ?></div>
        
        <?php if($sale->shop): ?>
        <div style="margin-top:8px;font-size:11px;line-height:1.5;">
          <div><b>Shop:</b> <?php echo e($sale->shop->name); ?></div>
          <?php if($sale->shop->phone): ?><div><b>Shop Phone:</b> <?php echo e($sale->shop->phone); ?></div><?php endif; ?>
          <?php if($sale->shop->address): ?><div><b>Shop Address:</b> <?php echo e($sale->shop->address); ?></div><?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div style="margin-top:8px;font-size:11px;line-height:1.5;">
          <div><b>Customer:</b> <?php echo e($sale->customer_name ?? '-'); ?></div>
          <div><b>Mobile:</b> <?php echo e($sale->customer_phone ?? '-'); ?></div>
          <div><b>Address:</b> <?php echo e($sale->customer_address ?? '-'); ?></div>
        </div>
        
        <div style="margin-top:10px;font-size:11px;line-height:1.5;">
          <?php if($grossAmount > 0.009): ?>
            <div><b>Gross Total:</b> Rs <?php echo e(number_format($grossAmount,2)); ?></div>
          <?php endif; ?>
          <?php if($itemDiscountAmount > 0.009): ?>
            <div><b>Product Discount:</b> Rs <?php echo e(number_format($itemDiscountAmount,2)); ?></div>
          <?php endif; ?>
          <?php if($overallDiscountAmount > 0.009): ?>
            <div><b>Overall Discount:</b> Rs <?php echo e(number_format($overallDiscountAmount,2)); ?></div>
          <?php endif; ?>
          <div><b>Total:</b> Rs <?php echo e(number_format((float)$sale->total_amount,2)); ?></div>
          <div><b>Paid:</b> Rs <?php echo e(number_format((float)$sale->paid_amount,2)); ?></div>
          <div><b>Received:</b> Rs <?php echo e(number_format($displayReceivedAmount,2)); ?></div>
          <?php if((float)($sale->change_returned ?? 0) > 0): ?>
            <div><b>Change Returned:</b> Rs <?php echo e(number_format((float)$sale->change_returned,2)); ?></div>
          <?php endif; ?>
          <div><b>Balance:</b> Rs <?php echo e(number_format((float)$sale->balance_amount,2)); ?></div>
        </div>
        
        <div style="margin-top:8px;">
          <?php echo $__env->make('mt.sales._payment_details', [
            'sale' => $sale,
            'lineStyle' => 'font-size:10px;color:#334155;line-height:1.45;margin-top:2px;'
          ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
      </div>
      <div style="flex:0 0 auto;">
        <?php echo $__env->make('mt.sales._receipt_qr', [
          'sale' => $sale,
          'qrSize' => 84,
          'qrTitle' => 'Scan To View Details',
          'qrSubtitle' => 'Invoice #' . $sale->id,
          'wrapperClass' => 'print-qr-card'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
    </div>
  </div>

  <div class="sale-layout">

    <div class="card">
      <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Customer</h3>
      <div><b>Name:</b> <?php echo e($sale->customer_name ?? '-'); ?></div>
      <div class="flex gap-8 items-center flex-wrap" style="margin-top:4px;">
        <b>Mobile:</b>
        <span><?php echo e($sale->customer_phone ?? '-'); ?></span>
        <?php if($sale->customer_phone): ?>
          <a href="<?php echo e(route('mt.sales.whatsapp', $sale)); ?>" target="_blank" rel="noopener" class="badge-wa">WA</a>
        <?php endif; ?>
      </div>
      <div style="margin-top:8px;"><b>Address:</b> <?php echo e($sale->customer_address ?? '-'); ?></div>
      <div style="margin-top:8px;"><b>Note:</b> <?php echo e($sale->note ?? '-'); ?></div>

      <hr class="separator screen-qr-only">

      <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Items</h3>
      <table class="mt-table">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Sell</th>
            <?php if($canSeeCost): ?>
              <th class="text-right cost-col">Purchase</th>
            <?php endif; ?>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <?php echo e($it->product_name); ?>

                <?php if((float)($it->discount_amount ?? 0) > 0): ?>
                  <div class="text-xs text-muted">
                    Base <?php echo e(number_format((float)($it->base_price ?? $it->price),2)); ?>

                    | Disc <?php echo e(number_format((float)($it->discount_percent ?? 0),2)); ?>%
                    | Save Rs <?php echo e(number_format((float)($it->discount_amount ?? 0),2)); ?>

                  </div>
                <?php endif; ?>
              </td>
              <td class="text-right"><?php echo e(number_format((float)$it->qty,2)); ?></td>
              <td class="text-right"><?php echo e(number_format((float)$it->price,2)); ?></td>
              <?php if($canSeeCost): ?>
                <td class="text-right cost-col"><?php echo e(number_format((float)$it->purchase_price,2)); ?></td>
              <?php endif; ?>
              <td class="text-right font-bold"><?php echo e(number_format((float)$it->line_total,2)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Summary</h3>

      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Gross Total</span>
        <b><?php echo e(number_format($grossAmount,2)); ?></b>
      </div>
      <?php if($itemDiscountAmount > 0.009): ?>
        <div class="flex justify-between" style="margin-bottom:8px;">
          <span>Product Discount</span>
          <b><?php echo e(number_format($itemDiscountAmount,2)); ?></b>
        </div>
      <?php endif; ?>
      <?php if($overallDiscountAmount > 0.009): ?>
        <div class="flex justify-between" style="margin-bottom:8px;">
          <span>Overall Discount</span>
          <b><?php echo e(number_format($overallDiscountAmount,2)); ?></b>
        </div>
      <?php endif; ?>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Total</span>
        <b><?php echo e(number_format((float)$sale->total_amount,2)); ?></b>
      </div>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Paid</span>
        <b><?php echo e(number_format((float)$sale->paid_amount,2)); ?></b>
      </div>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Received</span>
        <b><?php echo e(number_format($displayReceivedAmount,2)); ?></b>
      </div>
      <?php if((float)($sale->change_returned ?? 0) > 0): ?>
        <div class="flex justify-between" style="margin-bottom:8px;">
          <span>Change Returned</span>
          <b><?php echo e(number_format((float)$sale->change_returned,2)); ?></b>
        </div>
      <?php endif; ?>
      <div class="flex justify-between" style="margin-bottom:8px;">
        <span>Balance</span>
        <b class="text-danger"><?php echo e(number_format((float)$sale->balance_amount,2)); ?></b>
      </div>

      <hr class="separator">

      <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Payment Details</h3>
      <?php echo $__env->make('mt.sales._payment_details', [
        'sale' => $sale,
        'lineStyle' => 'font-size:13px;color:#334155;line-height:1.6;margin-top:6px;'
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <?php if($canSeeCost): ?>
        <div class="profit-section">
          <hr class="separator">
          <div class="flex justify-between" style="margin-bottom:8px;">
            <span>Profit Total</span>
            <b class="text-success"><?php echo e(number_format((float)$sale->profit_total,2)); ?></b>
          </div>
          <div class="flex justify-between" style="margin-bottom:8px;">
            <span>Profit Realized</span>
            <b class="text-success"><?php echo e(number_format((float)($profitRealized ?? $sale->profit_realized),2)); ?></b>
          </div>
        </div>
      <?php endif; ?>

      <hr class="separator print-hide">

      <div class="screen-qr-only" style="margin:14px 0 18px;">
        <?php echo $__env->make('mt.sales._receipt_qr', [
          'sale' => $sale,
          'qrSize' => 124,
          'qrTitle' => 'Scan To Open This Bill',
          'qrSubtitle' => 'Use your phone camera to retrieve the invoice'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>

      <hr class="separator print-hide">

      <div class="print-hide">
      <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Add Payment</h3>
      <?php if((float)$sale->balance_amount <= 0): ?>
        <div class="badge badge-success" style="padding:10px 14px;font-size:13px;">This bill is fully paid.</div>
      <?php else: ?>
        <form method="POST" action="<?php echo e(route('mt.payments.store')); ?>" class="form-stack">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="sale_id" value="<?php echo e($sale->id); ?>">
          <input name="amount" type="number" step="0.01" placeholder="Payment amount" class="mt-input">
          <select name="method" class="mt-select">
            <?php $__currentLoopData = $paymentMethodOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($methodKey); ?>" <?php if($methodKey === \App\Support\PaymentMethod::HARD_CASH): echo 'selected'; endif; ?>><?php echo e($methodLabel); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <input name="note" placeholder="Note (optional)" class="mt-input">
          <button class="btn btn-primary">Save Payment</button>
        </form>
      <?php endif; ?>

      <hr class="separator">

      <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;">Payments</h3>
      <?php $__empty_1 = true; $__currentLoopData = $sale->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="cart-item">
          <div class="flex justify-between items-center">
            <b><?php echo e(\App\Support\PaymentMethod::label($pay->method ?? null)); ?> • Rs <?php echo e(number_format((float)$pay->amount,2)); ?></b>
            <span class="text-xs text-muted"><?php echo e($pay->created_at); ?></span>
          </div>
          <div class="text-xs text-muted" style="margin-top:4px;"><?php echo e($pay->note ?? '-'); ?></div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-muted text-sm">No payments yet.</div>
      <?php endif; ?>
      </div>
    </div>

  </div>

<?php if(session('auto_print') || request()->get('autoprint')): ?>
<script>
  window.addEventListener('load', function() {
    setTimeout(function() {
      window.print();
      // Auto-close the tab after a small delay to allow print job submission
      setTimeout(function() { window.close(); }, 800);
    }, 150);
  });
</script>
<?php endif; ?>

<?php if(session('send_whatsapp')): ?>
<script>
  window.addEventListener('load', function() {
    const phone = <?php echo json_encode($sale->customer_phone ?? '', 15, 512) ?>;
    if (!phone) {
      alert('No customer phone number available for WhatsApp.');
      return;
    }
    // Build bill text
    let text = '🧾 *Invoice #<?php echo e($sale->id); ?>*\n';
    text += '📅 <?php echo e($sale->created_at?->format("d M Y, h:i A") ?? now()->format("d M Y, h:i A")); ?>\n';
    text += '────────────────\n';
    <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      text += '• <?php echo e(addslashes($it->product_name)); ?>  ×<?php echo e($it->qty); ?>  = Rs <?php echo e(number_format((float)$it->line_total,2)); ?>\n';
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    text += '────────────────\n';
    text += '*Total: Rs <?php echo e(number_format((float)$sale->total_amount,2)); ?>*\n';
    text += 'Paid: Rs <?php echo e(number_format((float)$sale->paid_amount,2)); ?>\n';
    <?php if((float)$sale->balance_amount > 0): ?>
      text += '⚠️ Balance Due: Rs <?php echo e(number_format((float)$sale->balance_amount,2)); ?>\n';
    <?php endif; ?>
    text += '\nThank you for your purchase! 🙏';

    // Clean phone number
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) cleanPhone = '92' + cleanPhone.substring(1);
    if (!cleanPhone.startsWith('92')) cleanPhone = '92' + cleanPhone;

    const waUrl = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(text);
    // open in named popup (created on checkout click)
    window.open(waUrl, 'mtPopup');
    // close popup after 20 seconds to avoid stray windows
    setTimeout(function() {
      const w = window.open('', 'mtPopup');
      if (w && !w.closed) { w.close(); }
    }, 20000);
  });
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sales/show.blade.php ENDPATH**/ ?>