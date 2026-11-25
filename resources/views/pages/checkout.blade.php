@extends('layouts.checkout')

@section('content')
    <div class="container checkout-layout">
        <form id="checkout-form" method="post" action="{{ route('checkout.place') }}" novalidate>
            @csrf

            <div class="form-card">
                <h3>1. Delivery Shipping Information</h3>
                <div class="form-row">
                    <div class="field {{ $errors->has('firstName') ? 'invalid' : '' }}">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" autocomplete="given-name">
                        @error('firstName') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="field {{ $errors->has('lastName') ? 'invalid' : '' }}">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" autocomplete="family-name">
                        @error('lastName') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="field {{ $errors->has('email') ? 'invalid' : '' }}">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email">
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="field {{ $errors->has('address') ? 'invalid' : '' }}">
                    <label for="address">Street Address *</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" autocomplete="street-address">
                    @error('address') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-row">
                    <div class="field {{ $errors->has('city') ? 'invalid' : '' }}">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2">
                        @error('city') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="field {{ $errors->has('postal') ? 'invalid' : '' }}">
                        <label for="postal">Postal Code *</label>
                        <input type="text" id="postal" name="postal" value="{{ old('postal') }}" inputmode="numeric" maxlength="4" autocomplete="postal-code">
                        @error('postal') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="field {{ $errors->has('phone') ? 'invalid' : '' }}">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="09171234567" autocomplete="tel">
                    @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="notes">Order Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Delivery instructions, preferred time, etc.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-card">
                <h3>2. Shipping Method</h3>
                @php $selectedShip = old('shipping', 'express'); @endphp
                <label class="ship-option">
                    <input type="radio" name="shipping" value="express" {{ $selectedShip === 'express' ? 'checked' : '' }}>
                    <span class="opt-text">Express Food Dispatch (Guaranteed Fresh)<small>Delivered in insulated, temperature-protected packs.</small></span>
                    <span class="opt-price" id="price-express">{{ $express === 0 ? 'FREE' : \App\Helpers\Format::peso($express) }}</span>
                </label>
                <label class="ship-option">
                    <input type="radio" name="shipping" value="standard" {{ $selectedShip === 'standard' ? 'checked' : '' }}>
                    <span class="opt-text">Standard Delivery<small>Free on orders over ₱3,000. Best for packaging and equipment.</small></span>
                    <span class="opt-price" id="price-standard">{{ $standard === 0 ? 'FREE' : \App\Helpers\Format::peso($standard) }}</span>
                </label>
            </div>

            <div class="form-card">
                <h3>3. Payment Method</h3>
                @php $selectedPay = old('payment'); @endphp
                <label class="pay-option">
                    <input type="radio" name="payment" value="cod" {{ $selectedPay === 'cod' ? 'checked' : '' }}>
                    <span class="opt-text">Cash on Delivery<small>Pay cash when your order arrives.</small></span>
                </label>
                <label class="pay-option">
                    <input type="radio" name="payment" value="online" {{ $selectedPay === 'online' ? 'checked' : '' }}>
                    <span class="opt-text">Pay Online (GCash, Maya, Card, Bank, OTC)<small>Redirects to a realistic mock payment screen after you place your order. Sandbox mode — no real money is charged.</small></span>
                </label>
                @error('payment') <span class="inline-error" id="payment-error">{{ $message }}</span> @enderror
                <p class="note">This is a school project. No real money is charged. Online payments run in a built-in mock gateway that simulates GCash, Maya, cards, bank transfers, and OTC cash payments.</p>
            </div>

            <label class="check-line {{ $errors->has('terms') ? 'invalid' : '' }}">
                <input type="checkbox" name="terms" {{ old('terms') ? 'checked' : '' }}>
                <span>I confirm my order details are correct and I agree to the order terms.</span>
            </label>
            @error('terms') <span class="inline-error" id="terms-error">{{ $message }}</span> @enderror
        </form>

        <div class="checkout-summary-wrap">
            <aside class="summary-box" id="checkout-summary">
                <h3>Review Your Order</h3>
                @foreach ($items as $i)
                    @php $p = $i['product']; @endphp
                    <div class="co-item">
                        <div class="cart-thumb ph">[IMG]</div>
                        <div class="co-name">{{ $p->name }}<small>Qty: {{ $i['qty'] }}</small></div>
                        <div class="co-price">{{ $p->peso($i['qty']) }}</div>
                    </div>
                @endforeach

                <div class="co-divider"></div>
                <div class="summary-line"><span>Subtotal</span><span>{{ \App\Helpers\Format::peso($subtotal) }}</span></div>
                <div class="summary-line" id="ship-line">
                    <span>Express Food Dispatch</span><span>{{ $express === 0 ? 'FREE' : \App\Helpers\Format::peso($express) }}</span>
                </div>
                <div class="summary-line total"><span>Final Total</span><span id="final-total">{{ \App\Helpers\Format::peso($subtotal + $express) }}</span></div>
                <button type="submit" form="checkout-form" class="btn btn-block">Place Order • <span id="place-order-total">{{ \App\Helpers\Format::peso($subtotal + $express) }}</span></button>
            </aside>

            <aside class="summary-box secure-card">
                <h4><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"/></svg> Demo Checkout</h4>
                <p>Nothing is charged. Your order is saved to the database once you click Place Order.</p>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('checkout-form');
    if (!form) return;

    var expressCost = {{ $express }};
    var standardCost = {{ $standard }};
    var subtotal = {{ $subtotal }};

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
@endpush
