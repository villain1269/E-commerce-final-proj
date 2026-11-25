<?php $__env->startSection('content'); ?>
    <div class="container checkout-layout">
        <form id="checkout-form" method="post" action="<?php echo e(route('checkout.place')); ?>" novalidate>
            <?php echo csrf_field(); ?>

            <div class="form-card">
                <h3>1. Delivery Shipping Information</h3>
                <div class="form-row">
                    <div class="field <?php echo e($errors->has('firstName') ? 'invalid' : ''); ?>">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" value="<?php echo e(old('firstName')); ?>" autocomplete="given-name">
                        <?php $__errorArgs = ['firstName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field <?php echo e($errors->has('lastName') ? 'invalid' : ''); ?>">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" value="<?php echo e(old('lastName')); ?>" autocomplete="family-name">
                        <?php $__errorArgs = ['lastName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="field <?php echo e($errors->has('email') ? 'invalid' : ''); ?>">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="email">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field <?php echo e($errors->has('address') ? 'invalid' : ''); ?>">
                    <label for="address">Street Address *</label>
                    <input type="text" id="address" name="address" value="<?php echo e(old('address')); ?>" autocomplete="street-address">
                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-row">
                    <div class="field <?php echo e($errors->has('city') ? 'invalid' : ''); ?>">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" value="<?php echo e(old('city')); ?>" autocomplete="address-level2">
                        <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field <?php echo e($errors->has('postal') ? 'invalid' : ''); ?>">
                        <label for="postal">Postal Code *</label>
                        <input type="text" id="postal" name="postal" value="<?php echo e(old('postal')); ?>" inputmode="numeric" maxlength="4" autocomplete="postal-code">
                        <?php $__errorArgs = ['postal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="field <?php echo e($errors->has('phone') ? 'invalid' : ''); ?>">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="09171234567" autocomplete="tel">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="error-msg"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label for="notes">Order Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Delivery instructions, preferred time, etc."><?php echo e(old('notes')); ?></textarea>
                </div>
            </div>

            <div class="form-card">
                <h3>2. Shipping Method</h3>
                <?php $selectedShip = old('shipping', 'express'); ?>
                <label class="ship-option">
                    <input type="radio" name="shipping" value="express" <?php echo e($selectedShip === 'express' ? 'checked' : ''); ?>>
                    <span class="opt-text">Express Food Dispatch (Guaranteed Fresh)<small>Delivered in insulated, temperature-protected packs.</small></span>
                    <span class="opt-price" id="price-express"><?php echo e($express === 0 ? 'FREE' : \App\Helpers\Format::peso($express)); ?></span>
                </label>
                <label class="ship-option">
                    <input type="radio" name="shipping" value="standard" <?php echo e($selectedShip === 'standard' ? 'checked' : ''); ?>>
                    <span class="opt-text">Standard Delivery<small>Free on orders over ₱3,000. Best for packaging and equipment.</small></span>
                    <span class="opt-price" id="price-standard"><?php echo e($standard === 0 ? 'FREE' : \App\Helpers\Format::peso($standard)); ?></span>
                </label>
            </div>

            <div class="form-card">
                <h3>3. Payment Method</h3>
                <?php $selectedPay = old('payment'); ?>
                <label class="pay-option">
                    <input type="radio" name="payment" value="cod" <?php echo e($selectedPay === 'cod' ? 'checked' : ''); ?>>
                    <span class="opt-text">Cash on Delivery<small>Pay cash when your order arrives.</small></span>
                </label>
                <label class="pay-option">
                    <input type="radio" name="payment" value="online" <?php echo e($selectedPay === 'online' ? 'checked' : ''); ?>>
                    <span class="opt-text">Pay Online (GCash, Maya, Card, Bank, OTC)<small>Redirects to a realistic mock payment screen after you place your order. Sandbox mode — no real money is charged.</small></span>
                </label>
                <?php $__errorArgs = ['payment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="inline-error" id="payment-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="note">This is a school project. No real money is charged. Online payments run in a built-in mock gateway that simulates GCash, Maya, cards, bank transfers, and OTC cash payments.</p>
            </div>

            <label class="check-line <?php echo e($errors->has('terms') ? 'invalid' : ''); ?>">
                <input type="checkbox" name="terms" <?php echo e(old('terms') ? 'checked' : ''); ?>>
                <span>I confirm my order details are correct and I agree to the order terms.</span>
            </label>
            <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="inline-error" id="terms-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </form>

        <div class="checkout-summary-wrap">
            <aside class="summary-box" id="checkout-summary">
                <h3>Review Your Order</h3>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $p = $i['product']; ?>
                    <div class="co-item">
                        <div class="cart-thumb ph">[IMG]</div>
                        <div class="co-name"><?php echo e($p->name); ?><small>Qty: <?php echo e($i['qty']); ?></small></div>
                        <div class="co-price"><?php echo e($p->peso($i['qty'])); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="co-divider"></div>
                <div class="summary-line"><span>Subtotal</span><span><?php echo e(\App\Helpers\Format::peso($subtotal)); ?></span></div>
                <div class="summary-line" id="ship-line">
                    <span>Express Food Dispatch</span><span><?php echo e($express === 0 ? 'FREE' : \App\Helpers\Format::peso($express)); ?></span>
                </div>
                <div class="summary-line total"><span>Final Total</span><span id="final-total"><?php echo e(\App\Helpers\Format::peso($subtotal + $express)); ?></span></div>
                <button type="submit" form="checkout-form" class="btn btn-block">Place Order • <span id="place-order-total"><?php echo e(\App\Helpers\Format::peso($subtotal + $express)); ?></span></button>
            </aside>

            <aside class="summary-box secure-card">
                <h4><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"/></svg> Demo Checkout</h4>
                <p>Nothing is charged. Your order is saved to the database once you click Place Order.</p>
            </aside>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var form = document.getElementById('checkout-form');
    if (!form) return;

    var expressCost = <?php echo e($express); ?>;
    var standardCost = <?php echo e($standard); ?>;
    var subtotal = <?php echo e($subtotal); ?>;

    var labels = {
        express:  'Express Food Dispatch',
        standard: 'Standard Delivery'
    };

    function fmt(n) { return '₱' + n.toLocaleString('en-PH'); }

    function updateSummary() {
        var method = 'express';
        form.querySelectorAll('input[name=shipping]').forEach(function (r) {
            if (r.checked) method = r.value;
        });
        var ship = method === 'express' ? expressCost : standardCost;
        var total = subtotal + ship;

        var shipLine = document.getElementById('ship-line');
        shipLine.querySelector('span:first-child').textContent = labels[method];
        shipLine.querySelector('span:last-child').textContent  = ship === 0 ? 'FREE' : fmt(ship);

        document.getElementById('final-total').textContent = fmt(total);
        document.getElementById('place-order-total').textContent = fmt(total);
    }

    form.querySelectorAll('input[name=shipping]').forEach(function (r) {
        r.addEventListener('change', updateSummary);
    });

    updateSummary();
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.checkout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/checkout.blade.php ENDPATH**/ ?>