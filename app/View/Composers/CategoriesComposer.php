<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class CategoriesComposer
{
    public function compose(View $view): void
    {
        $view->with('navCategories', Category::orderBy('sort_order')->get());
    }
}
