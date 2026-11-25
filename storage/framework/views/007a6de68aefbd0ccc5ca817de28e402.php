<?php
/** @var \App\Models\Category[]|\Illuminate\Support\Collection $navCategories */
?>
<footer class="site-footer" id="contact">
    <div class="container footer-grid">
        <div class="footer-brand">
            <h4>Danielle's Crafted Pizza</h4>
            <p>Premium ingredients, expert-grade equipment, and culinary resources designed for home chefs and thriving pizzerias.</p>
            <p class="tagline">Everything You Need for Pizza Success.</p>
        </div>
        <div>
            <h5>Products</h5>
            <ul>
                <?php $__currentLoopData = ($navCategories ?? [])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('shop', ['category' => $category->key])); ?>"><?php echo e($category->label); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div>
            <h5>Businesses</h5>
            <ul>
                <li><a href="<?php echo e(route('home')); ?>#wholesale">Wholesale Program</a></li>
                <li><a href="<?php echo e(route('starter-kit.show')); ?>">Starter Kit Builder</a></li>
                <li><a href="<?php echo e(route('shop', ['category' => 'boxes'])); ?>">Bulk Packaging</a></li>
                <li><a href="<?php echo e(route('home')); ?>#why-us">Why Danielle's</a></li>
            </ul>
        </div>
        <div>
            <h5>Contact</h5>
            <ul>
                <li>hello@daniellescraftedpizza.example</li>
                <li>+63 900 000 0000</li>
                <li>Mon to Sat, 8am to 6pm</li>
                <li><a href="<?php echo e(route('cart.index')); ?>">Your Cart</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <span>&copy; <?php echo e(date('Y')); ?> Danielle's Crafted Pizza. Student project, not a real store.</span>
            <span class="social" aria-label="Social links">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="4"/><circle cx="12" cy="12" r="3.500"/><circle cx="17" cy="7" r=".6"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 5c-1 .5-2 .8-3 .9a4 4 0 00-7 3.100A11 11 0 013 4s-4 9 5 13a12 12 0 01-6 2c9 5 19 0 19-11.500 0-.3 1-1.500 1-2.500z"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M10 8.500l5 3.500-5 3.500z"/></svg>
            </span>
        </div>
    </div>
</footer>
<?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/partials/footer.blade.php ENDPATH**/ ?>