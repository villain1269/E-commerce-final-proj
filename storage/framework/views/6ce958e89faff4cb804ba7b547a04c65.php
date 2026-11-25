<?php $__env->startSection('title', 'Secure Checkout | Danielle\'s Crafted Pizza'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container" style="padding:3rem 0 4rem">
        <div class="empty-state">
            <h2>Your cart is empty</h2>
            <p>Add something before checking out.</p>
            <a class="btn" href="<?php echo e(route('shop')); ?>">Go to Shop</a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/checkout-empty.blade.php ENDPATH**/ ?>