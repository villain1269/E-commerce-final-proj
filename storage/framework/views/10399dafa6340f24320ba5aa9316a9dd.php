<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connecting to <?php echo e($meta['label']); ?>…</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: <?php echo e($meta['brand_color']); ?>;
            --brand-text: <?php echo e($meta['brand_text']); ?>;
            --ink: #12151f;
            --muted: #5c616b;
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
        .connecting-page {
            background: #fff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(18,21,31,.12);
            text-align: center;
        }
        .connecting-header {
            background: var(--brand);
            color: var(--brand-text);
            padding: 2.2rem 1.5rem 1.8rem;
        }
        .connecting-logo {
            width: 70px; height: 70px;
            background: rgba(255,255,255,.18);
            border-radius: 16px;
            margin: 0 auto 1rem;
            display: flex; align-items: center; justify-content: center;
        }
        .connecting-logo svg { width: 36px; height: 36px; }
        .connecting-brand { font-weight: 700; font-size: 1.3rem; line-height: 1.1; }
        .connecting-brand-sub {
            font-size: .82rem; opacity: .85; font-weight: 500;
            margin-top: .25rem;
        }

        .connecting-body { padding: 2rem 1.6rem 1.4rem; }
        .connecting-amount {
            font-size: 2.4rem; font-weight: 700; letter-spacing: -.02em;
            margin: 0 0 .3rem;
        }
        .connecting-amount .currency {
            font-size: 1.1rem; vertical-align: top; opacity: .8;
            margin-right: .15rem;
        }
        .connecting-merchant {
            color: var(--muted); font-size: .9rem;
            margin-bottom: 1.8rem;
        }
        .connecting-merchant strong { color: var(--ink); }

        .spinner-wrap {
            display: flex; align-items: center; justify-content: center;
            margin: 1rem 0 1.2rem;
        }
        .spinner {
            width: 38px; height: 38px;
            border: 3px solid #e9e5dc;
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .connecting-status {
            font-size: .95rem; font-weight: 600;
            color: var(--ink);
            margin-bottom: .3rem;
        }
        .connecting-substatus {
            font-size: .82rem; color: var(--muted);
        }

        .order-info {
            background: #f8f7f3;
            border-radius: 8px;
            padding: .8rem 1rem;
            margin-top: 1.4rem;
            text-align: left;
            font-size: .85rem;
        }
        .order-info-row {
            display: flex; justify-content: space-between;
            padding: .25rem 0;
        }
        .order-info-row .label { color: var(--muted); }
        .order-info-row .value { font-weight: 600; }

        .connecting-footer {
            padding: 1rem;
            background: #faf9f5;
            border-top: 1px solid #e9e5dc;
            font-size: .75rem;
            color: var(--muted);
            text-align: center;
        }
        .connecting-footer code { font-family: monospace; }

        .cancel-link {
            display: inline-block;
            margin-top: 1.2rem;
            font-size: .85rem;
            color: var(--muted);
            text-decoration: none;
        }
        .cancel-link:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .connecting-page { max-width: 100%; border-radius: 0; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="connecting-page">
        <div class="connecting-header">
            <div class="connecting-logo">
                <?php if($method === 'card'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                <?php elseif($method === 'gcash'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 12a9 9 0 11-9-9c2.5 0 4.7.9 6.4 2.5"/><path d="M17 8l-4 4-2-2"/></svg>
                <?php elseif($method === 'maya'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-5"/></svg>
                <?php elseif($method === 'online_banking'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M9 14h6"/></svg>
                <?php elseif($method === 'otc'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 9l1-4h16l1 4"/><path d="M4 9v11h16V9"/><path d="M9 13h6"/></svg>
                <?php endif; ?>
            </div>
            <div class="connecting-brand"><?php echo e($meta['label']); ?></div>
            <div class="connecting-brand-sub">
                <?php if($method === 'card'): ?>
                    Secure card payment
                <?php elseif($method === 'gcash'): ?>
                    GCash wallet
                <?php elseif($method === 'maya'): ?>
                    Maya wallet
                <?php elseif($method === 'online_banking'): ?>
                    <?php echo e($bankLabel ?? 'Online banking'); ?>

                <?php elseif($method === 'otc'): ?>
                    <?php echo e($channelLabel ?? 'OTC cash payment'); ?>

                <?php endif; ?>
            </div>
        </div>

        <div class="connecting-body">
            <div class="connecting-amount">
                <span class="currency">₱</span><?php echo e(number_format($order->total)); ?>

            </div>
            <div class="connecting-merchant">
                Paying <strong><?php echo e($businessName); ?></strong>
            </div>

            <div class="spinner-wrap">
                <div class="spinner"></div>
            </div>
            <div class="connecting-status" id="status-text">Connecting to <?php echo e($meta['label']); ?>…</div>
            <div class="connecting-substatus" id="substatus-text">You'll be redirected to authorize the payment.</div>

            <div class="order-info">
                <div class="order-info-row">
                    <span class="label">Order reference</span>
                    <span class="value"><?php echo e($order->ref); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="label">Payment method</span>
                    <span class="value"><?php echo e($meta['label']); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="label">Customer</span>
                    <span class="value"><?php echo e($order->first_name); ?> <?php echo e($order->last_name); ?></span>
                </div>
            </div>

            <a href="<?php echo e(route('payment.show', ['ref' => $order->ref])); ?>" class="cancel-link">← Cancel and choose another method</a>
        </div>

        <div class="connecting-footer">
            <?php if($gateway === 'xendit'): ?>
                Powered by <strong>Xendit</strong> · Sandbox mode<br>
                Payment request: <code><?php echo e($order->payment_intent_id); ?></code>
            <?php else: ?>
                Sandbox mock · Payment ref: <code><?php echo e($order->payment_ref); ?></code>
            <?php endif; ?>
        </div>
    </div>

    <form id="redirect-form" action="<?php echo e($redirect_url); ?>" method="get" style="display:none">
        
    </form>

    <script>
        (function () {
            var statusEl = document.getElementById('status-text');
            var subEl = document.getElementById('substatus-text');
            var url = "<?php echo e($redirect_url); ?>";
            var method = "<?php echo e($method); ?>";

            // Update status message after ~700ms
            setTimeout(function () {
                statusEl.textContent = 'Opening ' +
                    (method === 'gcash' ? 'GCash' :
                     method === 'maya' ? 'Maya' :
                     method === 'card' ? 'card payment' :
                     method === 'online_banking' ? '<?php echo e($bankLabel ?? "bank"); ?>' :
                     method === 'otc' ? '<?php echo e($channelLabel ?? "outlet"); ?>' : 'payment') +
                    '…';
                subEl.textContent = 'Redirecting you to a secure page.';
            }, 700);

            // After ~1.6s total, redirect to the gateway URL
            setTimeout(function () {
                statusEl.textContent = 'Redirecting…';
                subEl.textContent = '';
                window.location.href = url;
            }, 1600);
        })();
    </script>
</body>
</html>
<?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/connecting.blade.php ENDPATH**/ ?>