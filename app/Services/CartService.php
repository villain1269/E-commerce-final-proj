<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const FREE_SHIPPING_MIN = 3000;
    public const SHIPPING_RATES = [
        'standard' => 150,
        'express'  => 250,
    ];

    /**
     * Get the cart as a collection of ['product' => Product, 'qty' => int] items.
     */
    public function items(): Collection
    {
        $raw = $this->raw();
        if (empty($raw)) {
            return collect();
        }

        $products = Product::whereIn('public_id', array_keys($raw))->get()->keyBy('public_id');

        return collect($raw)
            ->filter(fn ($qty, $id) => isset($products[$id]))
            ->map(function ($qty, $id) use ($products) {
                return ['product' => $products[$id], 'qty' => (int) $qty];
            })
            ->values();
    }

    /**
     * Raw cart array: ['p1' => 2, 'p5' => 1, ...]
     */
    public function raw(): array
    {
        return Session::get('cart', []);
    }

    public function count(): int
    {
        return collect($this->raw())->sum();
    }

    public function subtotal(): int
    {
        return $this->items()->reduce(function (int $sum, array $i) {
            return $sum + ($i['product']->price * $i['qty']);
        }, 0);
    }

    public function shippingFor(int $subtotal, string $method = 'standard'): int
    {
        if ($subtotal === 0) {
            return 0;
        }
        if ($method === 'express') {
            return self::SHIPPING_RATES['express'];
        }
        // standard
        return $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_RATES['standard'];
    }

    public function total(string $method = 'standard'): int
    {
        $sub = $this->subtotal();
        return $sub + $this->shippingFor($sub, $method);
    }

    public function amountUntilFreeShipping(): int
    {
        return max(0, self::FREE_SHIPPING_MIN - $this->subtotal());
    }

    public function add(string $productId, int $qty = 1): void
    {
        if (!Product::where('public_id', $productId)->exists()) {
            return;
        }
        $cart = $this->raw();
        $cart[$productId] = min(99, ($cart[$productId] ?? 0) + $qty);
        Session::put('cart', $cart);
    }

    public function setQty(string $productId, int $qty): void
    {
        $cart = $this->raw();
        if (!isset($cart[$productId])) {
            return;
        }
        $cart[$productId] = max(1, min(99, $qty));
        Session::put('cart', $cart);
    }

    public function remove(string $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    public function clear(): void
    {
        Session::forget('cart');
    }

    /**
     * Add a whole kit (array of [productId, qty] pairs).
     */
    public function addKit(array $items): void
    {
        foreach ($items as [$id, $qty]) {
            $this->add($id, (int) $qty);
        }
    }
}
