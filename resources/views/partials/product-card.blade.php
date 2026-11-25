@php
/** @var \App\Models\Product $product */
$img = $product->imageUrl();
@endphp
<article class="product-card">
    <a href="{{ route('product.show', ['id' => $product->public_id]) }}" class="product-image ph" aria-label="{{ $product->name }}">
        @if ($product->wholesale)
            <span class="img-badge">WHOLESALE PACK</span>
        @endif
        @if ($img)
            <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block">
        @else
            <span>[PRODUCT IMAGE]</span>
        @endif
    </a>
    <div class="product-info">
        <span class="product-cat">{{ $product->category->label }}</span>
        <h3><a href="{{ route('product.show', ['id' => $product->public_id]) }}" title="{{ $product->name }}">{{ $product->name }}</a></h3>
        <strong class="price">{{ $product->peso() }}</strong>
    </div>
    <form method="post" action="{{ route('cart.add', ['id' => $product->public_id]) }}" class="add-row-form">
        @csrf
        <input type="hidden" name="qty" value="1">
        <button type="submit" class="add-row" data-add="{{ $product->public_id }}" aria-label="Add {{ $product->name }} to order">
            <span>Add to Order</span><span class="plus" aria-hidden="true">+</span>
        </button>
    </form>
</article>
