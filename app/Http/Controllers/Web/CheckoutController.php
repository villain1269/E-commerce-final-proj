<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): View
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return view('pages.checkout-empty');
        }

        $subtotal = $this->cart->subtotal();
        $express  = $this->cart->shippingFor($subtotal, 'express');
        $standard = $this->cart->shippingFor($subtotal, 'standard');

        return view('pages.checkout', [
            'items'     => $items,
            'subtotal'  => $subtotal,
            'express'   => $express,
            'standard'  => $standard,
            'cartCount' => $this->cart->count(),
        ]);
    }

    /**
     * Place the order: validate the delivery form, create an Order record
     * with status=pending_payment, clear the cart, send a confirmation email
     * to the customer, then redirect to the dedicated payment screen.
     *
     * If the customer chose Cash on Delivery, we skip the payment screen
     * and go straight to confirmation.
     */
    public function placeOrder(Request $request)
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $validator = Validator::make($request->all(), [
            'firstName' => ['required', 'string', 'min:2'],
            'lastName'  => ['required', 'string', 'min:2'],
            'email'     => ['required', 'email'],
            'address'   => ['required', 'string', 'min:8'],
            'city'      => ['required', 'string', 'min:2'],
            'postal'    => ['required', 'regex:/^\d{4}$/'],
            'phone'     => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            'notes'     => ['nullable', 'string'],
            'shipping'  => ['required', 'in:express,standard'],
            'payment'   => ['required', 'in:cod,online'],
            'terms'     => ['accepted'],
        ], [
            'firstName.required' => 'Enter your first name.',
            'firstName.min'      => 'Enter your first name.',
            'lastName.required'  => 'Enter your last name.',
            'lastName.min'       => 'Enter your last name.',
            'email.required'     => 'Enter a valid email address.',
            'email.email'        => 'Enter a valid email address.',
            'address.required'   => 'Enter your full street address.',
            'address.min'        => 'Enter your full street address.',
            'city.required'      => 'Enter your city or municipality.',
            'city.min'           => 'Enter your city or municipality.',
            'postal.required'    => 'Postal code must be 4 digits.',
            'postal.regex'       => 'Postal code must be 4 digits.',
            'phone.required'     => 'Enter a valid PH mobile number (e.g. 09171234567).',
            'phone.regex'        => 'Enter a valid PH mobile number (e.g. 09171234567).',
            'shipping.required'  => 'Please choose a shipping method.',
            'shipping.in'        => 'Please choose a shipping method.',
            'payment.required'   => 'Please choose a payment method (COD or Online).',
            'payment.in'         => 'Please choose a payment method (COD or Online).',
            'terms.accepted'     => 'Please accept the order terms.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('checkout.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $subtotal = $this->cart->subtotal();
        $shipping = $this->cart->shippingFor($subtotal, $validated['shipping']);
        $total = $subtotal + $shipping;

        $order = DB::transaction(function () use ($validated, $items, $subtotal, $shipping, $total) {
            $order = Order::create([
                'ref'             => 'DCP-' . substr((string) time(), -8),
                'first_name'      => $validated['firstName'],
                'last_name'       => $validated['lastName'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'],
                'address'         => $validated['address'],
                'city'            => $validated['city'],
                'postal'          => $validated['postal'],
                'notes'           => $validated['notes'] ?? null,
                'shipping_method' => $validated['shipping'],
                'payment'         => $validated['payment'],  // 'cod' or 'online'
                'subtotal'        => $subtotal,
                'shipping'        => $shipping,
                'total'           => $total,
                'payment_status'  => $validated['payment'] === 'cod' ? 'pending_cod' : 'pending_payment',
            ]);

            foreach ($items as $i) {
                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_public_id' => $i['product']->public_id,
                    'product_name'      => $i['product']->name,
                    'product_sku'       => $i['product']->sku,
                    'qty'               => $i['qty'],
                    'price'             => $i['product']->price,
                ]);
            }

            return $order;
        });

        // Send confirmation email immediately for COD orders (no payment step).
        // For online orders, the email is sent AFTER successful payment — see PaymentController.
        if ($validated['payment'] === 'cod') {
            $this->sendOrderEmail($order);
        }

        $this->cart->clear();

        // COD orders go straight to confirmation; online orders go to the payment screen
        if ($validated['payment'] === 'cod') {
            session()->flash('last_order_ref', $order->ref);
            return redirect()->route('confirmation.show');
        }

        return redirect()->route('payment.show', ['ref' => $order->ref]);
    }

    /**
     * Send the order confirmation email to the customer.
     *
     * Uses the email address the customer entered in the delivery form.
     * If mail sending fails, we log the error but don't block the order —
     * the customer still sees the confirmation page. Idempotent — only
     * sends once per order (guarded by the email_sent column).
     */
    private function sendOrderEmail(Order $order): void
    {
        if ($order->email_sent) {
            return;
        }

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

