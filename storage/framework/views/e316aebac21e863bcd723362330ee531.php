<?php $__env->startSection('title', 'Your Shopping Cart | Danielle\'s Crafted Pizza'); ?>

<?php $__env->startSection('content'); ?>
    <main class="container">
        <div class="cart-title"><h1>Your Shopping Cart</h1></div>

        <?php if($items->isEmpty()): ?>
            <div id="cart-content">
                <div class="empty-state">
                    <h2>Your cart is empty</h2>
                    <p>Add some dough, boxes or equipment to get started.</p>
                    <a class="btn" href="<?php echo e(route('shop')); ?>">Start Shopping</a>
                    <a class="btn btn-outline" href="<?php echo e(route('starter-kit.show')); ?>">Try the Starter Kit Builder</a>
                </div>
            </div>
        <?php else: ?>
            <div id="cart-content">
                <div class="cart-layout">
                    <div class="cart-table">
                        <div class="cart-head">
                            <span>Product</span><span>Price</span><span>Quantity</span><span>Total</span><span></span>
                        </div>

                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $p = $i['product']; ?>
                            <div class="cart-row" data-id="<?php echo e($p->public_id); ?>">
                                <div class="cart-item">
                                    <div class="cart-thumb ph">[IMG]</div>
                                    <div>
                                        <a href="<?php echo e(route('product.show', ['id' => $p->public_id])); ?>"><strong><?php echo e($p->name); ?></strong></a>
                                        <small>SKU: <?php echo e($p->sku); ?></small>
                                    </div>
                                </div>
                                <span class="cart-price"><?php echo e($p->peso()); ?></span>

                                <div class="qty-control">
                                    <form method="post" action="<?php echo e(route('cart.dec', ['id' => $p->public_id])); ?>" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" aria-label="Decrease quantity">−</button>
                                    </form>
                                    <form method="post" action="<?php echo e(route('cart.qty', ['id' => $p->public_id])); ?>" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="number" min="1" max="99" value="<?php echo e($i['qty']); ?>" name="qty" aria-label="Quantity" onchange="this.form.submit()">
                                    </form>
                                    <form method="post" action="<?php echo e(route('cart.inc', ['id' => $p->public_id])); ?>" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" aria-label="Increase quantity">+</button>
                                    </form>
                                </div>

                                <strong class="line-total"><?php echo e($p->peso($i['qty'])); ?></strong>

                                <form method="post" action="<?php echo e(route('cart.remove', ['id' => $p->public_id])); ?>" style="display:inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="remove-btn" aria-label="Remove <?php echo e($p->name); ?>">✕</button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <aside class="summary-box">
                        <h3>Order Summary</h3>
                        <div class="summary-line"><span>Subtotal</span><span><?php echo e(\App\Helpers\Format::peso($subtotal)); ?></span></div>
                        <div class="summary-line"><span>Standard Delivery</span><span class="<?php echo e($shipping === 0 ? '' : 'red'); ?>"><?php echo e($shipping === 0 ? 'FREE' : \App\Helpers\Format::peso($shipping)); ?></span></div>
                        <div class="summary-line total"><span>Total</span><span><?php echo e(\App\Helpers\Format::peso($total)); ?></span></div>

                        <?php if($toFree > 0): ?>
                            <p class="note">Add <?php echo e(\App\Helpers\Format::peso($toFree)); ?> more for free standard delivery. Express option at checkout.</p>
                        <?php else: ?>
                            <p class="note ok">You qualify for free standard delivery.</p>
                        <?php endif; ?>

                        <a class="btn btn-block" href="<?php echo e(route('checkout.index')); ?>">Proceed to Secure Checkout</a>
                        <a class="link-center" href="<?php echo e(route('shop')); ?>">— or — Continue Shopping</a>

                        <form method="post" action="<?php echo e(route('cart.clear')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="link-center" style="background:none;border:none;color:inherit;cursor:pointer;text-decoration:underline;font-size:.9rem;margin-top:.5rem">
                                Clear cart
                            </button>
                        </form>
                    </aside>
                </div>
            </div>
        <?php endif; ?>

        <?php if(! $upsells->isEmpty()): ?>
            <section class="upsell">
                <h2>Forgot Any Pizza Essentials?</h2>
                <div class="product-grid">
                    <?php $__currentLoopData = $upsells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.product-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/cart.blade.php ENDPATH**/ ?>