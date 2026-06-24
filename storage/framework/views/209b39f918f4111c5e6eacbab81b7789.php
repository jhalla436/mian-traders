<?php
  $qrSize = $qrSize ?? 132;
  $receiptQrSvg = $receiptQrSvg ?? \App\Support\SaleReceipt::qrSvg($sale, $qrSize);
  $qrTitle = $qrTitle ?? 'Scan To View Bill Details';
  $qrSubtitle = $qrSubtitle ?? ('Invoice #' . $sale->id . ' - Direct Data');
  $wrapperClass = trim('receipt-qr-card ' . ($wrapperClass ?? ''));
  $imageClass = trim('receipt-qr-image ' . ($imageClass ?? ''));
?>

<div class="<?php echo e($wrapperClass); ?>" style="border:1px solid #e2e8f0;border-radius:14px;padding:14px;background:#fff;text-align:center;">
  <div style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Quick Retrieve</div>
  <div class="<?php echo e($imageClass); ?>" style="margin:12px auto 8px;width:<?php echo e($qrSize); ?>px;line-height:0;"><?php echo $receiptQrSvg; ?></div>
  <div style="font-size:12px;font-weight:700;color:#0f172a;"><?php echo e($qrTitle); ?></div>
  <div style="font-size:11px;color:#64748b;margin-top:4px;"><?php echo e($qrSubtitle); ?></div>
</div>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/sales/_receipt_qr.blade.php ENDPATH**/ ?>