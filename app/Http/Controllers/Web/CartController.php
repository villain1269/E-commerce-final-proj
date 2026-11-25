<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    /**
     * Render the cart page.
     */
    public function index(): View
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $shipping = $this->cart->shippingFor($subtotal, 'standard');
        $total = $subtotal + $shipping;
        $toFree = $this->cart->amountUntilFreeShipping();

        // Upsell: products not currently in cart, take 4
        $inCartIds = $items->pluck('product.public_id')->all();
        $upsells = empty($inCartIds)
            ? Product::with('category')->limit(4)->get()
            : Product::with('category')->whereNotIn('public_id', $inCartIds)->limit(4)->get();

        return view('pages.cart', [
            'items'     => $items,
            'subtotal'  => $subtotal,
            'shipping'  => $shipping,
            'total'     => $total,
            'toFree'    => $toFree,
            'upsells'   => $upsells,
            'cartCount' => $this->cart->count(),
        ]);
    }

    /**
     * Add a product to the cart.
     * Supports both form POST (redirect) and JSON (AJAX) responses.
     */
    public function add(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $qty = max(1, min(99, (int) $request->input('qty', 1)));
        $this->cart->add($id, $qty);

        return $this->respond($request, 'Added to cart');
    }

    public function setQty(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $qty = max(1, min(99, (int) $request->input('qty', 1)));
        $this->cart->setQty($id, $qty);

        return $this->respond($request, 'Quantity updated');
    }

    public function increment(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $current = $this->cart->raw()[$id] ?? 0;
        $this->cart->setQty($id, $current + 1);

        return $this->respond($request, 'Quantity updated');
    }

    public function decrement(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $current = $this->cart->raw()[$id] ?? 1;
        $this->cart->setQty($id, $current - 1);

        return $this->respond($request, 'Quantity updated');
    }

    public function remove(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->cart->remove($id);

        return $this->respond($request, 'Item removed');
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $this->cart->clear();

        return $this->respond($request, 'Cart cleared');
    }

    /**
     * Returns JSON for AJAX, or redirect with flash for normal form POST.
     */
    private function respond(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'         => true,
                'message'    => $message,
                'cartCount'  => $this->cart->count(),
                'subtotal'   => $this->cart->subtotal(),
            ]);
        }

        return redirect()->back()->with('status', $message);
    }
}
