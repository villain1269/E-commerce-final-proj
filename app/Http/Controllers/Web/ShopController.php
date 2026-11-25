<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ShopController extends Controller
{
    private const PAGE_SIZE = 9;

    /**
     * Get an array of selected values from a query param.
     * Accepts both `?category=foo,bar` and `?category[]=foo&category[]=bar`.
     */
    private function queryList(Request $request, string $key): array
    {
        $value = $request->query($key);
        if (is_array($value)) {
            return array_values(array_filter(array_map('strval', $value), fn ($v) => $v !== ''));
        }
        if (is_string($value)) {
            return array_values(array_filter(explode(',', $value), fn ($v) => $v !== ''));
        }
        return [];
    }

    public function index(Request $request)
    {
        $categories = Category::orderBy('sort_order')->get();

        $query = Product::with('category');

        // category filter (multi-select)
        $cats = $this->queryList($request, 'category');
        if (!empty($cats)) {
            $query->whereHas('category', fn ($q) => $q->whereIn('key', $cats));
        }

        // search query
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('desc', 'like', "%{$search}%")
                  ->orWhereHas('category', fn ($c) => $c->where('label', 'like', "%{$search}%"));
            });
        }

        // price range
        $min = $request->query('min');
        $max = $request->query('max');
        if (is_numeric($min)) {
            $query->where('price', '>=', (int) $min);
        }
        if (is_numeric($max)) {
            $query->where('price', '<=', (int) $max);
        }

        // suited-for filter (multi-select)
        $suited = $this->queryList($request, 'suit');
        if (!empty($suited)) {
            $query->where(function ($q) use ($suited) {
                foreach ($suited as $s) {
                    $q->orWhereJsonContains('suited', $s);
                }
            });
        }

        // sort
        $sort = $request->query('sort', 'best');
        $query = match ($sort) {
            'low'   => $query->orderBy('price', 'asc'),
            'high'  => $query->orderBy('price', 'desc'),
            'name'  => $query->orderBy('name', 'asc'),
            default => $query->orderByDesc('reviews'),
        };

        $total = $query->count();
        $page = max(1, (int) $request->query('page', 1));
        $products = $query->forPage($page, self::PAGE_SIZE)->get();
        $pages = (int) ceil($total / self::PAGE_SIZE);
        if ($pages < 1) {
            $pages = 1;
        }

        return view('pages.shop', [
            'categories'     => $categories,
            'products'       => $products,
            'total'          => $total,
            'count'          => $products->count(),
            'page'           => $page,
            'pages'          => $pages,
            'pageSize'        => self::PAGE_SIZE,
            'selectedCats'   => $cats,
            'selectedSuit'   => $suited,
            'min'            => $min,
            'max'            => $max,
            'sort'           => $sort,
            'search'         => $search,
        ]);
    }
}
