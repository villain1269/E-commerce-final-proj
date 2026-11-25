<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Xendit API wrapper for the e-commerce checkout flow.
 *
 * Docs: https://developers.xendit.co/api-reference/
 *
 * Sandbox / test mode is enabled by using keys that start with
 * xnd_development_ or xnd_public_development_ or xnd_secret_development_.
 * Production keys start with xnd_production_ / xnd_public_production_ /
 * xnd_secret_production_.
 *
 * Authentication: Xendit uses HTTP Basic Auth with the secret key as the
 * username and an empty password. This class handles that for you.
 *
 * Amounts: Xendit takes amounts in CENTAVOS (₱100 = 10000).
 */
class XenditService
{
    private const BASE_URL = 'https://api.xendit.co';

    /**
     * Maps our internal method types to Xendit payment_method structures.
     *
     * NOTE on PH channel availability as of late 2026:
     * - GCASH ewallet — supported
     * - GRABPAY ewallet — supported
     * - PAYMAYA ewallet — supported (channel_code = PAYMAYA, requires cancel_return_url)
     * - OVER_THE_COUNTER with 7ELEVEN / PALAWAN / MLHUILLIER / CEBUANA — supported
     * - CARD — needs Xendit.js client-side tokenization (we redirect to a mock for cards)
     * - Online banking (BPI/BDO/UnionBank) — Xendit's PH bank transfer channels are limited
     *   and most require pre-activation. We fall back to a mock for these.
     */
    public const METHOD_MAP = [
        'gcash' => [
            'payment_method_type' => 'EWALLET',
            'channel_code'        => 'GCASH',
            'requires_cancel_url' => false,
            'requires_customer'    => false,
            'real_gateway'         => true,
        ],
        'maya' => [
            'payment_method_type' => 'EWALLET',
            'channel_code'        => 'PAYMAYA',
            'requires_cancel_url' => true,
            'requires_customer'    => false,
            'real_gateway'         => true,
        ],
        'grabpay' => [
            'payment_method_type' => 'EWALLET',
            'channel_code'        => 'GRABPAY',
            'requires_cancel_url' => false,
            'requires_customer'    => false,
            'real_gateway'         => true,
        ],
        // Card needs Xendit.js for tokenization — handled separately
        'card' => [
            'real_gateway' => false,
        ],
        // Online banking channels via Xendit in PH require activation — fall back to mock
        'online_banking' => [
            'real_gateway' => false,
        ],
        // OTC — supported, but requires amount + customer_name in channel_properties
        'otc' => [
            'payment_method_type'   => 'OVER_THE_COUNTER',
            'requires_cancel_url'   => false,
            'requires_customer'      => true,
            'real_gateway'           => true,
            'channels' => [
                '7eleven'     => '7ELEVEN',
                'mlhuillier'  => 'MLHUILLIER',
                'cebuana'     => 'CEBUANA',
                'palawan'     => 'PALAWAN',
            ],
        ],
    ];

    /** Online banking channels that are actually supported by Xendit PH (subset). */
    public const ONLINE_BANK_CHANNELS = [
        'bpi'            => 'BPI',
        'bdo'            => 'BDO',
        'unionbank'      => 'UBP',
        'metrobank'      => 'MBT',
        'chinabank'      => 'CBC',
        'pnb'            => 'PNB',
        'rcbc'           => 'RCBC',
        'security_bank'  => 'SECBC',
    ];

    public function __construct(
        private readonly ?string $secretKey = null,
    ) {
        $this->secretKey ??= env('XENDIT_SECRET_KEY', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey)
            && str_starts_with($this->secretKey, 'xnd_');
    }

    public function isSandbox(): bool
    {
        return str_contains($this->secretKey ?? '', 'development');
    }

    /**
     * True if this method is supported via real Xendit test-mode API calls
     * (vs falling back to the mock gateway).
     */
    public function supportsRealGateway(string $methodKey): bool
    {
        return self::METHOD_MAP[$methodKey]['real_gateway'] ?? false;
    }

    /* ----------------------------------------------------------------
     * API helpers
     * -------------------------------------------------------------- */

