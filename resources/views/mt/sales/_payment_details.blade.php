@php
  $payments = collect($sale->payments ?? []);
  $lineStyle = $lineStyle ?? 'font-size:12px;color:#334155;line-height:1.5;margin-top:4px;';
  $receivedAmount = (float)($sale->received_amount ?? 0);
  if ($receivedAmount <= 0 && (float)($sale->paid_amount ?? 0) > 0) {
      $receivedAmount = (float)$sale->paid_amount;
  }
  $changeReturned = (float)($sale->change_returned ?? 0);
  $primaryMethod = \App\Support\PaymentMethod::normalize($payments->first()->method ?? null);
@endphp

@if($receivedAmount > 0)
  <div style="{{ $lineStyle }}">
    @if($primaryMethod === \App\Support\PaymentMethod::HARD_CASH)
      Cash Received From Customer: Rs {{ number_format($receivedAmount, 2) }}
    @else
      Amount Transferred: Rs {{ number_format($receivedAmount, 2) }}
    @endif
  </div>
@endif

@if($changeReturned > 0)
  <div style="{{ $lineStyle }}">Change Returned To Customer: Rs {{ number_format($changeReturned, 2) }}</div>
@endif

@if($payments->isNotEmpty())
  @foreach($payments as $pay)
    <div style="{{ $lineStyle }}">
      {{ \App\Support\PaymentMethod::label($pay->method ?? null) }}: Rs {{ number_format((float)($pay->amount ?? 0), 2) }}
      @if(!empty($pay->note))
        | {{ $pay->note }}
      @endif
    </div>
  @endforeach
@elseif((float)($sale->paid_amount ?? 0) > 0)
  <div style="{{ $lineStyle }}">
    Payment Method Not Recorded: Rs {{ number_format((float)($sale->paid_amount ?? 0), 2) }}
  </div>
@else
  <div style="{{ $lineStyle }}">No payment received yet (Udhar / Unpaid).</div>
@endif
