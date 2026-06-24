<?php $__env->startSection('content'); ?>
<?php
  $prefillCompany = $prefillCompany ?? null;
?>

  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">New Purchase (Stock In)</h2>
    <a href="<?php echo e(route('mt.purchases.index')); ?>" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <form method="POST" action="<?php echo e(route('mt.purchases.store')); ?>" style="background:#fff;padding:14px;border-radius:10px;">
    <?php echo csrf_field(); ?>

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Purchase Date</div>
        <input type="date" name="purchase_date" value="<?php echo e(old('purchase_date', now()->toDateString())); ?>" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <?php $__errorArgs = ['purchase_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">Company (optional)</div>
        <select id="company_id" name="company_id" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:280px;" onchange="recalcAllCosts()">
          <option value="">-- Not a company (local supplier) --</option>
          <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php if((string)old('company_id', (string)$prefillCompany) === (string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">Local Supplier Name</div>
        <input name="supplier_name" value="<?php echo e(old('supplier_name')); ?>" placeholder="If not company" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:240px;">
        <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">Invoice #</div>
        <input name="invoice_no" value="<?php echo e(old('invoice_no')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:180px;">
        <?php $__errorArgs = ['invoice_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Transport Charges (we paid)</div>
        <input type="number" step="0.01" name="transport_charges" value="<?php echo e(old('transport_charges', 0)); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <?php $__errorArgs = ['transport_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">This will be added in Company Ledger as <b>credit</b> (deduct later).</div>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;">Payment made to company</div>
        <input type="number" step="0.01" name="payment_made" value="<?php echo e(old('payment_made', 0)); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        <?php $__errorArgs = ['payment_made'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div style="flex:1;min-width:280px;">
        <div style="font-size:12px;color:#6b7280;">Note</div>
        <input name="note" value="<?php echo e(old('note')); ?>" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Optional">
        <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div style="border:1px solid #eee;border-radius:10px;overflow:hidden;margin-bottom:12px;">
      <div style="background:#f9fafb;padding:10px;font-weight:800;">Items</div>
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Product</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Qty</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Unit Cost</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">Line Total</th>
            <th style="text-align:right;border-bottom:1px solid #eee;padding:8px;">#</th>
          </tr>
        </thead>
        <tbody id="lines">
          <tr>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;">
              <select name="product_id[]" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" onchange="calcCostForRow(this)">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <input name="qty[]" type="number" step="0.01" value="1" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:120px;text-align:right;" oninput="recalc()">
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <input name="unit_cost[]" type="number" step="0.01" value="0" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:140px;text-align:right;" oninput="recalc()">
              <div style="font-size:11px;color:#6b7280;margin-top:4px;">If you select a Company, unit cost will auto-load from company discount rules (you can edit).</div>
            </td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;font-weight:800;" class="line_total">0.00</td>
            <td style="padding:8px;border-bottom:1px solid #f2f2f2;text-align:right;">
              <button type="button" onclick="removeRow(this)" style="padding:8px 10px;border-radius:10px;background:#ef4444;color:#fff;border:0;">X</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="padding:10px;display:flex;gap:10px;justify-content:space-between;align-items:center;flex-wrap:wrap;">
        <button type="button" onclick="addRow()" style="padding:10px 12px;border-radius:10px;background:#2563eb;color:#fff;border:0;">+ Add Line</button>
        <div style="font-weight:900;">Goods Total: <span id="goodsTotal">0.00</span></div>
      </div>
    </div>

    <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#dc2626;font-size:12px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <button style="padding:12px 14px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;">Save Purchase & Stock In</button>
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
      } catch(e){
        console.error(e);
        recalc();
      }
    }

    async function recalcAllCosts(){
      const companyId = document.getElementById('company_id').value;
      if (!companyId) { recalc(); return; }
      for (const tr of document.querySelectorAll('#lines tr')){
        const pid = tr.querySelector('select[name="product_id[]"]').value;
        try {
          const cost = await fetchCost(companyId, pid);
          tr.querySelector('input[name="unit_cost[]"]').value = fmt(cost);
        } catch(e){ console.error(e); }
      }
      recalc();
    }

    function removeRow(btn){
      const tbody = document.getElementById('lines');
      if (tbody.querySelectorAll('tr').length <= 1) {
        alert('At least 1 line is required.');
        return;
      }
      btn.closest('tr').remove();
      recalc();
    }

    recalc();
    setTimeout(recalcAllCosts, 100);
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/purchases/create.blade.php ENDPATH**/ ?>