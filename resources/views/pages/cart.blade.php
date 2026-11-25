@extends('layouts.app')

@section('title', 'Your Shopping Cart | Danielle\'s Crafted Pizza')

@section('content')
    <main class="container">
        <div class="cart-title"><h1>Your Shopping Cart</h1></div>

        @if ($items->isEmpty())
            <div id="cart-content">
                <div class="empty-state">
                    <h2>Your cart is empty</h2>
                    <p>Add some dough, boxes or equipment to get started.</p>
                    <a class="btn" href="{{ route('shop') }}">Start Shopping</a>
                    <a class="btn btn-outline" href="{{ route('starter-kit.show') }}">Try the Starter Kit Builder</a>
                </div>
            </div>
        @else
            <div id="cart-content">
                <div class="cart-layout">
                    <div class="cart-table">
                        <div class="cart-head">
                            <span>Product</span><span>Price</span><span>Quantity</span><span>Total</span><span></span>
                        </div>

                        @foreach ($items as $i)
                            @php $p = $i['product']; @endphp
                            <div class="cart-row" data-id="{{ $p->public_id }}">
                                <div class="cart-item">
                                    <div class="cart-thumb ph">[IMG]</div>
                                    <div>
                                        <a href="{{ route('product.show', ['id' => $p->public_id]) }}"><strong>{{ $p->name }}</strong></a>
                                        <small>SKU: {{ $p->sku }}</small>
                                    </div>
                                </div>
                                <span class="cart-price">{{ $p->peso() }}</span>

                                <div class="qty-control">
                                    <form method="post" action="{{ route('cart.dec', ['id' => $p->public_id]) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" aria-label="Decrease quantity">−</button>
                                    </form>
                                    <form method="post" action="{{ route('cart.qty', ['id' => $p->public_id]) }}" style="display:inline">
                                        @csrf
                                        <input type="number" min="1" max="99" value="{{ $i['qty'] }}" name="qty" aria-label="Quantity" onchange="this.form.submit()">
                                    </form>
                                    <form method="post" action="{{ route('cart.inc', ['id' => $p->public_id]) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" aria-label="Increase quantity">+</button>
                                    </form>
                                </div>

                                <strong class="line-total">{{ $p->peso($i['qty']) }}</strong>

                                <form method="post" action="{{ route('cart.remove', ['id' => $p->public_id]) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="remove-btn" aria-label="Remove {{ $p->name }}">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <aside class="summary-box">
                        <h3>Order Summary</h3>
                        <div class="summary-line"><span>Subtotal</span><span>{{ \App\Helpers\Format::peso($subtotal) }}</span></div>
                        <div class="summary-line"><span>Standard Delivery</span><span class="{{ $shipping === 0 ? '' : 'red' }}">{{ $shipping === 0 ? 'FREE' : \App\Helpers\Format::peso($shipping) }}</span></div>
                        <div class="summary-line total"><span>Total</span><span>{{ \App\Helpers\Format::peso($total) }}</span></div>

                        @if ($toFree > 0)
                            <p class="note">Add {{ \App\Helpers\Format::peso($toFree) }} more for free standard delivery. Express option at checkout.</p>
                        @else
                            <p class="note ok">You qualify for free standard delivery.</p>
                        @endif

                        <a class="btn btn-block" href="{{ route('checkout.index') }}">Proceed to Secure Checkout</a>
                        <a class="link-center" href="{{ route('shop') }}">— or — Continue Shopping</a>

                        <form method="post" action="{{ route('cart.clear') }}">
                            @csrf
                            <button type="submit" class="link-center" style="background:none;border:none;color:inherit;cursor:pointer;text-decoration:underline;font-size:.9rem;margin-top:.5rem">
                                Clear cart
                            </button>
                        </form>
                    </aside>
                </div>
            </div>
        @endif

        @if (! $upsells->isEmpty())
            <section class="upsell">
                <h2>Forgot Any Pizza Essentials?</h2>
                <div class="product-grid">
                    @foreach ($upsells as $product)
                        @include('partials.product-card')
                    @endforeach
                </div>
            </section>
        @endif
    </main>
@endsection
