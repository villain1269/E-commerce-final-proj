<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();
        $featured = Product::with('category')->featured()->get();

        return view('pages.home', [
            'categories' => $categories,
            'featured'   => $featured,
        ]);
    }
}
