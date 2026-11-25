<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation — {{ $order->ref }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f5f4f0;
            margin: 0;
            padding: 0;
            color: #12151f;
            line-height: 1.6;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #12151f;
            color: #fff;
            padding: 24px 28px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 700;
        }
        .header .logo {
            color: #f5a800;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .body {
            background: #fff;
            padding: 28px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .check {
            width: 56px; height: 56px;
            background: #1a8a3a;
            border-radius: 50%;
            text-align: center;
            line-height: 56px;
            color: #fff;
            font-size: 28px;
            margin: 0 auto 16px;
        }
        .thank-you {
            text-align: center;
            margin-bottom: 24px;
        }
        .thank-you h2 {
            margin: 0 0 8px;
            font-size: 20px;
            color: #12151f;
        }
        .thank-you p {
            margin: 0;
            color: #5c616b;
            font-size: 14px;
        }
        .order-ref-box {
            background: #f8f7f3;
            border-radius: 6px;
            padding: 14px 18px;
            text-align: center;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .order-ref-box .label {
            color: #5c616b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .order-ref-box .value {
            font-weight: 700;
            font-size: 16px;
            color: #12151f;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-left: 8px;
        }
        .status-paid { background: #e6f4ea; color: #1a8a3a; }
        .status-pending { background: #fff8e6; color: #8a6d00; }
        .status-failed { background: #fdecea; color: #dc3a1e; }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #12151f;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 2px solid #e9e5dc;
            padding-bottom: 8px;
            margin: 0 0 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            text-align: left;
            font-size: 12px;
            color: #5c616b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 0;
            border-bottom: 1px solid #e9e5dc;
        }
        .items-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f5f4f0;
            font-size: 14px;
        }
        .items-table .qty { color: #5c616b; font-size: 13px; }
        .items-table .price { text-align: right; font-weight: 600; }
        .totals {
            margin-left: auto;
            width: 220px;
            margin-bottom: 24px;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }
        .totals .row.total {
            border-top: 2px solid #12151f;
            margin-top: 6px;
            padding-top: 10px;
            font-weight: 700;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .info-block .label {
            font-size: 12px;
            color: #5c616b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }
        .info-block .value {
            font-size: 14px;
            color: #12151f;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #5c616b;
        }
        .footer a {
            color: #dc3a1e;
            text-decoration: none;
        }
        .note {
            background: #fff8e6;
            border-left: 3px solid #f5a800;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 13px;
            color: #5c4400;
            margin-bottom: 20px;
        }
        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; }
            .totals { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">DANIELLE'S CRAFTED PIZZA</div>
            <h1>Order Confirmation</h1>
        </div>

        <div class="body">
            <div class="check">✓</div>

            <div class="thank-you">
                <h2>Thank you for your order, {{ $firstName }}!</h2>
                <p>We've received your order and are getting it ready. A copy of this confirmation has been sent to <strong>{{ $order->email }}</strong>.</p>
            </div>

            <div class="order-ref-box">
                <div class="label">Order Number</div>
                <div class="value">{{ $order->ref }}</div>
                <div style="margin-top:8px">
                    Payment status:
                    <span class="status-badge {{ $statusLabel === 'Paid' ? 'status-paid' : ($statusLabel === 'Failed' ? 'status-failed' : 'status-pending') }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            @if ($statusLabel !== 'Paid' && $statusLabel !== 'Cash on Delivery')
                <div class="note">
                    <strong>Payment pending.</strong> Your order is confirmed but payment is still being processed. You'll receive a follow-up email once payment is verified.
                </div>
            @endif

            <h3 class="section-title">Items Ordered</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="qty" style="text-align:center">{{ $item->qty }}</td>
                            <td class="price">₱{{ number_format($item->price * $item->qty) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="row">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($subtotal) }}</span>
                </div>
                <div class="row">
                    <span>{{ $shippingLabel }}</span>
                    <span>{{ $shipping == 0 ? 'FREE' : '₱' . number_format($shipping) }}</span>
                </div>
                <div class="row total">
                    <span>Total</span>
                    <span>₱{{ number_format($total) }}</span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-block">
                    <div class="label">Delivery Address</div>
                    <div class="value">
                        <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                        {{ $order->address }}<br>
                        {{ $order->city }}, {{ $order->postal }}<br>
                        {{ $order->phone }}
                    </div>
                </div>
                <div class="info-block">
                    <div class="label">Payment Method</div>
                    <div class="value">{{ $paymentLabel }}</div>
                    @if ($order->notes)
                        <div class="label" style="margin-top:12px">Order Notes</div>
                        <div class="value">{{ $order->notes }}</div>
                    @endif
                </div>
            </div>

            @if ($statusLabel === 'Cash on Delivery')
                <div class="note">
                    <strong>Cash on Delivery.</strong> Please prepare <strong>₱{{ number_format($total) }}</strong> in cash for our delivery rider. Exact amount is appreciated.
                </div>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated message from Danielle's Crafted Pizza.</p>
            <p>Questions? Reply to this email or contact us at hello@daniellescraftedpizza.example</p>
            <p style="margin-top:12px; font-size:11px; color:#999;">
                &copy; {{ date('Y') }} Danielle's Crafted Pizza · Student project · Not a real store
            </p>
        </div>
    </div>
</body>
</html>
