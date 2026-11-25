<?php
/** @var \App\Models\Category[]|\Illuminate\Support\Collection $navCategories */
/** @var int $cartCount */
?>
<div class="announcement">Free delivery on orders over <b>₱3,000</b> &nbsp;|&nbsp; Wholesale pricing for pizza sellers</div>
<header class="site-header">
    <div class="container nav-wrap">
        <button class="menu-toggle" id="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
        <a href="<?php echo e(route('home')); ?>" class="logo" aria-label="Danielle's Crafted Pizza home">
            <span class="logo-mark">D</span>
            <span class="logo-text"><b>DANIELLE'S</b><em>CRAFTED PIZZA</em></span>
        </a>
        <nav class="main-nav" id="main-nav" aria-label="Main">
            <ul>
                <li class="has-sub">
                    <a href="<?php echo e(route('shop')); ?>">Shop</a>
                    <ul class="submenu">
                        <li><a href="<?php echo e(route('shop')); ?>">All Pizza Supplies</a></li>
                        <?php $__currentLoopData = $navCategories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e(route('shop', ['category' => $category->key])); ?>"><?php echo e($category->label); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
                <li><a href="<?php echo e(route('starter-kit.show')); ?>">Starter Kit</a></li>
                <li><a href="<?php echo e(route('home')); ?>#wholesale">Wholesale</a></li>
                <li><a href="<?php echo e(route('home')); ?>#why-us">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <button class="icon-btn" id="search-toggle" aria-label="Search products" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.500-3.500"/></svg>
            </button>
            <a href="<?php echo e(route('cart.index')); ?>" class="cart-link" aria-label="Cart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.500 11h10l2-8H6.500"/><circle cx="9" cy="19.500" r="1.200"/><circle cx="17" cy="19.500" r="1.200"/></svg>
                <span>Cart (<span class="cart-count"><?php echo e($cartCount ?? 0); ?></span>)</span>
            </a>
        </div>
    </div>
    <div class="search-bar hidden" id="search-bar">
        <div class="container">
            <form id="search-form" role="search" method="get" action="<?php echo e(route('shop')); ?>">
                <input type="text" id="search-input" name="q" placeholder="Search dough, boxes, paddles..." aria-label="Search products">
                <button class="btn" type="submit">Search</button>
            </form>
        </div>
    </div>
</header>
<?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/partials/header.blade.php ENDPATH**/ ?>