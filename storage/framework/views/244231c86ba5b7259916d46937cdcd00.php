<?php $__env->startSection('content'); ?>
<?php
  // Construct WhatsApp order text
  $orderText = "*Purchase Order #{$order->id}*\n";
  $orderText .= "*Company:* " . ($order->company?->name ?? 'Unknown') . "\n";
  $orderText .= "*Date:* " . ($order->order_date ? $order->order_date->format('Y-m-d') : now()->toDateString()) . "\n";
  $orderText .= "*Status:* " . strtoupper($order->status) . "\n\n";
  $orderText .= "*Order List:*\n";
  
  foreach($order->items as $idx => $it) {
      $qtyStr = rtrim(rtrim(number_format((float)$it->qty, 3), '0'), '.');
      $pName = $it->product?->name ?? 'Unknown Product';
      $orderText .= ($idx + 1) . ". {$pName} (Qty: {$qtyStr})\n";
  }
  
  $orderText .= "\n*Goods Total:* Rs. " . number_format((float)$order->goods_total, 2) . "\n";
  if ($order->note) {
      $orderText .= "*Note:* {$order->note}\n";
  }
  $encodedText = rawurlencode($orderText);
?>

  <div class="page-header">
    <div>
      <div class="page-subtitle">Company Order Details</div>
      <h2 class="page-title" style="display:flex; align-items:center; gap:10px;">
        Order #<?php echo e($order->id); ?>

        <span class="badge badge-info"><?php echo e(strtoupper($order->status)); ?></span>
      </h2>
    </div>
    <div class="page-actions">
      <?php if($order->status !== 'received'): ?>
        <form method="POST" action="<?php echo e(route('mt.company_orders.destroy', $order)); ?>" onsubmit="return confirm('Are you sure you want to delete this order? This will also delete any recorded payments for this order.')" style="display:inline-block;">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button class="btn btn-danger btn-sm">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete Order
          </button>
        </form>
      <?php endif; ?>
      <a href="<?php echo e(route('mt.company_orders.pdf', $order)); ?>" class="btn btn-teal btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        Export PDF
      </a>
      <a href="<?php echo e(route('mt.company_orders.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
    </div>
  </div>

  <!-- Summary Cards Row -->
  <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:16px;">
    <div class="card" style="padding:14px 18px;">
      <div class="filter-label" style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase;">Goods Total</div>
      <div style="font-size:20px; font-weight:900; color:#0f172a; margin-top:4px;">Rs. <?php echo e(number_format((float)$order->goods_total, 2)); ?></div>
    </div>
    <div class="card" style="padding:14px 18px;">
      <div class="filter-label" style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase;">Amount Paid</div>
      <div style="font-size:20px; font-weight:900; color:#10b981; margin-top:4px;">Rs. <?php echo e(number_format((float)$order->paid_amount, 2)); ?></div>
    </div>
    <div class="card" style="padding:14px 18px;">
      <div class="filter-label" style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase;">Remaining Balance</div>
      <div style="font-size:20px; font-weight:900; color:#ef4444; margin-top:4px;">Rs. <?php echo e(number_format((float)$order->balance, 2)); ?></div>
    </div>
    <div class="card" style="padding:14px 18px; display:flex; flex-direction:column; justify-content:center;">
      <div class="filter-label" style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:4px;">Payment Status</div>
      <?php
        $badgeClass = 'badge-danger';
        if ($order->payment_status === 'paid') $badgeClass = 'badge-success';
        elseif ($order->payment_status === 'partial') $badgeClass = 'badge-info';
      ?>
      <div>
        <span class="badge <?php echo e($badgeClass); ?>" style="font-size:12px; padding:4px 12px; border-radius:12px;">
          <?php echo e(strtoupper($order->payment_status ?: 'unpaid')); ?>

        </span>
      </div>
    </div>
  </div>

  <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; align-items:start; margin-bottom:16px;">
    <!-- Items Table -->
    <div class="table-card" style="margin: 0;">
      <div style="padding:16px 20px; background:#f8fafc; border-bottom:2px solid #e2e8f0; font-weight:800; font-size:14px; color:#0f172a;">Order Items</div>
      <table class="mt-table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th class="text-right">Base Price</th>
            <th class="text-right">Orig. Disc%</th>
            <th class="text-right">Extra Disc%</th>
            <th class="text-right">Net Cost</th>
            <th class="text-right" style="width: 80px;">Qty</th>
            <th class="text-right">Line Total</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
              <td>
                <div class="font-bold"><?php echo e($it->product?->name); ?></div>
                <?php if($it->product?->sku): ?>
                  <div class="text-xs text-muted">SKU: <?php echo e($it->product->sku); ?></div>
                <?php endif; ?>
                <?php if($isShell): ?>
                  <div class="text-xs text-info font-semibold">Shell formula (<?php echo e($it->product->width_in); ?>x<?php echo e($it->product->length_in); ?>x<?php echo e($it->product->height_in); ?>)</div>
                <?php endif; ?>
              </td>
              <td class="text-right"><?php echo e(number_format($basePrice, 2)); ?></td>
              <td class="text-right"><?php echo e($it->original_discount > 0 ? number_format($it->original_discount, 2) . '%' : '-'); ?></td>
              <td class="text-right"><?php echo e($it->extra_discount > 0 ? number_format($it->extra_discount, 2) . '%' : '-'); ?></td>
              <td class="text-right font-semibold"><?php echo e(number_format((float)$it->unit_cost, 2)); ?></td>
              <td class="text-right"><?php echo e(rtrim(rtrim(number_format((float)$it->qty, 3), '0'), '.')); ?></td>
              <td class="text-right font-bold" style="color:#6366f1;"><?php echo e(number_format((float)$it->line_total, 2)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php if($order->note): ?>
        <div style="padding:16px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:13px; color:#475569;">
          <b>Note:</b> <?php echo e($order->note); ?>

        </div>
      <?php endif; ?>
    </div>

    <!-- Right Sidebar Information -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      <!-- Send Order Share Panel -->
      <div class="card">
        <h3 style="margin:0 0 12px; font-size:15px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
          <span style="font-size:18px;">💬</span> Send Order list
        </h3>
        <p class="text-xs text-muted" style="margin-bottom:12px;">Instantly send the purchase list to company representative's WhatsApp.</p>
        
        <div style="display:flex; flex-direction:column; gap:8px;">
          <?php
            $hasContacts = false;
          ?>

          <!-- Main Company Phone -->
          <?php if($order->company?->phone_main): ?>
            <?php
              $hasContacts = true;
              $waUrl = \App\Support\WhatsApp::url($order->company->phone_main, $orderText);
            ?>
            <?php if($waUrl): ?>
              <a href="<?php echo e($waUrl); ?>" target="_blank" class="btn btn-success btn-sm" style="justify-content:center;">
                Send to Main (<?php echo e($order->company->phone_main); ?>)
              </a>
            <?php endif; ?>
          <?php endif; ?>

          <!-- Representative Contacts -->
          <?php if($order->company?->contacts): ?>
            <?php $__currentLoopData = $order->company->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($contact->phone_primary && $contact->is_active): ?>
                <?php
                  $hasContacts = true;
                  $cWaUrl = \App\Support\WhatsApp::url($contact->phone_primary, $orderText);
                ?>
                <?php if($cWaUrl): ?>
                  <a href="<?php echo e($cWaUrl); ?>" target="_blank" class="btn btn-primary btn-sm" style="justify-content:center;">
                    Send to <?php echo e($contact->name); ?> (<?php echo e($contact->role_title ?: 'Rep'); ?>)
                  </a>
                <?php endif; ?>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>

          <?php if(!$hasContacts): ?>
            <div class="text-center text-muted text-xs" style="padding:10px; background:#f1f5f9; border-radius:8px;">
              No WhatsApp numbers configured for this company. Add contacts or phone in Company Settings.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Linked Purchase Card / Receive Form -->
      <?php if($order->status === 'open'): ?>
        <div class="card" style="border-left: 4px solid #16a34a;">
          <h3 style="margin:0 0 12px; font-size:15px; font-weight:800; color:#0f172a;">Receive Stock In</h3>
          <form method="POST" action="<?php echo e(route('mt.company_orders.receive', $order)); ?>" class="form-stack">
            <?php echo csrf_field(); ?>
            <div class="form-group">
              <label class="form-label">Purchase Date</label>
              <input type="date" name="purchase_date" value="<?php echo e(now()->toDateString()); ?>" required class="mt-input">
            </div>
            <div class="form-group">
              <label class="form-label">Transport Charges</label>
              <input type="number" step="0.01" name="transport_charges" value="0" class="mt-input">
            </div>
            <div class="form-group">
              <label class="form-label">Payment Made</label>
              <input type="number" step="0.01" name="payment_made" value="0" class="mt-input">
            </div>
            <div class="form-group">
              <label class="form-label">Note</label>
              <input name="note" placeholder="Optional" class="mt-input">
            </div>
            <button class="btn btn-success" style="width:100%; justify-content:center; margin-top:8px;">Receive & Stock In</button>
          </form>
        </div>
      <?php else: ?>
        <div class="card" style="background: #f8fafc;">
          <div class="font-bold" style="font-size:13px; color:#475569;">Stock Status</div>
          <div class="badge badge-success" style="margin-top:8px; font-size:11px;">STOCK IN COMPLETED</div>
          <?php if($order->receivedPurchase): ?>
            <div style="margin-top:10px; font-size:12px; color:#64748b;">
              Linked Invoice: <a href="<?php echo e(route('mt.purchases.show', $order->receivedPurchase)); ?>" style="color:#6366f1; font-weight:700;">Purchase #<?php echo e($order->received_purchase_id); ?></a>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Payments Section -->
  <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; align-items:start; margin-bottom:16px;">
    <!-- Payment Logs -->
    <div class="table-card" style="margin: 0;">
      <div style="padding:16px 20px; background:#f8fafc; border-bottom:2px solid #e2e8f0; font-weight:800; font-size:14px; color:#0f172a;">Payment Records</div>
      <table class="mt-table">
        <thead>
          <tr>
            <th>Payment Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Recorded By</th>
            <th class="text-right">Paid Amount (Rs.)</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $orderPayments = $order->ledgerEntries->where('entry_type', 'payment')->where('direction', 'credit');
          ?>
          <?php $__empty_1 = true; $__currentLoopData = $orderPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($pay->entry_date?->format('Y-m-d')); ?></td>
              <td><span class="badge badge-success">PAYMENT</span></td>
              <td><?php echo e($pay->description ?: '-'); ?></td>
              <td><?php echo e($pay->user ? $pay->user->name : '-'); ?></td>
              <td class="text-right font-bold" style="color: #10b981;"><?php echo e(number_format((float)$pay->amount, 2)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr class="empty-row"><td colspan="5">No payment records saved for this order.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Record Payment Form Card -->
    <?php if($order->status !== 'cancelled' && $order->balance > 0): ?>
      <div class="card" style="border-top: 4px solid #6366f1;">
        <h3 style="margin:0 0 12px; font-size:15px; font-weight:800; color:#0f172a;">Record Order Payment</h3>
        <form method="POST" action="<?php echo e(route('mt.company_orders.payments', $order)); ?>" class="form-stack">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="form-label">Payment Date</label>
            <input type="date" name="payment_date" value="<?php echo e(now()->toDateString()); ?>" required class="mt-input">
          </div>
          <div class="form-group">
            <label class="form-label">Payment Amount (Rs.)</label>
            <input type="number" step="0.01" name="amount" value="<?php echo e($order->balance); ?>" max="<?php echo e($order->balance); ?>" min="0.01" required class="mt-input text-right" style="font-weight:700; color:#10b981; font-size:15px;">
            <div class="text-xs text-muted" style="margin-top:2px;">Remaining balance is Rs. <?php echo e(number_format($order->balance, 2)); ?></div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <input name="description" placeholder="e.g. Paid via bank transfer" class="mt-input">
          </div>
          <button class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">Save Payment</button>
        </form>
      </div>
    <?php elseif($order->balance <= 0): ?>
      <div class="card text-center" style="background:#f0fdf4; border:1px solid #bbf7d0; display:flex; align-items:center; justify-content:center; padding: 30px 20px;">
        <div>
          <div style="font-size:24px; margin-bottom:8px;">✅</div>
          <div class="font-bold" style="color:#15803d; font-size:14px;">ORDER FULLY PAID</div>
          <p class="text-xs text-muted" style="margin:4px 0 0;">All ledger entries and payments have cleared the payable amount.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\company_orders\show.blade.php ENDPATH**/ ?>