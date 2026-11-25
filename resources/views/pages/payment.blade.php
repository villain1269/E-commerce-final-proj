@extends('layouts.checkout')

@section('title', 'Choose Payment Method | Danielle\'s Crafted Pizza')

@section('content')
    <div class="payment-wrap">
        <div class="payment-head">
            <h1>Choose how to pay</h1>
            <p class="order-ref">Order No. <strong>{{ $order->ref }}</strong></p>
            <span class="total-pill">Total: {{ \App\Helpers\Format::peso($order->total) }}</span>
        </div>

        @if ($xenditConfigured ?? false)
            <div class="sandbox-banner">
                <b>Xendit test mode active.</b>
                @if ($xenditSandbox ?? true)
                    No real money will be charged. GCash / Maya / OTC will redirect to Xendit's sandbox pages where you can simulate approvals. Cards and online banking fall back to the built-in mock gateway (Xendit.js card tokenization isn't wired up yet).
                @else
                    Production mode — real charges will be processed.
                @endif
            </div>
        @else
            <div class="sandbox-banner">
                <b>Mock mode.</b> No real money will be charged. You'll be redirected to a realistic mock payment page where you can approve or decline the payment.<br>
                <small style="display:block;margin-top:.4rem;color:#7a6a00">
                    Tip: To use real Xendit test mode, add your sandbox keys to <code>.env</code>:
                    <code>XENDIT_SECRET_KEY=xnd_development_...</code> and <code>XENDIT_PUBLIC_KEY=xnd_public_development_...</code>
                </small>
            </div>
        @endif

        <div class="payment-layout">
            <!-- LEFT: Method picker -->
            <form method="post" action="{{ route('payment.pay', ['ref' => $order->ref]) }}" id="pay-form">
                @csrf
                <input type="hidden" name="method_type" id="method-input" value="">
                <input type="hidden" name="bank" id="bank-input" value="">
                <input type="hidden" name="channel" id="channel-input" value="">

                <div class="method-tiles" id="method-tiles">
                    @foreach ($methods as $key => $meta)
                        <div class="method-tile" data-method="{{ $key }}" style="{{ $loop->last ? '' : 'margin-bottom:.8rem' }}">
                            <div class="method-icon" style="background:{{ $meta['brand_color'] }};color:{{ $meta['brand_text'] }}">
                                @if ($key === 'card')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                @elseif ($key === 'gcash')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 12a9 9 0 11-9-9c2.5 0 4.7.9 6.4 2.5"/><path d="M17 8l-4 4-2-2"/></svg>
                                @elseif ($key === 'maya')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-5"/></svg>
                                @elseif ($key === 'online_banking')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 14h6"/></svg>
                                @elseif ($key === 'otc')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 9l1-4h16l1 4"/><path d="M4 9v11h16V9"/><path d="M9 13h6"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="method-name">
                                    {{ $meta['label'] }}
                                    @if ($xenditConfigured ?? false)
                                        @if ($key === 'gcash' || $key === 'maya' || $key === 'otc')
                                            <small style="display:inline-block;margin-left:.5rem;padding:.1rem .35rem;background:#e3f1ff;color:#006AFF;border-radius:3px;font-size:.65rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase">Xendit live</small>
                                        @else
                                            <small style="display:inline-block;margin-left:.5rem;padding:.1rem .35rem;background:#fff8e6;color:#7a6a00;border-radius:3px;font-size:.65rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase">Mock</small>
                                        @endif
                                    @endif
                                </div>
                                <div class="method-sub">{{ $meta['sub'] }}</div>
                            </div>
                            <span class="method-radio"></span>
                        </div>

                        {{-- Sub-options for online banking --}}
                        @if ($key === 'online_banking')
                            <div class="method-suboptions" data-for="online_banking" style="margin-bottom:.8rem">
                                <h5>Select your bank</h5>
                                <select id="bank-select">
                                    @foreach ($banks as $bk => $bl)
                                        <option value="{{ $bk }}">{{ $bl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Sub-options for OTC --}}
                        @if ($key === 'otc')
                            <div class="method-suboptions" data-for="otc" style="margin-bottom:.8rem">
                                <h5>Select a payment outlet</h5>
                                <select id="channel-select">
                                    @foreach ($channels as $ck => $cl)
                                        <option value="{{ $ck }}">{{ $cl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endforeach
                </div>

                <button type="submit" class="pay-btn" id="pay-now-btn" disabled>Pay {{ \App\Helpers\Format::peso($order->total) }}</button>
            </form>

            <p style="margin-top:1rem;text-align:center">
                <a href="{{ route('cart.index') }}" style="font-size:.9rem;color:var(--muted)">← Return to cart</a>
            </p>

            <!-- RIGHT: Order summary -->
        </div>

        <aside class="summary-box payment-summary" style="margin-top:2rem">
            <h3>Order Summary</h3>
            @foreach ($order->items as $item)
                <div class="summary-line">
                    <span>{{ $item->product_name }} <small>×{{ $item->qty }}</small></span>
                    <span>{{ \App\Helpers\Format::peso($item->price * $item->qty) }}</span>
                </div>
            @endforeach
            <div class="summary-line"><span>Subtotal</span><span>{{ \App\Helpers\Format::peso($order->subtotal) }}</span></div>
            <div class="summary-line">
                <span>{{ $order->shipping_method === 'express' ? 'Express Food Dispatch' : 'Standard Delivery' }}</span>
                <span>{{ $order->shipping === 0 ? 'FREE' : \App\Helpers\Format::peso($order->shipping) }}</span>
            </div>
            <div class="summary-line total"><span>Total</span><span>{{ \App\Helpers\Format::peso($order->total) }}</span></div>

            <div style="margin-top:1.2rem;font-size:.85rem;color:var(--muted)">
                <strong>Delivery:</strong><br>
                {{ $order->first_name }} {{ $order->last_name }}<br>
                {{ $order->address }}, {{ $order->city }} {{ $order->postal }}<br>
                {{ $order->phone }}
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var tiles = document.querySelectorAll('.method-tile');
    var subOptions = document.querySelectorAll('.method-suboptions');
    var payBtn = document.getElementById('pay-now-btn');
    var methodInput = document.getElementById('method-input');
    var bankInput = document.getElementById('bank-input');
    var channelInput = document.getElementById('channel-input');
    var bankSelect = document.getElementById('bank-select');
    var channelSelect = document.getElementById('channel-select');
    var total = {{ $order->total }};

    function fmt(n) { return '₱' + n.toLocaleString('en-PH'); }

    function labelFor(m) {
        return { gcash: 'GCash', maya: 'Maya', card: 'Card', online_banking: 'Online Banking', otc: 'OTC' }[m] || m;
    }

    function selectMethod(method) {
        methodInput.value = method;
        tiles.forEach(function (t) {
            t.classList.toggle('selected', t.dataset.method === method);
        });
        subOptions.forEach(function (s) {
            s.classList.toggle('open', s.dataset.for === method);
        });
        // Capture current selection
        if (bankSelect) bankInput.value = bankSelect.value;
        if (channelSelect) channelInput.value = channelSelect.value;
        payBtn.disabled = false;
        payBtn.textContent = 'Pay ' + fmt(total) + ' with ' + labelFor(method);
    }

    tiles.forEach(function (t) {
        t.addEventListener('click', function () {
            selectMethod(t.dataset.method);
        });
    });

    if (bankSelect) bankSelect.addEventListener('change', function () { bankInput.value = this.value; });
    if (channelSelect) channelSelect.addEventListener('change', function () { channelInput.value = this.value; });
})();
</script>
@endpush
