<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['label'] }} Payment — {{ $businessName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: {{ $meta['brand_color'] }};
            --brand-text: {{ $meta['brand_text'] }};
            --ink: #12151f;
            --muted: #5c616b;
            --line: #e9e5dc;
            --radius: 10px;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: "Outfit", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f4f0;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .mock-page {
            background: #fff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(18,21,31,.12);
        }

        .mock-header {
            background: var(--brand);
            color: var(--brand-text);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: .8rem;
        }
        .mock-header .mock-logo {
            width: 40px; height: 40px;
            background: rgba(255,255,255,.18);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.2rem;
            flex-shrink: 0;
        }
        .mock-header .mock-logo svg { width: 22px; height: 22px; }
        .mock-header .mock-brand { font-weight: 700; font-size: 1.15rem; line-height: 1.1; }
        .mock-header .mock-brand small { display: block; font-size: .75rem; opacity: .85; font-weight: 500; }

        .mock-body { padding: 1.6rem; }

        .mock-merchant {
            display: flex; align-items: center; gap: .8rem;
            padding-bottom: 1.2rem; margin-bottom: 1.2rem;
            border-bottom: 1px solid var(--line);
        }
        .mock-merchant-avatar {
            width: 44px; height: 44px;
            background: var(--brand);
            color: var(--brand-text);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.05rem;
        }
        .mock-merchant-name { font-weight: 600; font-size: .98rem; }
        .mock-merchant-sub { font-size: .82rem; color: var(--muted); }

        .mock-amount {
            text-align: center;
            margin: 1.4rem 0 1.8rem;
        }
        .mock-amount .currency {
            font-size: 1.1rem; vertical-align: top; opacity: .8;
            margin-right: .15rem;
        }
        .mock-amount .value {
            font-size: 2.6rem; font-weight: 700; letter-spacing: -.02em;
            color: var(--ink);
        }
        .mock-amount .sub {
            font-size: .82rem; color: var(--muted);
            margin-top: .25rem;
        }

        .mock-detail {
            background: #f8f7f3;
            border-radius: 8px;
            padding: .9rem 1rem;
            margin-bottom: .7rem;
            display: flex; justify-content: space-between;
            font-size: .9rem;
        }
        .mock-detail .label { color: var(--muted); }
        .mock-detail .value { font-weight: 600; }

        .mock-card-form { display: none; margin: 1rem 0; }
        .mock-card-form.open { display: block; }
        .mock-card-form .field { margin-bottom: .8rem; }
        .mock-card-form label { display: block; font-size: .78rem; font-weight: 600; margin-bottom: .25rem; color: var(--muted); }
        .mock-card-form input {
            width: 100%; padding: .7rem .85rem;
            border: 1px solid var(--line); border-radius: 6px;
            font: inherit; background: #fff;
        }
        .mock-card-form input:focus { outline: none; border-color: var(--brand); }
        .mock-card-form .row { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
        .mock-test-hint {
            font-size: .78rem; color: #5c4400; background: #fff8e6;
            border-left: 3px solid #f5a800; padding: .6rem .8rem;
            border-radius: 4px; margin-top: .8rem;
        }
        .mock-test-hint code { font-family: monospace; background: rgba(0,0,0,.07); padding: 0 .35rem; border-radius: 3px; }

        .mock-actions {
            margin-top: 1.4rem;
            display: flex; flex-direction: column; gap: .7rem;
        }
        .mock-btn {
            padding: 1rem 1.4rem;
            border-radius: 10px;
            border: none;
            font: inherit;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity .15s, transform .05s;
        }
        .mock-btn:active { transform: scale(.99); }
        .mock-btn-approve {
            background: var(--brand);
            color: var(--brand-text);
        }
        .mock-btn-decline {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--line);
        }
        .mock-btn:disabled { opacity: .6; cursor: not-allowed; }

        .mock-footer {
            text-align: center;
            padding: 1rem;
            background: #faf9f5;
            border-top: 1px solid var(--line);
            font-size: .75rem;
            color: var(--muted);
        }
        .mock-footer code { font-family: monospace; }

        .mock-spinner-overlay {
            position: fixed; inset: 0;
            background: rgba(255,255,255,.92);
            display: none; align-items: center; justify-content: center;
            flex-direction: column;
            z-index: 1000;
        }
        .mock-spinner-overlay.show { display: flex; }
        .mock-spinner {
            width: 44px; height: 44px;
            border: 3px solid #e9e5dc;
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .mock-spinner-text {
            margin-top: 1rem; font-size: .92rem; color: var(--muted);
        }

        .mock-sandbox-tag {
            position: absolute; top: .8rem; right: .8rem;
            background: rgba(255,255,255,.2); color: var(--brand-text);
            font-size: .68rem; font-weight: 700; letter-spacing: .05em;
            padding: .25rem .55rem; border-radius: 4px; text-transform: uppercase;
        }

        @media (max-width: 480px) {
            .mock-page { max-width: 100%; border-radius: 0; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="mock-page">
        <div class="mock-header" style="position:relative">
            <div class="mock-logo">
                @if ($method === 'card')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                @elseif ($method === 'gcash')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 12a9 9 0 11-9-9c2.5 0 4.7.9 6.4 2.5"/><path d="M17 8l-4 4-2-2"/></svg>
                @elseif ($method === 'maya')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-5"/></svg>
                @elseif ($method === 'online_banking')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 14h6"/></svg>
                @elseif ($method === 'otc')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 9l1-4h16l1 4"/><path d="M4 9v11h16V9"/><path d="M9 13h6"/></svg>
                @endif
            </div>
            <div class="mock-brand">
                {{ $meta['label'] }}
                <small>
                    @if ($method === 'card')
                        Secure card payment
                    @elseif ($method === 'gcash')
                        GCash wallet
                    @elseif ($method === 'maya')
                        Maya wallet
                    @elseif ($method === 'online_banking')
                        {{ $bankLabel }} online banking
                    @elseif ($method === 'otc')
                        {{ $channelLabel }} cash payment
                    @endif
                </small>
            </div>
            <span class="mock-sandbox-tag">Sandbox</span>
        </div>

        <div class="mock-body">
            <div class="mock-merchant">
                <div class="mock-merchant-avatar">D</div>
                <div>
                    <div class="mock-merchant-name">{{ $businessName }}</div>
                    <div class="mock-merchant-sub">Pizza supplies &amp; equipment</div>
                </div>
            </div>

            <div class="mock-amount">
                <span class="currency">₱</span><span class="value">{{ number_format($order->total) }}</span>
                <div class="sub">Total to pay</div>
            </div>

            <div class="mock-detail">
                <span class="label">Order reference</span>
                <span class="value">{{ $order->ref }}</span>
            </div>
            <div class="mock-detail">
                <span class="label">Items</span>
                <span class="value">{{ $order->items->count() }} item(s)</span>
            </div>
            <div class="mock-detail">
                <span class="label">Customer</span>
                <span class="value">{{ $order->first_name }} {{ $order->last_name }}</span>
            </div>
            <div class="mock-detail">
                <span class="label">Payment method</span>
                <span class="value">
                    {{ $meta['label'] }}
                    @if ($method === 'online_banking') · {{ $bankLabel }} @endif
                    @if ($method === 'otc') · {{ $channelLabel }} @endif
                </span>
            </div>

            @if ($method === 'card')
                <div class="mock-card-form open">
                    <div class="field">
                        <label>Card number</label>
                        <input type="text" id="mock-card-number" inputmode="numeric" placeholder="4242 4242 4242 4242" autocomplete="cc-number" maxlength="19">
                    </div>
                    <div class="row">
                        <div class="field">
                            <label>Expiry (MM/YY)</label>
                            <input type="text" id="mock-card-exp" placeholder="12 / 28" autocomplete="cc-exp" maxlength="7">
                        </div>
                        <div class="field">
                            <label>CVC</label>
                            <input type="text" id="mock-card-cvc" inputmode="numeric" placeholder="123" autocomplete="cc-csc" maxlength="4">
                        </div>
                    </div>
                    <div class="mock-test-hint">
                        <strong>Sandbox test cards:</strong><br>
                        Visa: <code>4242 4242 4242 4242</code><br>
                        Mastercard: <code>5555 5555 5555 4444</code><br>
                        Any future expiry + any 3-digit CVC works.
                    </div>
                </div>
            @endif

            <form method="post" action="{{ route('mock.complete', ['ref' => $order->ref]) }}" id="mock-form">
                @csrf
                <input type="hidden" name="result" id="result-input" value="">
                <div class="mock-actions">
                    <button type="submit" data-result="approve" class="mock-btn mock-btn-approve" id="approve-btn">
                        @if ($method === 'card')
                            Pay ₱{{ number_format($order->total) }}
                        @elseif ($method === 'gcash' || $method === 'maya')
                            Approve via {{ $meta['label'] }}
                        @elseif ($method === 'online_banking')
                            Approve via {{ $bankLabel }}
                        @elseif ($method === 'otc')
                            Generate reference &amp; approve
                        @endif
                    </button>
                    <button type="submit" data-result="decline" class="mock-btn mock-btn-decline">
                        Cancel payment
                    </button>
                </div>
            </form>
        </div>

        <div class="mock-footer">
            This is a <strong>mock payment gateway</strong> for demonstration purposes only.<br>
            No real money is charged. Payment reference: <code>{{ $order->payment_ref ?? 'MOCK-' . $order->ref }}</code>
        </div>
    </div>

    <div class="mock-spinner-overlay" id="spinner">
        <div class="mock-spinner"></div>
        <div class="mock-spinner-text">Processing payment…</div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('mock-form');
            var spinner = document.getElementById('spinner');
            var resultInput = document.getElementById('result-input');
            var buttons = form.querySelectorAll('button[type=submit]');

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Set the hidden input value from data-result attribute
                    resultInput.value = btn.dataset.result;

                    // For card method with approve: validate card details
                    @if ($method === 'card')
                        if (btn.dataset.result === 'approve') {
                            var num = document.getElementById('mock-card-number').value.replace(/\s+/g, '');
                            var exp = document.getElementById('mock-card-exp').value;
                            var cvc = document.getElementById('mock-card-cvc').value;
                            if (!/^\d{15,16}$/.test(num)) {
                                alert('Enter a test card number (e.g. 4242 4242 4242 4242).');
                                return;
                            }
                            if (!/\d{2}\s*\/\s*\d{2,4}/.test(exp)) {
                                alert('Enter the card expiry as MM / YY.');
                                return;
                            }
                            if (!/^\d{3,4}$/.test(cvc)) {
                                alert('Enter the 3-digit CVC.');
                                return;
                            }
                        }
                    @endif

                    // Show spinner
                    spinner.classList.add('show');

                    // Disable all buttons so user can't click again
                    buttons.forEach(function (b) { b.disabled = true; });

                    // Submit after 1.5s delay (simulates processing)
                    // form.submit() will include the hidden input's value
                    setTimeout(function () {
                        form.submit();
                    }, 1500);
                });
            });

            // Format card number with spaces
            var cn = document.getElementById('mock-card-number');
            if (cn) {
                cn.addEventListener('input', function () {
                    var v = this.value.replace(/\D/g, '').slice(0, 16);
                    this.value = v.replace(/(.{4})/g, '$1 ').trim();
                });
            }
            // Format expiry
            var ce = document.getElementById('mock-card-exp');
            if (ce) {
                ce.addEventListener('input', function () {
                    var v = this.value.replace(/\D/g, '').slice(0, 4);
                    if (v.length >= 3) this.value = v.slice(0, 2) + ' / ' + v.slice(2);
                    else if (v.length === 2) this.value = v + ' / ';
                    else this.value = v;
                });
            }
        })();
    </script>
</body>
</html>
