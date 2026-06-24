<?php $__env->startSection('content'); ?>
<?php
  $isType = function(string $t) use ($type){
    return $type === $t ? 'background:#111827;color:#fff;' : 'background:#e5e7eb;color:#111827;';
  };
?>

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
  <div>
    <h2 style="margin:0;">WhatsApp Numbers</h2>
    <div style="color:#6b7280;font-size:12px;margin-top:4px;">
      Use this to copy/export customer phone numbers for WhatsApp groups.
      <span style="margin-left:6px;">Total matching: <b><?php echo e((int)($totalCount ?? 0)); ?></b></span>
    </div>
  </div>

  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <input name="q" value="<?php echo e($q ?? ''); ?>" placeholder="Search phone/name..."
           style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:220px;">

    <input type="hidden" name="type" value="<?php echo e($type); ?>">
    <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>

    <a href="<?php echo e(route('mt.whatsapp.index', ['type'=>$type])); ?>"
       style="padding:10px 12px;border-radius:10px;background:#e5e7eb;color:#111827;text-decoration:none;">Clear</a>

    <a href="<?php echo e(route('mt.whatsapp.export', ['type'=>$type, 'q'=>$q])); ?>"
       style="padding:10px 12px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;">Download CSV</a>
  </form>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
  <a href="<?php echo e(route('mt.whatsapp.index', ['type'=>'udhar', 'q'=>$q])); ?>"
     style="padding:8px 12px;border-radius:999px;text-decoration:none;<?php echo e($isType('udhar')); ?>">Udhar Customers</a>
  <a href="<?php echo e(route('mt.whatsapp.index', ['type'=>'walkin', 'q'=>$q])); ?>"
     style="padding:8px 12px;border-radius:999px;text-decoration:none;<?php echo e($isType('walkin')); ?>">Walk-in / Paid</a>
  <a href="<?php echo e(route('mt.whatsapp.index', ['type'=>'all', 'q'=>$q])); ?>"
     style="padding:8px 12px;border-radius:999px;text-decoration:none;<?php echo e($isType('all')); ?>">All</a>

  <div style="margin-left:auto;color:#6b7280;font-size:12px;">
    Tip: WhatsApp format is digits only (92xxxxxxxxxx).
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start;">
  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <h3 style="margin:0;">WhatsApp Format (92...)</h3>
      <button type="button" onclick="copyText('waBox')"
              style="padding:8px 12px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">Copy</button>
    </div>
    <textarea id="waBox" rows="10" style="width:100%;margin-top:10px;padding:10px;border:1px solid #ddd;border-radius:10px;"><?php echo e($numbersWa ?? ''); ?></textarea>
    <div style="color:#6b7280;font-size:12px;margin-top:6px;">Paste in WhatsApp "Invite via link" or your phone contacts list.</div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <h3 style="margin:0;">Original Numbers</h3>
      <button type="button" onclick="copyText('origBox')"
              style="padding:8px 12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">Copy</button>
    </div>
    <textarea id="origBox" rows="10" style="width:100%;margin-top:10px;padding:10px;border:1px solid #ddd;border-radius:10px;"><?php echo e($numbersOriginal ?? ''); ?></textarea>
    <div style="color:#6b7280;font-size:12px;margin-top:6px;">These are the exact numbers saved in invoices.</div>
  </div>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;margin-top:12px;">
  <h3 style="margin-top:0;">Customers</h3>

  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Name</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Phones</th>
        <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Total Sales</th>
        <th style="text-align:right;border-bottom:1px solid #eee;padding:10px;">Current Udhar</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Last Purchase</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:10px;">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $p1 = trim((string)($r->customer_phone ?? ''));
          $p2 = trim((string)($r->customer_phone2 ?? ''));
          $p3 = trim((string)($r->customer_phone3 ?? ''));
        ?>
        <tr>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
            <div style="font-weight:800;"><?php echo e($r->customer_name ?? '-'); ?></div>
          </td>

          <td style="border-bottom:1px solid #f3f4f6;padding:10px;">
            <?php $__currentLoopData = [$p1,$p2,$p3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if(!empty($pp)): ?>
                <?php $wa = \App\Support\WhatsApp::url($pp); ?>
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:4px;">
                  <span><?php echo e($pp); ?></span>
                  <?php if($wa): ?>
                    <a href="<?php echo e($wa); ?>" target="_blank"
                       style="padding:4px 8px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;font-size:12px;">WhatsApp</a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(empty($p1) && empty($p2) && empty($p3)): ?>
              -
            <?php endif; ?>
          </td>

          <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;"><?php echo e(number_format((float)($r->total_sales ?? 0), 2)); ?></td>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;text-align:right;">
            <span style="font-weight:800;<?php echo e((float)($r->total_udhar ?? 0) > 0 ? 'color:#b91c1c;' : 'color:#16a34a;'); ?>">
              <?php echo e(number_format((float)($r->total_udhar ?? 0), 2)); ?>

            </span>
          </td>
          <td style="border-bottom:1px solid #f3f4f6;padding:10px;"><?php echo e($r->last_purchase ?? '-'); ?></td>

          <td style="border-bottom:1px solid #f3f4f6;padding:10px;white-space:nowrap;">
            <?php if(!empty($p1)): ?>
              <a href="<?php echo e(route('mt.customers.profile', $p1)); ?>"
                 style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Profile</a>
            <?php endif; ?>
            <?php if(!empty($p1) && (float)($r->total_udhar ?? 0) > 0): ?>
              <a href="<?php echo e(route('mt.udhar.show', $p1)); ?>"
                 style="padding:6px 10px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;margin-left:6px;">Udhar</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" style="padding:12px;color:#6b7280;">No results.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div style="margin-top:12px;">
    <?php echo e($rows->links()); ?>

  </div>
</div>

<script>
  function copyText(id){
    const el = document.getElementById(id);
    if (!el) return;

    const text = el.value || '';

    // Try modern clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(() => {
        toast('Copied!');
      }).catch(() => {
        fallbackCopy(el);
      });
      return;
    }

    fallbackCopy(el);
  }

  function fallbackCopy(el){
    el.focus();
    el.select();
    try {
      document.execCommand('copy');
      toast('Copied!');
    } catch (e) {
      toast('Copy failed.');
    }
    window.getSelection().removeAllRanges();
  }

  function toast(msg){
    const t = document.createElement('div');
    t.innerText = msg;
    t.style.position = 'fixed';
    t.style.bottom = '20px';
    t.style.right = '20px';
    t.style.background = '#111827';
    t.style.color = '#fff';
    t.style.padding = '10px 12px';
    t.style.borderRadius = '10px';
    t.style.fontSize = '12px';
    t.style.zIndex = 999999;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 1200);
  }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/whatsapp/index.blade.php ENDPATH**/ ?>