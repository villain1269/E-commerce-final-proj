<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function confirmation(Request $request)
    {
        // Try query string first (set by the payment screen), then fall back to session
        $ref = $request->query('ref') ?: session('last_order_ref');

        if (!$ref) {
            return view('pages.confirmation', ['order' => null]);
        }

        $order = Order::with('items')->where('ref', $ref)->first();

        return view('pages.confirmation', ['order' => $order]);
    }
}
