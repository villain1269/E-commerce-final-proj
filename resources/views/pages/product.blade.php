@extends('layouts.app')

@section('title', $product->name . " | Danielle's Crafted Pizza")

@section('content')
    <main class="container">
        <nav class="breadcrumb" id="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span>›</span>
            <a href="{{ route('shop', ['category' => $product->category->key]) }}">{{ $product->category->label }}</a>
            <span>›</span>
            <span class="here">{{ $product->name }}</span>
        </nav>

        <div class="detail-grid">
            <div>
                @php $mainImg = $product->imageUrl(); @endphp
                <div class="detail-main ph" style="border-radius:10px;@if ($mainImg) background:none;padding:0;@endif">
                    @if ($mainImg)
                        <img src="{{ $mainImg }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;display:block">
                    @else
                        [PRODUCT IMAGE]
                    @endif
                </div>
                <div class="thumbs">
                    @foreach (range(2, 4) as $i)
                        @php
                            $thumbImg = null;
                            $thumbCandidates = [
                                $product->public_id . '-' . $i,
                                $product->sku . '-' . $i,
                                str($product->name)->slug() . '-' . $i,
                            ];
                            foreach ($thumbCandidates as $base) {
                                foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                                    $rel = "products/{$base}.{$ext}";
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) {
                                        $thumbImg = \Illuminate\Support\Facades\Storage::disk('public')->url($rel);
                                        break 2;
                                    }
                                }
                            }
                        @endphp
                        <div class="ph" style="border-radius:6px;@if ($thumbImg) background:none;padding:0;@endif">
                            @if ($thumbImg)
                                <img src="{{ $thumbImg }}" alt="{{ $product->name }} view {{ $i }}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;display:block">
                            @else
                                [IMAGE {{ $i }}]
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="detail-card">
                <span class="tag-label">{{ $product->hasTag('Best Seller') ? 'BESTSELLER ' . $product->category->badge : strtoupper($product->category->label) }}</span>
                <h1>{{ $product->name }}</h1>
                <div class="rating">
                    <span class="stars" aria-label="{{ $product->rating }} out of 5 stars">{{ $product->starString() }}</span>
                    <span>({{ $product->reviews }} reviews)</span>
                </div>
                <div class="detail-price">{{ $product->peso() }} <small>/ {{ $product->unit }}</small></div>
                <p class="detail-desc">{{ $product->desc }}</p>

                <form method="post" action="{{ route('cart.add', ['id' => $product->public_id]) }}" class="buy-row-form">
                    @csrf
                    <div class="buy-row">
                        <div class="qty-control">
                            <button type="button" id="qty-minus" aria-label="Decrease">−</button>
                            <input id="detail-qty" name="qty" type="number" min="1" max="99" value="1" aria-label="Quantity">
                            <button type="button" id="qty-plus" aria-label="Increase">+</button>
                        </div>
                        <button class="btn" type="submit" id="detail-add">Add to Cart • {{ $product->peso() }}</button>
                    </div>
                </form>

                <div class="notice"><span>Selling pizza? Orders over <b>₱3,000 ship free</b>, and wholesale pricing is available for bigger orders.</span></div>
                <div class="meta">
                    <div><strong>SKU:</strong> {{ $product->sku }}</div>
                    <div><strong>Category:</strong> {{ $product->category->label }}</div>
                    @if ($product->specSecondaryLabel())
                        <div><strong>{{ $product->specSecondaryLabel() }}:</strong> {{ $product->specSecondaryValue() }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div id="product-extra">
            <div class="tabs" id="tabs" role="tablist">
                <button class="tab active" data-tab="desc">Description</button>
                <button class="tab" data-tab="specs">Ingredients / Specs</button>
                <button class="tab" data-tab="reviews">Customer Reviews ({{ $product->reviews }})</button>
            </div>

            <div class="tab-panel" id="tab-panel">
                <p>{{ $product->desc }}</p>
                <p>Packed in seller-friendly quantities and prepared fresh for pickup or delivery. Message us for wholesale pricing or custom orders.</p>
            </div>

            <section class="related">
                <h2>Perfect Companions</h2>
                <div class="product-grid">
                    @foreach ($related as $relProduct)
                        @include('partials.product-card', ['product' => $relProduct])
                    @endforeach
                </div>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
<script>
(function () {
    // Quantity + add-to-cart label sync
    var qty = document.getElementById('detail-qty'),
        addBtn = document.getElementById('detail-add');
    if (!qty || !addBtn) return;
    var unit = {{ $product->price }};
    var clamp = function () {
        var v = parseInt(qty.value, 10);
        if (isNaN(v) || v < 1) v = 1;
        if (v > 99) v = 99;
        qty.value = v;
        return v;
    };
    var updateLabel = function () {
        addBtn.textContent = 'Add to Cart • ₱' + (unit * clamp()).toLocaleString('en-PH');
    };
    document.getElementById('qty-minus').onclick = function () { qty.value = clamp() - 1; updateLabel(); };
    document.getElementById('qty-plus').onclick  = function () { qty.value = clamp() + 1; updateLabel(); };
    qty.addEventListener('change', updateLabel);
    updateLabel();

    // Tabs (Description / Specs / Reviews)
    var tabsEl = document.getElementById('tabs');
    var panel = document.getElementById('tab-panel');
    if (!tabsEl || !panel) return;

    var specRows = @json(collect($product->specs)->map(fn ($s) => ['label' => $s[0], 'value' => $s[1]]));
    var specHtml = '<table class="spec-table"><tbody>';
    specRows.forEach(function (s) {
        specHtml += '<tr><th>' + s.label + '</th><td>' + s.value + '</td></tr>';
    });
    specHtml += '<tr><th>SKU</th><td>{{ $product->sku }}</td></tr>';
    specHtml += '</tbody></table>';

    var panels = {
        desc: '<p>{{ e($product->desc) }}</p>' +
              '<p>Packed in seller-friendly quantities and prepared fresh for pickup or delivery. Message us for wholesale pricing or custom orders.</p>',
        specs: specHtml,
        reviews: '<div class="review"><span class="stars">★★★★★</span> <strong>Chef Marco</strong>' +
                  '<p>Consistent every single batch. Our customers noticed the difference right away.</p></div>' +
                  '<div class="review"><span class="stars">{{ $product->starString() }}</span> <strong>Sarah L.</strong>' +
                  '<p>Great value for the pack size and easy to reorder. Sample review for demonstration.</p></div>'
    };

    tabsEl.addEventListener('click', function (e) {
        var b = e.target.closest('[data-tab]');
        if (!b) return;
        tabsEl.querySelectorAll('.tab').forEach(function (t) {
            t.classList.toggle('active', t === b);
        });
        panel.innerHTML = panels[b.dataset.tab] || '';
    });
})();
</script>
@endpush
