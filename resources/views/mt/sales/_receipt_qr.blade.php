@php
  $qrSize = $qrSize ?? 132;
  $receiptQrSvg = $receiptQrSvg ?? \App\Support\SaleReceipt::qrSvg($sale, $qrSize);
  $qrTitle = $qrTitle ?? 'Scan To View Bill Details';
  $qrSubtitle = $qrSubtitle ?? ('Invoice #' . $sale->id . ' - Direct Data');
  $wrapperClass = trim('receipt-qr-card ' . ($wrapperClass ?? ''));
  $imageClass = trim('receipt-qr-image ' . ($imageClass ?? ''));
@endphp

<div class="{{ $wrapperClass }}" style="border:1px solid #e2e8f0;border-radius:14px;padding:14px;background:#fff;text-align:center;">
  <div style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Quick Retrieve</div>
  <div class="{{ $imageClass }}" style="margin:12px auto 8px;width:{{ $qrSize }}px;line-height:0;">{!! $receiptQrSvg !!}</div>
  <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ $qrTitle }}</div>
  <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $qrSubtitle }}</div>
</div>
