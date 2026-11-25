<?php

use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\StarterKitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Danielle's Crafted Pizza (Laravel port with Xendit + mock)
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop');

// Product detail
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/qty/{id}', [CartController::class, 'setQty'])->name('cart.qty');
Route::post('/cart/inc/{id}', [CartController::class, 'increment'])->name('cart.inc');
Route::post('/cart/dec/{id}', [CartController::class, 'decrement'])->name('cart.dec');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

// Payment screen (dedicated) — appears after Place Order if customer chose "Online"
Route::get('/pay/{ref}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/pay/{ref}/pay', [PaymentController::class, 'pay'])->name('payment.pay');

// Xendit return URL (where the customer lands after GCash/Maya/OTC approval)
Route::get('/payment/return/{ref}', [PaymentController::class, 'return'])->name('payment.return');

// Xendit webhook (called by Xendit server-to-server — POST, no CSRF)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Mock payment gateway — used as fallback (no Xendit keys, or unsupported methods like card)
Route::get('/mock/{ref}', [PaymentController::class, 'mockShow'])->name('mock.show');
Route::post('/mock/{ref}/complete', [PaymentController::class, 'mockComplete'])->name('mock.complete');

// Order confirmation
Route::get('/confirmation', [OrderController::class, 'confirmation'])->name('confirmation.show');

// Starter Kit Builder — dedicated page + form POST
Route::get('/starter-kit', [StarterKitController::class, 'show'])->name('starter-kit.show');
Route::post('/starter-kit', [StarterKitController::class, 'recommend'])->name('starter-kit.recommend');

// Newsletter (no real persistence — just shows a thank-you toast)
Route::post('/newsletter', function () {
    return back()->with('status', 'Thanks for subscribing!');
})->name('newsletter.subscribe');
