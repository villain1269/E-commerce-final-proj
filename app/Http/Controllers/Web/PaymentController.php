<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * All payment methods supported by the gateway.
     * Each entry has the brand colors / SVG path used by the mock gateway page
     * so it visually looks like the real wallet's checkout screen.
     */
    public const METHODS = [
        'gcash' => [
            'label'       => 'GCash',
            'sub'         => 'Opens GCash login in a new tab',
            'brand_color' => '#006AFF',
            'brand_text'  => '#FFFFFF',
            'icon'        => 'wallet',
        ],
        'maya' => [
            'label'       => 'Maya',
            'sub'         => 'Opens Maya login in a new tab',
            'brand_color' => '#21B14B',
            'brand_text'  => '#FFFFFF',
            'icon'        => 'wallet',
        ],
        'card' => [
            'label'       => 'Credit / Debit Card',
            'sub'         => 'Pay inline — no redirect',
            'brand_color' => '#635BFF',
            'brand_text'  => '#FFFFFF',
            'icon'        => 'card',
        ],
        'online_banking' => [
            'label'       => 'Online Banking',
            'sub'         => 'Log in to your bank to approve',
            'brand_color' => '#1B1F2C',
            'brand_text'  => '#FFFFFF',
            'icon'        => 'bank',
        ],
        'otc' => [
            'label'       => 'Over-the-Counter',
            'sub'         => 'Pay cash at a branch — reference issued',
            'brand_color' => '#F5A800',
            'brand_text'  => '#12151F',
            'icon'        => 'store',
        ],
    ];

    public const ONLINE_BANKS = [
        'bpi'            => 'BPI',
        'bdo'            => 'BDO',
        'unionbank'      => 'UnionBank',
        'metrobank'      => 'Metrobank',
        'chinabank'      => 'China Bank',
        'pnb'            => 'Philippine National Bank',
        'rcbc'           => 'RCBC',
        'security_bank'  => 'Security Bank',
    ];

    public const OTC_CHANNELS = [
        '7eleven'        => '7-Eleven',
        'mlhuillier'     => 'MLhuillier',
        'cebuana'        => 'Cebuana Lhuillier',
        'palawan'        => 'Palawan Express',
    ];

    public function __construct(private readonly XenditService $xendit)
    {
    }

    /**
     * Show the dedicated payment screen — the customer picks a method here.
     */
    public function show(string $ref): View
    {
        $order = Order::where('ref', $ref)->first();

        if (!$order) {
            return redirect()->route('cart.index')->with('status', 'Order not found.');
        }

        if ($order->isPaid() || $order->payment_status === 'pending_cod') {
            return redirect()->route('confirmation.show', ['ref' => $order->ref]);
        }

        return view('pages.payment', [
            'order'    => $order,
            'methods'  => self::METHODS,
            'banks'     => self::ONLINE_BANKS,
            'channels'  => self::OTC_CHANNELS,
            'xenditConfigured' => $this->xendit->isConfigured(),
            'xenditSandbox'    => $this->xendit->isSandbox(),
        ]);
    }

    /**
     * "Pay" button click — routes to real Xendit test mode (if configured and
     * the method is supported by Xendit PH) or falls back to the mock gateway.
     */
    public function pay(Request $request, string $ref)
    {
        $order = Order::where('ref', $ref)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('status', 'Order not found.');
        }
        if ($order->isPaid()) {
            return redirect()->route('confirmation.show', ['ref' => $order->ref]);
        }

        $data = $request->validate([
            'method_type' => ['required', 'in:gcash,maya,card,online_banking,otc'],
            'bank'         => ['nullable', 'string'],
            'channel'      => ['nullable', 'string'],
            'card_number'  => ['nullable', 'string'],
            'card_exp'     => ['nullable', 'string'],
            'card_cvc'     => ['nullable', 'string'],
        ]);

        $methodKey = $data['method_type'];

        // Persist the chosen method on the order
        $order->update([
            'payment_method_type' => $methodKey,
            'payment_status'      => 'awaiting_payment_method',
            'payment_ref'         => 'MOCK-' . strtoupper(substr(md5(uniqid('', true)), 0, 12)),
        ]);

        // Resolve brand metadata for the connecting screen
        $meta = self::METHODS[$methodKey];
        $bankLabel = $data['bank'] ? (self::ONLINE_BANKS[$data['bank']] ?? null) : null;
        $channelLabel = $data['channel'] ? (self::OTC_CHANNELS[$data['channel']] ?? null) : null;

        // --- Try real Xendit if configured + method is supported ---
        if ($this->xendit->isConfigured() && $this->xendit->supportsRealGateway($methodKey)) {
            try {
                $successUrl = route('payment.return', ['ref' => $order->ref, 'method' => $methodKey]);
                $cancelUrl  = route('payment.show', ['ref' => $order->ref]);

                $subOptions = [];
                if ($methodKey === 'otc') {
                    $subOptions['channel'] = $data['channel'] ?? '7eleven';
                    $subOptions['customer_name'] = $order->first_name . ' ' . $order->last_name;
                }

                $pr = $this->xendit->createPaymentRequest(
                    amount: $order->total,
                    referenceId: $order->ref,
                    methodKey: $methodKey,
                    successUrl: $successUrl,
                    cancelUrl: $cancelUrl,
                    subOptions: $subOptions,
                );

                // Persist the Xendit payment request id so the return handler can verify status
                $order->update([
                    'payment_intent_id' => $pr['id'],
                    'payment_status'    => $pr['status'],  // REQUIRES_ACTION or PENDING
                    'payment_ref'       => $pr['id'],
                ]);

                if (!empty($pr['redirect_url'])) {
                    // Show the "Connecting to {wallet}…" intermediate screen,
                    // which then auto-redirects to Xendit after ~1.6s.
                    return view('pages.connecting', [
                        'order'        => $order,
                        'method'       => $methodKey,
                        'meta'         => $meta,
                        'bankLabel'    => $bankLabel,
                        'channelLabel' => $channelLabel,
                        'redirect_url' => $pr['redirect_url'],
                        'gateway'      => 'xendit',
                        'businessName' => 'Danielle\'s Crafted Pizza',
                    ]);
                }

                // OTC may go straight to PENDING (no redirect — reference issued)
                if ($pr['status'] === 'PENDING' || $pr['status'] === 'SUCCEEDED') {
                    return redirect()->route('payment.return', ['ref' => $order->ref, 'method' => $methodKey]);
                }

                // No redirect URL and not pending — show the mock as fallback
                Log::warning('Xendit: no redirect URL in response, falling back to mock', [
                    'order' => $order->ref,
                    'pr_id' => $pr['id'],
                    'status' => $pr['status'],
                ]);
            } catch (\Throwable $e) {
                Log::error('Xendit createPaymentRequest failed, falling back to mock', [
                    'order' => $order->ref,
                    'error' => $e->getMessage(),
                ]);
                // Fall through to mock gateway
            }
        }

        // --- Mock gateway fallback ---
        // Show the same "Connecting to {wallet}…" intermediate screen, but
        // redirect to our mock gateway page after the brief delay.
        $mockUrl = route('mock.show', ['ref' => $order->ref]) . '?' . http_build_query(array_filter([
            'method'  => $methodKey,
            'bank'    => $data['bank'] ?? null,
            'channel' => $data['channel'] ?? null,
        ]));

        return view('pages.connecting', [
            'order'        => $order,
            'method'       => $methodKey,
            'meta'         => $meta,
            'bankLabel'    => $bankLabel,
            'channelLabel' => $channelLabel,
            'redirect_url' => $mockUrl,
            'gateway'      => 'mock',
            'businessName' => 'Danielle\'s Crafted Pizza',
        ]);
    }

    /**
     * Return URL — Xendit redirects here after the customer completes (or
     * cancels) payment on the wallet/bank/OTC page.
     *
     * We retrieve the PaymentRequest status from Xendit to know whether
     * the payment actually succeeded.
     */
    public function return(Request $request, string $ref)
    {
        $order = Order::where('ref', $ref)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('status', 'Order not found.');
        }

        // Verify with Xendit if we have a payment intent id and the service is configured
        if ($this->xendit->isConfigured() && $order->payment_intent_id) {
            try {
                $pr = $this->xendit->retrievePaymentRequest($order->payment_intent_id);
                $status = $pr['status'];

                $orderStatus = match ($status) {
                    'SUCCEEDED', 'COMPLETED'                  => 'paid',
                    'FAILED', 'EXPIRED', 'VOIDED'             => 'failed',
                    'PENDING', 'REQUIRES_ACTION', 'AWAITING_CAPTURE' => 'awaiting_payment_method',
                    default                                    => $order->payment_status,
                };
                $order->update(['payment_status' => $orderStatus]);

                // Send the confirmation email now that we know the payment method
                // and the order is in a final state (paid or failed).
                if ($order->isPaid() && !$order->email_sent) {
                    $this->sendOrderEmail($order);
                }
            } catch (\Throwable $e) {
                Log::warning('Xendit return: retrievePaymentRequest failed', [
                    'order' => $order->ref,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the redirect — let the customer see the confirmation page with pending status
            }
        }

        return redirect()->route('confirmation.show', ['ref' => $order->ref]);
    }

    /**
     * Webhook endpoint (called by Xendit server-to-server).
     *
     * Xendit sends the webhook verification token in the `X-CALLBACK-TOKEN`
     * header. We verify it (if configured) then update the order based on
     * the event status.
     */
    public function webhook(Request $request)
    {
        $callbackToken = $request->header('X-CALLBACK-TOKEN', '');

        if (!$this->xendit->verifyWebhook($callbackToken)) {
            Log::warning('Xendit webhook: invalid callback token', [
                'has_token' => !empty($callbackToken),
            ]);
            return response()->json(['error' => 'Invalid callback token'], 401);
        }

        $payload = $request->all();
        $status = $payload['status'] ?? '';
        $referenceId = $payload['reference_id'] ?? '';

        Log::info('Xendit webhook received', [
            'reference_id' => $referenceId,
            'status'       => $status,
        ]);

        if ($referenceId) {
            $order = Order::where('ref', $referenceId)->first();
            if ($order) {
                $orderStatus = match ($status) {
                    'SUCCEEDED', 'COMPLETED'       => 'paid',
                    'FAILED', 'EXPIRED', 'VOIDED'  => 'failed',
                    'PENDING', 'REQUIRES_ACTION'   => 'awaiting_payment_method',
                    default                         => null,
                };
                if ($orderStatus) {
                    $order->update(['payment_status' => $orderStatus]);
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    /* ----------------------------------------------------------------
     * Mock gateway (fallback)
     * -------------------------------------------------------------- */

    /**
     * The mock gateway page — looks like a real wallet/bank checkout screen.
     * Used when Xendit is not configured, or for methods not supported by
     * Xendit PH (cards, online banking).
     */
    public function mockShow(Request $request, string $ref): View
    {
        $order = Order::where('ref', $ref)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('status', 'Order not found.');
        }

        $method = $request->query('method', $order->payment_method_type ?? 'gcash');
        if (!isset(self::METHODS[$method])) {
            $method = 'gcash';
        }

        $meta = self::METHODS[$method];

        // Resolve label for the chosen bank / OTC channel
        $bankLabel = null;
        $channelLabel = null;
        if ($method === 'online_banking') {
            $bk = $request->query('bank', 'bpi');
            $bankLabel = self::ONLINE_BANKS[$bk] ?? 'BPI';
        }
        if ($method === 'otc') {
            $ch = $request->query('channel', '7eleven');
            $channelLabel = self::OTC_CHANNELS[$ch] ?? '7-Eleven';
        }

        return view('pages.mock-gateway', [
            'order'        => $order,
            'method'       => $method,
            'meta'         => $meta,
            'bankLabel'    => $bankLabel,
            'channelLabel' => $channelLabel,
            'businessName' => 'Danielle\'s Crafted Pizza',
        ]);
    }

    /**
     * POST from the mock gateway page — user clicked Approve or Decline.
     */
    public function mockComplete(Request $request, string $ref)
    {
        $order = Order::where('ref', $ref)->first();
        if (!$order) {
            return redirect()->route('cart.index')->with('status', 'Order not found.');
        }

        $data = $request->validate([
            'result' => ['required', 'in:approve,decline'],
        ]);

        if ($data['result'] === 'approve') {
            $order->update(['payment_status' => 'paid']);
            // Send the confirmation email now that payment is complete
            // and the payment_method_type is set.
            $this->sendOrderEmail($order);
        } else {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('payment.show', ['ref' => $order->ref])
                ->with('status', 'Payment was declined. Please try a different method.');
        }

        return redirect()->route('confirmation.show', ['ref' => $order->ref]);
    }

    /**
     * Send the order confirmation email to the customer.
     *
     * Idempotent — only sends once per order (guarded by the email_sent column).
     * Failures are logged but don't block the customer from seeing the
     * confirmation page.
     */
    private function sendOrderEmail(Order $order): void
    {
        if ($order->email_sent) {
            return;
        }

        // Refresh the order from DB to make sure we have the latest
        // payment_method_type + payment_status.
        $order->refresh();

        try {
            Mail::to($order->email)->send(new OrderConfirmation($order));
            $order->update(['email_sent' => true]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send order confirmation email', [
                'order'  => $order->ref,
                'email'  => $order->email,
                'error'  => $e->getMessage(),
            ]);
        }
    }
}
