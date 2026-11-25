<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $id)
    {
        $product = Product::with('category')
            ->where('public_id', $id)
            ->first();

        if (!$product) {
            return view('pages.product-not-found');
        }

        $related = $product->related(4);

        return view('pages.product', [
            'product'  => $product,
            'related'  => $related,
        ]);
    }
}
