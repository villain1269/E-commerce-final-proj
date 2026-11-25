<?php $__env->startSection('title', 'Order Confirmed | Danielle\'s Crafted Pizza'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $order): ?>
        <main class="container">
            <div class="empty-state" style="margin:3rem 0">
                <h2>No recent order found</h2>
                <p>Place an order and your confirmation will appear here.</p>
                <a class="btn" href="<?php echo e(route('shop')); ?>">Go to Shop</a>
            </div>
        </main>
    <?php else: ?>
        <?php
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
        ?>
        <main class="container" id="confirmation-content">
            <div class="confirm-head">
                <div class="check-badge" style="<?php echo e($order->isPaid() || $order->payment_status === 'pending_cod' ? '' : 'background:var(--amber);color:#fff'); ?>">
                    <?php if($order->isPaid() || $order->payment_status === 'pending_cod'): ?> ✓ <?php else: ?> ! <?php endif; ?>
                </div>
                <h1>Thank you, <?php echo e($firstName); ?>!</h1>
                <p>
                    <?php if($order->isPaid()): ?>
                        Your payment was successful and your order has been received. A confirmation would be sent to <strong><?php echo e($order->email); ?></strong>.
                    <?php elseif($order->payment_status === 'pending_cod'): ?>
                        Your order has been received. Payment will be collected on delivery.
                    <?php else: ?>
                        Your order has been placed but payment is still pending. A confirmation would be sent to <strong><?php echo e($order->email); ?></strong> once payment is verified.
                    <?php endif; ?>
                </p>
                <p class="order-ref">
                    Order No. <strong><?php echo e($order->ref); ?></strong>
                    &nbsp;·&nbsp;
                    Payment status:
                    <strong style="color:<?php echo e($statusColor); ?>"><?php echo e($statusText); ?></strong>
                </p>
            </div>

            <div class="confirm-grid">
                <div class="summary-box">
                    <h3>Items</h3>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="summary-item">
                            <span><?php echo e($item->product_name); ?> <small>× <?php echo e($item->qty); ?></small></span>
                            <span><?php echo e(\App\Helpers\Format::peso($item->price * $item->qty)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-line"><span>Subtotal</span><span><?php echo e(\App\Helpers\Format::peso($order->subtotal)); ?></span></div>
                    <div class="summary-line"><span><?php echo e($shipLabel); ?></span><span><?php echo e($order->shipping === 0 ? 'FREE' : \App\Helpers\Format::peso($order->shipping)); ?></span></div>
                    <div class="summary-line total"><span>Total</span><span><?php echo e(\App\Helpers\Format::peso($order->total)); ?></span></div>

                    <?php if($order->payment_method_type && $order->payment_method_type !== 'cod'): ?>
                        <div class="summary-line" style="margin-top:.5rem;font-size:.85rem;color:var(--muted)">
                            <span>Paid via</span>
                            <span><?php echo e(ucfirst($order->payment_method_type)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="summary-box">
                    <h3>Delivery Details</h3>
                    <p><strong><?php echo e($order->first_name); ?> <?php echo e($order->last_name); ?></strong><br><?php echo e($fullAddress); ?><br><?php echo e($order->phone); ?></p>
                    <h3>Payment</h3>
                    <p>
                        <?php echo e($paymentLabel); ?>

                        <?php if($order->payment_method_type && $order->payment_method_type !== 'cod'): ?>
                            <?php
                                $gatewayLabel = $order->payment_intent_id ? 'Xendit' : 'mock gateway';
                                $methodLabel = ucfirst($order->payment_method_type);
                                if ($order->payment_method_type === 'gcash') $methodLabel = 'GCash';
                            ?>
                            <br><small style="color:var(--muted)"><?php echo e($methodLabel); ?> via <?php echo e($gatewayLabel); ?></small>
                        <?php endif; ?>
                    </p>
                    <?php if($order->notes): ?>
                        <h3>Notes</h3>
                        <p><?php echo e($order->notes); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(! $order->isPaid() && $order->payment_status !== 'pending_cod'): ?>
                <div style="text-align:center;margin:1.5rem 0">
                    <a class="btn" href="<?php echo e(route('payment.show', ['ref' => $order->ref])); ?>">Complete Payment</a>
                </div>
            <?php endif; ?>

            <div class="center-actions">
                <a class="btn" href="<?php echo e(route('shop')); ?>">Continue Shopping</a>
                <a class="btn btn-outline" href="<?php echo e(route('home')); ?>">Back to Home</a>
            </div>
        </main>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/confirmation.blade.php ENDPATH**/ ?>