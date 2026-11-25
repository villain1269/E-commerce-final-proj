<header class="site-header">
    <div class="container nav-wrap nav-checkout">
        <a href="{{ route('home') }}" class="logo" aria-label="Danielle's Crafted Pizza home">
            <span class="logo-mark">D</span>
            <span class="logo-text"><b>DANIELLE'S</b><em>CRAFTED PIZZA</em></span>
        </a>
        <span class="secure-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
            Secure Checkout
        </span>
        <a href="{{ route('cart.index') }}" class="return-link">Return to Cart</a>
    </div>
</header>
<div class="steps-bar"><div class="container">
    <span class="on">1. Shipping Info</span><span class="sep">›</span>
    <span>2. Shipping Method</span><span class="sep">›</span>
    <span>3. Payment Details</span>
</div></div>
