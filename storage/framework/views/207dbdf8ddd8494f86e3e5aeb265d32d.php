<?php $__env->startSection('content'); ?>
<?php
  $prefillCompany = $prefillCompany ?? null;
?>

  <div class="page-header">
    <h2 class="page-title">New Purchase (Stock In)</h2>
    <a href="<?php echo e(route('mt.purchases.index')); ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('mt.purchases.store')); ?>" class="card">
    <?php echo csrf_field(); ?>

    <div class="flex gap-12 flex-wrap items-end" style="margin-bottom:16px;">
      <div class="form-group">
        <label class="form-label">Purchase Date</label>
        <input type="date" name="purchase_date" value="<?php echo e(old('purchase_date', now()->toDateString())); ?>" required class="mt-input">
        <?php $__errorArgs = ['purchase_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="form-group">
        <label class="form-label">Company (optional)</label>
        <select id="company_id" name="company_id" class="mt-select" style="min-width:260px;" onchange="recalcAllCosts()">
          <option value="">-- Not a company --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if((string)old('company_id', (string)$prefillCompany) === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="form-group">
        <label class="form-label">Local Supplier Name</label>
        <input name="supplier_name" value="<?php echo e(old('supplier_name')); ?>" placeholder="If not company" class="mt-input" style="min-width:220px;">
        <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="form-group">
        <label class="form-label">Invoice #</label>
        <input name="invoice_no" value="<?php echo e(old('invoice_no')); ?>" class="mt-input" style="min-width:160px;">
        <?php $__errorArgs = ['invoice_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="flex gap-12 flex-wrap items-end" style="margin-bottom:16px;">
      <div class="form-group">
        <label class="form-label">Transport Charges (we paid)</label>
        <input type="number" step="0.01" name="transport_charges" value="<?php echo e(old('transport_charges', 0)); ?>" class="mt-input">
        <?php $__errorArgs = ['transport_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <span class="text-xs text-muted">Added as <b>credit</b> in Company Ledger.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Payment made to company</label>
        <input type="number" step="0.01" name="payment_made" value="<?php echo e(old('payment_made', 0)); ?>" class="mt-input">
        <?php $__errorArgs = ['payment_made'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="form-group" style="flex:1;min-width:260px;">
        <label class="form-label">Note</label>
        <input name="note" value="<?php echo e(old('note')); ?>" class="mt-input" placeholder="Optional">
        <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="table-card" style="margin-bottom:16px;">
      <div style="padding:12px 16px;background:#f8fafc;border-bottom:2px solid #e2e8f0;font-weight:800;font-size:14px;">Items</div>
      <table class="mt-table">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Line Total</th>
            <th class="text-right">#</th>
          </tr>
        </thead>
        <tbody id="lines">
          <tr>
            <td>
              <select name="product_id[]" class="mt-select" onchange="calcCostForRow(this)">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </td>
            <td class="text-right">
              <input name="qty[]" type="number" step="0.01" value="1" class="mt-input" style="width:100px;text-align:right;" oninput="recalc()">
            </td>
            <td class="text-right">
              <input name="unit_cost[]" type="number" step="0.01" value="0" class="mt-input" style="width:120px;text-align:right;" oninput="recalc()">
              <div class="text-xs text-muted" style="margin-top:2px;">Auto-loads from discount rules.</div>
            </td>
            <td class="text-right font-bold line_total">0.00</td>
            <td class="text-right">
              <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-xs">✕</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="padding:12px 16px;display:flex;gap:12px;justify-content:space-between;align-items:center;flex-wrap:wrap;border-top:1px solid #e2e8f0;">
        <button type="button" onclick="addRow()" class="btn btn-primary btn-sm">+ Add Line</button>
        <div class="font-bold" style="font-size:15px;">Goods Total: <span id="goodsTotal" style="color:#6366f1;">0.00</span></div>
      </div>
    </div>

    <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="form-error" style="margin-bottom:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <button class="btn btn-success">Save Purchase & Stock In</button>
  </form>

  <script>
    function fmt(n){ return (Math.round((n + Number.EPSILON)*100)/100).toFixed(2); }
    async function fetchCost(companyId, productId){
      const url = `<?php echo e(route('mt.purchases.calc_cost')); ?>?company_id=${encodeURIComponent(companyId)}&product_id=${encodeURIComponent(productId)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const js = await res.json();
      if (!js.ok) throw new Error(js.message || 'Cost fetch failed');
      return parseFloat(js.unit_cost || 0);
    }
    function recalc(){
      let goods = 0;
      document.querySelectorAll('#lines tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('input[name="qty[]"]').value || '0');
        const cost = parseFloat(tr.querySelector('input[name="unit_cost[]"]').value || '0');
        const total = qty*cost;
        tr.querySelector('.line_total').textContent = fmt(total);
        goods += total;
      });
      document.getElementById('goodsTotal').textContent = fmt(goods);
    }
    function addRow(){
      const tbody = document.getElementById('lines');
      const first = tbody.querySelector('tr');
      const clone = first.cloneNode(true);
      clone.querySelector('input[name="qty[]"]').value = 1;
      clone.querySelector('input[name="unit_cost[]"]').value = 0;
      clone.querySelector('.line_total').textContent = '0.00';
      clone.querySelector('select[name="product_id[]"]').onchange = function(){ calcCostForRow(this); };
      tbody.appendChild(clone);
      recalcAllCosts();
    }
    async function calcCostForRow(sel){
      const tr = sel.closest('tr');
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      try {
        const cost = await fetchCost(companyId, sel.value);
        tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost);
        recalc();
      } catch(e){ console.error(e); recalc(); }
    }
    async function recalcAllCosts(){
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      for (const tr of document.querySelectorAll('#lines tr')){
        const pid = tr.querySelector('select[name="product_id[]"]').value;
        try { const cost = await fetchCost(companyId, pid); tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost); } catch(e){ console.error(e); }
      }
      recalc();
    }
    function removeRow(btn){
      const tbody = document.getElementById('lines');
      if (tbody.querySelectorAll('tr').length <= 1) { alert('At least 1 line is required.'); return; }
      btn.closest('tr').remove(); recalc();
    }
    recalc(); setTimeout(recalcAllCosts, 100);
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\mt\purchases\create.blade.php ENDPATH**/ ?>