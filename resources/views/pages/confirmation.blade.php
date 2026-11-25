@extends('layouts.app')

@section('title', 'Order Confirmed | Danielle\'s Crafted Pizza')

@section('content')
    @if (! $order)
        <main class="container">
            <div class="empty-state" style="margin:3rem 0">
                <h2>No recent order found</h2>
                <p>Place an order and your confirmation will appear here.</p>
                <a class="btn" href="{{ route('shop') }}">Go to Shop</a>
            </div>
        </main>
    @else
        @php
            $shipLabels = ['express' => 'Express Food Dispatch', 'standard' => 'Standard Delivery'];
            $paymentLabels = [
                'cod'    => 'Cash on Delivery',
                'online' => 'Online Payment',
            ];
            $firstName = $order->first_name;
            $shipLabel = $shipLabels[$order->shipping_method] ?? 'Delivery';
            $paymentLabel = $paymentLabels[$order->payment] ?? ucfirst($order->payment);

            // Status badge text + color
            $statusText = $order->statusBadge();
            $statusColor = match ($order->payment_status) {
                'paid', 'succeeded'         => '#1a8a3a',
                'pending_cod'                => '#5c616b',
                'failed', 'cancelled'        => '#dc3a1e',
                default                       => '#f5a800',
            };
            $fullAddress = $order->address . ', ' . $order->city . ' ' . $order->postal;
        @endphp
        <main class="container" id="confirmation-content">
            <div class="confirm-head">
                <div class="check-badge" style="{{ $order->isPaid() || $order->payment_status === 'pending_cod' ? '' : 'background:var(--amber);color:#fff' }}">
                    @if ($order->isPaid() || $order->payment_status === 'pending_cod') ✓ @else ! @endif
                </div>
                <h1>Thank you, {{ $firstName }}!</h1>
                <p>
                    @if ($order->isPaid())
                        Your payment was successful and your order has been received. A confirmation would be sent to <strong>{{ $order->email }}</strong>.
                    @elseif ($order->payment_status === 'pending_cod')
                        Your order has been received. Payment will be collected on delivery.
                    @else
                        Your order has been placed but payment is still pending. A confirmation would be sent to <strong>{{ $order->email }}</strong> once payment is verified.
                    @endif
                </p>
                <p class="order-ref">
                    Order No. <strong>{{ $order->ref }}</strong>
                    &nbsp;·&nbsp;
                    Payment status:
                    <strong style="color:{{ $statusColor }}">{{ $statusText }}</strong>
                </p>
            </div>

            <div class="confirm-grid">
                <div class="summary-box">
                    <h3>Items</h3>
                    @foreach ($order->items as $item)
                        <div class="summary-item">
                            <span>{{ $item->product_name }} <small>× {{ $item->qty }}</small></span>
                            <span>{{ \App\Helpers\Format::peso($item->price * $item->qty) }}</span>
                        </div>
                    @endforeach
                    <div class="summary-line"><span>Subtotal</span><span>{{ \App\Helpers\Format::peso($order->subtotal) }}</span></div>
                    <div class="summary-line"><span>{{ $shipLabel }}</span><span>{{ $order->shipping === 0 ? 'FREE' : \App\Helpers\Format::peso($order->shipping) }}</span></div>
                    <div class="summary-line total"><span>Total</span><span>{{ \App\Helpers\Format::peso($order->total) }}</span></div>

                    @if ($order->payment_method_type && $order->payment_method_type !== 'cod')
                        <div class="summary-line" style="margin-top:.5rem;font-size:.85rem;color:var(--muted)">
                            <span>Paid via</span>
                            <span>{{ ucfirst($order->payment_method_type) }}</span>
                        </div>
                    @endif
                </div>
                <div class="summary-box">
                    <h3>Delivery Details</h3>
                    <p><strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>{{ $fullAddress }}<br>{{ $order->phone }}</p>
                    <h3>Payment</h3>
                    <p>
                        {{ $paymentLabel }}
                        @if ($order->payment_method_type && $order->payment_method_type !== 'cod')
                            @php
                                $gatewayLabel = $order->payment_intent_id ? 'Xendit' : 'mock gateway';
                                $methodLabel = ucfirst($order->payment_method_type);
                                if ($order->payment_method_type === 'gcash') $methodLabel = 'GCash';
                            @endphp
                            <br><small style="color:var(--muted)">{{ $methodLabel }} via {{ $gatewayLabel }}</small>
                        @endif
                    </p>
                    @if ($order->notes)
                        <h3>Notes</h3>
                        <p>{{ $order->notes }}</p>
                    @endif
                </div>
            </div>

            @if (! $order->isPaid() && $order->payment_status !== 'pending_cod')
                <div style="text-align:center;margin:1.5rem 0">
                    <a class="btn" href="{{ route('payment.show', ['ref' => $order->ref]) }}">Complete Payment</a>
                </div>
            @endif

            <div class="center-actions">
                <a class="btn" href="{{ route('shop') }}">Continue Shopping</a>
                <a class="btn btn-outline" href="{{ route('home') }}">Back to Home</a>
            </div>
        </main>
    @endif
@endsection