    /**
     * Create a PaymentRequest for an order.
     *
     * Returns:
     *   - id:           Xendit payment request ID (pr-xxxx)
     *   - status:       REQUIRES_ACTION | PENDING | SUCCEEDED | FAILED
     *   - redirect_url: URL the customer should be sent to (for REQUIRES_ACTION)
     *   - actions:      raw actions array
     *
     * @param int    $amount         Peso amount (NOT centavos — converted internally)
     * @param string $referenceId    Our order reference (DCP-xxxx)
     * @param string $methodKey      Our internal method key (gcash/maya/grabpay/otc)
     * @param string $successUrl     Where Xendit sends the customer after approval
     * @param string $cancelUrl     Where Xendit sends the customer if they cancel
     * @param array  $subOptions    ['channel' => '7eleven'] for OTC, ['customer_name' => '...']
     * @return array{id: string, status: string, redirect_url: ?string, actions: array, raw: array}
     */
    public function createPaymentRequest(
        int $amount,
        string $referenceId,
        string $methodKey,
        string $successUrl,
        string $cancelUrl,
        array $subOptions = [],
    ): array {
        $config = self::METHOD_MAP[$methodKey] ?? null;
        if (!$config) {
            throw new \RuntimeException("Unsupported payment method: {$methodKey}");
        }

        $paymentMethod = [
            'type'         => $config['payment_method_type'],
            'reusability'  => 'ONE_TIME_USE',
        ];

        // Build the channel-specific block
        $lowerKey = strtolower($config['payment_method_type']);

        // For OTC, channel_code is determined per-channel (7ELEVEN, MLHUILLIER, etc.)
        // For ewallets, channel_code is fixed (GCASH, PAYMAYA, GRABPAY)
        $channelCode = $config['channel_code'] ?? null;
        if ($methodKey === 'otc') {
            $chosenChannel = $subOptions['channel'] ?? '7eleven';
            $channelCode = $config['channels'][$chosenChannel] ?? '7ELEVEN';
        }

        $channelBlock = [
            'channel_code' => $channelCode,
            'channel_properties' => [
                'success_return_url' => $successUrl,
                'failure_return_url' => $successUrl,  // same page; we'll detect status from URL param
            ],
        ];
        if (!empty($config['requires_cancel_url'])) {
            $channelBlock['channel_properties']['cancel_return_url'] = $cancelUrl;
        }
        if ($methodKey === 'otc') {
            $channelBlock['channel_properties']['amount'] = $amount * 100;
            $channelBlock['channel_properties']['customer_name'] = $subOptions['customer_name'] ?? 'Customer';
        }
        $paymentMethod[$lowerKey] = $channelBlock;

        $body = [
            'reference_id'    => $referenceId,
            'type'            => 'PAY',
            'country'         => 'PH',
            'currency'        => 'PHP',
            'amount'          => $amount * 100,
            'payment_method'  => $paymentMethod,
            'description'     => "Order {$referenceId} — Danielle's Crafted Pizza",
        ];

        $resp = $this->post('/payment_requests', $body);

        $actions = $resp['actions'] ?? [];
        $redirectUrl = null;
        foreach ($actions as $a) {
            if (($a['action'] ?? '') === 'AUTH' && ($a['url_type'] ?? '') === 'WEB') {
                $redirectUrl = $a['url'] ?? null;
                break;
            }
        }
        if (!$redirectUrl && !empty($actions[0]['url'])) {
            $redirectUrl = $actions[0]['url'];
        }

        return [
            'id'           => $resp['id'],
            'status'       => $resp['status'],
            'redirect_url' => $redirectUrl,
            'actions'      => $actions,
            'raw'          => $resp,
        ];
    }

    /**
     * Retrieve a PaymentRequest by id — used after the customer returns from
     * the gateway to check if payment actually succeeded.
     */
    public function retrievePaymentRequest(string $paymentRequestId): array
    {
        $resp = $this->get("/payment_requests/{$paymentRequestId}");

        $actions = $resp['actions'] ?? [];
        $redirectUrl = null;
        foreach ($actions as $a) {
            if (($a['action'] ?? '') === 'AUTH' && ($a['url_type'] ?? '') === 'WEB') {
                $redirectUrl = $a['url'] ?? null;
                break;
            }
        }

        return [
            'id'           => $resp['id'],
            'status'       => $resp['status'],
            'redirect_url' => $redirectUrl,
            'raw'          => $resp,
        ];
    }

    /**
     * Verify that an incoming webhook request really came from Xendit.
     *
     * Xendit sends a header `X-CALLBACK-TOKEN` containing your webhook
     * verification token (set in the dashboard). We compare it to the
     * value in .env. If the env value is empty, we accept all callbacks
     * (acceptable for sandbox-only setups).
     */
    public function verifyWebhook(string $callbackToken): bool
    {
        $expected = env('XENDIT_WEBHOOK_TOKEN', '');
        if (empty($expected)) {
            // No token configured — accept (sandbox only)
            return true;
        }
        return hash_equals($expected, $callbackToken);
    }

    /* ----------------------------------------------------------------
     * Internal HTTP helpers
     * -------------------------------------------------------------- */

    private function post(string $path, array $body): array
    {
        try {
            $resp = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'X-IDEMPOTENCY-KEY' => uniqid('dcp_', true),
                ])
                ->post(self::BASE_URL . $path, $body);

            if ($resp->failed()) {
                Log::error('Xendit POST failed', [
                    'path'   => $path,
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);

                $errBody = $resp->json();
                $errMsg = $errBody['message'] ?? $resp->body();
                throw new \RuntimeException('Xendit API error: ' . $errMsg, $resp->status());
            }

            return $resp->json();
        } catch (ConnectionException $e) {
            Log::error('Xendit connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Cannot reach Xendit API. Check your internet connection.', 0, $e);
        }
    }

    private function get(string $path, array $query = []): array
    {
        try {
            $resp = Http::withBasicAuth($this->secretKey, '')
                ->get(self::BASE_URL . $path . '?' . http_build_query($query));

            if ($resp->failed()) {
                Log::error('Xendit GET failed', [
                    'path'   => $path,
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);

                $errBody = $resp->json();
                $errMsg = $errBody['message'] ?? $resp->body();
                throw new \RuntimeException('Xendit API error: ' . $errMsg, $resp->status());
            }

            return $resp->json();
        } catch (ConnectionException $e) {
            Log::error('Xendit connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Cannot reach Xendit API. Check your internet connection.', 0, $e);
        }
    }
}
