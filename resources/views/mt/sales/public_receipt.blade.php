<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        body {
            margin: 0;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            font-family: Arial, sans-serif;
            color: #0f172a;
        }
        .wrap {
            max-width: 840px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }
        .card {
            background: #fff;
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .hero {
            padding: 22px 24px;
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            color: #fff;
        }
        .hero h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }
        .hero p {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
        }
        .section {
            padding: 18px 24px;
            border-top: 1px solid #eef2f7;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .meta-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 14px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            font-size: 14px;
        }
        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
        }
        .right {
            text-align: right;
        }
        .totals {
            display: grid;
            gap: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }
        .total-row strong {
            font-size: 16px;
        }
        .balance {
            color: #b91c1c;
        }
        .actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            appearance: none;
            border: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }
        .note {
            margin-top: 12px;
            padding: 12px 14px;
            background: #f8fafc;
            border-radius: 12px;
            font-size: 13px;
            color: #334155;
        }
        @media (max-width: 640px) {
            .grid {
                grid-template-columns: 1fr;
            }
            .hero, .section {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
        @media print {
            body {
                background: #fff;
            }
            .wrap {
                max-width: none;
                padding: 0;
            }
            .card {
                box-shadow: none;
                border-radius: 0;
                border: none;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $displayReceivedAmount = (float)($sale->received_amount ?? 0);
        $grossAmount = (float)collect($sale->items ?? [])->sum(fn ($it) => (float)($it->base_line_total ?? $it->line_total ?? 0));
        $subtotalAmount = (float)($sale->subtotal_amount ?? collect($sale->items ?? [])->sum(fn ($it) => (float)($it->line_total ?? 0)));
        $itemDiscountAmount = (float)($sale->item_discount_total ?? max($grossAmount - $subtotalAmount, 0));
        $overallDiscountAmount = (float)($sale->overall_discount_amount ?? max($subtotalAmount - (float)($sale->total_amount ?? 0), 0));
        if ($displayReceivedAmount <= 0 && (float)($sale->paid_amount ?? 0) > 0) {
            $displayReceivedAmount = (float)$sale->paid_amount;
        }
    @endphp
    <div class="wrap">
        <div class="card">
            <div class="hero">
                <h1>{{ $sale->shop?->name ?? 'Receipt' }}</h1>
                <p>Receipt #{{ $sale->id }} • {{ $sale->created_at?->format('d M Y, h:i A') }}</p>
            </div>

            <div class="section grid">
                <div>
                    <div class="meta-label">Customer</div>
                    <div class="meta-value"><strong>Name:</strong> {{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                    <div class="meta-value"><strong>Mobile:</strong> {{ $sale->customer_phone ?? '-' }}</div>
                    <div class="meta-value"><strong>Address:</strong> {{ $sale->customer_address ?? '-' }}</div>
                </div>
                <div>
                    <div class="meta-label">Shop</div>
                    <div class="meta-value">{{ $sale->shop?->owner_name ?? '-' }}</div>
                    <div class="meta-value">{{ $sale->shop?->phone ?? '-' }}</div>
                    <div class="meta-value">{{ $sale->shop?->address ?? '-' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="meta-label">Items</div>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="right">Qty</th>
                            <th class="right">Rate</th>
                            <th class="right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($sale->items as $it)
                        <tr>
                            <td>
                                {{ $it->product_name }}
                                @if((float)($it->discount_amount ?? 0) > 0)
                                    <div class="muted" style="margin-top:4px;">
                                        Base {{ number_format((float)($it->base_price ?? $it->price), 2) }}
                                        | Disc {{ number_format((float)($it->discount_percent ?? 0), 2) }}%
                                        | Save Rs {{ number_format((float)($it->discount_amount ?? 0), 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="right">{{ number_format((float) $it->qty, 2) }}</td>
                            <td class="right">{{ number_format((float) $it->price, 2) }}</td>
                            <td class="right">{{ number_format((float) $it->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="section">
                <div class="meta-label">Summary</div>
                <div class="totals">
                    <div class="total-row"><span>Gross Total</span><strong>Rs {{ number_format($grossAmount, 2) }}</strong></div>
                    @if($itemDiscountAmount > 0.009)
                        <div class="total-row"><span>Product Discount</span><strong>Rs {{ number_format($itemDiscountAmount, 2) }}</strong></div>
                    @endif
                    @if($overallDiscountAmount > 0.009)
                        <div class="total-row"><span>Overall Discount</span><strong>Rs {{ number_format($overallDiscountAmount, 2) }}</strong></div>
                    @endif
                    <div class="total-row"><span>Total</span><strong>Rs {{ number_format((float) $sale->total_amount, 2) }}</strong></div>
                    <div class="total-row"><span>Paid</span><strong>Rs {{ number_format((float) $sale->paid_amount, 2) }}</strong></div>
                    <div class="total-row"><span>Received</span><strong>Rs {{ number_format($displayReceivedAmount, 2) }}</strong></div>
                    @if((float)($sale->change_returned ?? 0) > 0)
                        <div class="total-row"><span>Change Returned</span><strong>Rs {{ number_format((float) $sale->change_returned, 2) }}</strong></div>
                    @endif
                    <div class="total-row"><span>Balance</span><strong class="balance">Rs {{ number_format((float) $sale->balance_amount, 2) }}</strong></div>
                </div>

                <div style="margin-top:14px;">
                    <div class="meta-label">Payment Details</div>
                    @include('mt.sales._payment_details', [
                        'sale' => $sale,
                        'lineStyle' => 'font-size:13px;color:#334155;line-height:1.6;margin-top:5px;'
                    ])
                </div>

                @if(!empty($sale->note))
                    <div class="note"><strong>Note:</strong> {{ $sale->note }}</div>
                @endif

                <div class="actions">
                    <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
