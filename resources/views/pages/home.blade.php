@extends('layouts.app')

@section('title', "Danielle's Crafted Pizza | Dough, Boxes & Supplies for Pizza Sellers")

@section('content')
    <!-- Hero -->
    <section class="hero">
        <div class="hero-copy">
            <span class="tag-label">Crafted Quality</span>
            <h1>Everything You Need For Pizza Success</h1>
            <p class="lead">From cold-fermented artisan dough to custom business packaging and pro paddles. Made for home pizzaiolos and fast-growing food businesses.</p>
            <div class="hero-actions">
                <a href="{{ route('shop') }}" class="btn">Shop Dough &amp; Bases</a>
                <a href="#wholesale" class="btn btn-outline-light">Wholesale Program</a>
            </div>
        </div>
        <div class="hero-image ph ph-dark">
    		<img src="{{ asset('images/hero-banner.jpg') }}" alt="Pizza dough and 			pizza-making supplies">
	</div>
    </section>

    <!-- Categories -->
    <section class="section" id="categories">
        <div class="container">
            <div class="section-head">
                <h2>Explore Our Specialist Categories</h2>
                <p>Professional-grade ingredients and gear cataloged for precision browsing.</p>
            </div>
            <div class="category-grid">
                @foreach ($categories as $category)
                    <a class="category-card" href="{{ route('shop', ['category' => $category->key]) }}">
                        <div class="cat-icon">@if ($category->key === 'dough')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 14c-1-4 2-8 7-8s8 3 7 8c-.5 3-3.500 4-7 4s-6.500-1-7-4z"/><circle cx="10" cy="12" r=".6"/><circle cx="14" cy="13" r=".6"/></svg>
                        @elseif ($category->key === 'bases')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.500"/></svg>
                        @elseif ($category->key === 'boxes')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/></svg>
                        @elseif ($category->key === 'equipment')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="6" cy="18" r="2.500"/><circle cx="18" cy="6" r="2.500"/><path d="M8 16l8-8"/></svg>
                        @elseif ($category->key === 'ready')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 21L4 6c5-3 11-3 16 0l-8 15z"/><circle cx="11" cy="9" r="1"/><circle cx="13.500" cy="13" r="1"/></svg>
                        @endif</div>
                        <h3>{{ $category->label }}</h3>
                        <span>{{ $category->products()->count() }} Items</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured products -->
    <section class="section" id="featured" style="padding-top:1rem">
        <div class="container">
            <div class="section-head-row">
                <div class="section-head">
                    <h2>Highly Recommended Products</h2>
                    <p>The essential gear and foundational ingredients used daily by top chefs.</p>
                </div>
                <div class="arrows">
                    <button class="arrow-btn" id="feat-prev" aria-label="Previous products">←</button>
                    <button class="arrow-btn red" id="feat-next" aria-label="Next products">→</button>
                </div>
            </div>
            <div class="carousel-track" id="featured-products">
                @foreach ($featured as $product)
                    @include('partials.product-card')
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why trust us -->
    <section class="section section-gray" id="why-us">
        <div class="container">
            <div class="section-head center"><h2>Why Food Enthusiasts &amp; Pizzerias Trust Danielle's</h2></div>
            <div class="why-grid">
                <div class="why-card">
                    <div class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 3l2.700 5.600 6.100.8-4.500 4.200 1.100 6.100L12 16.800 6.600 19.700l1.100-6.100L3.200 9.400l6.100-.8z"/></svg></div>
                    <h3>Cold-Fermented &amp; Handcrafted</h3>
                    <p>Our dough stands out with a 24-hour cold fermentation, so every ball stretches easily and bakes up airy.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.500"/><circle cx="17" cy="18" r="1.500"/></svg></div>
                    <h3>Business-Ready Packs</h3>
                    <p>Seller-friendly packs of 10 and 50 keep your cost per pizza low, with boxes that keep pies crisp on delivery.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="9" r="5"/><path d="M9 13.500L8 21l4-2 4 2-1-7.500"/></svg></div>
                    <h3>Culinary Professionalism</h3>
                    <p>Sturdy paddles, sharp slicers and serving plates chosen for stalls, shops and busy home kitchens.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Starter Kit Builder (preview/CTA — full builder lives on /starter-kit) -->
    <section class="section" id="starter-kit">
        <div class="container kit-layout">
            <div class="kit-intro">
                <span class="tag-label">Starter Kit Builder</span>
                <h2>Start your pizza business in one click</h2>
                <p>Tell us your business type and budget. We'll recommend a starter package of dough, boxes and equipment with an estimated total.</p>
                <a href="{{ route('starter-kit.show') }}" class="btn btn-large">Build Your Kit →</a>
            </div>
            <div class="kit-result">
                <div class="kit-placeholder">
                    <h3>Your recommended kit will appear here</h3>
                    <p style="margin:0 0 1rem">Pick a business type and budget to get started.</p>
                    <a href="{{ route('starter-kit.show') }}" class="btn btn-outline">Open the Kit Builder</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Wholesale band -->
    <section class="band" id="wholesale">
        <div class="band-image ph ph-dark">
    		<img src="{{ asset('images/wholesale-band.jpg') }}" alt="Pizza boxes and 		bulk pizza supplies">
	</div>
        <div class="band-copy">
            <span class="tag-label light">Wholesale Opportunities</span>
            <h2>Scaling Up Your Pizzeria Operations?</h2>
            <p>Get bulk discounts, scheduled recurring dough deliveries, custom printed box branding options, and priority support directly from our dispatch team.</p>
            <a href="{{ route('shop', ['category' => 'boxes']) }}" class="btn btn-amber">Shop Bulk Packs</a>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-head center"><h2>Loved By Serious Pizzaiolos</h2></div>
            <div class="testi-grid">
                <div class="testi">
                    <span class="quote-mark">"</span>
                    <p>Their cold-fermented balls rise beautifully. Our customers can't tell it's contracted dough. Essential partner!</p>
                    <cite>— Chef Marco, Crust &amp; Co.</cite>
                </div>
                <div class="testi">
                    <span class="quote-mark">"</span>
                    <p>The 12-inch boxes fit perfectly, don't sag, and keep our base crispiness all the way to the customer's table.</p>
                    <cite>— Sarah L., Napoli Delivery Hub</cite>
                </div>
                <div class="testi">
                    <span class="quote-mark">"</span>
                    <p>Great paddle and a handy starter kit. I launched my home pizza business with one order. Sample review.</p>
                    <cite>— Dave K., Home Pizza Seller</cite>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container news-wrap">
            <div>
                <h3>Get Dough Recipes &amp; Special Deals</h3>
                <p>Fresh weekly formulas, expert guide alerts &amp; wholesale discount updates.</p>
            </div>
            <form class="news-form" id="newsletter-form" method="post" action="{{ route('newsletter.subscribe') }}" novalidate>
                @csrf
                <input type="email" name="email" placeholder="Enter email address" aria-label="Email address">
                <button type="submit" class="btn btn-dark">Subscribe</button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    // Featured carousel arrows
    var track = document.getElementById('featured-products');
    if (track) {
        var step = function () {
            var card = track.querySelector('.product-card');
            return card ? card.offsetWidth + 19 : 280;
        };
        var prev = document.getElementById('feat-prev'),
            next = document.getElementById('feat-next');
        if (prev) prev.onclick = function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); };
        if (next) next.onclick = function () { track.scrollBy({ left:  step(), behavior: 'smooth' }); };
    }
})();
</script>
@endpush
