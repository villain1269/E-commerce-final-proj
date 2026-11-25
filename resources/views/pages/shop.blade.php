@extends('layouts.app')

@section('title', 'Professional Pizza Shop | Danielle\'s Crafted Pizza')

@section('content')
    <main class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span>›</span><span class="here">All Pizza Supplies</span>
        </nav>
        <div class="page-title">
            <h1>Professional Pizza Shop</h1>
            <p id="shop-sub">@if ($search)
                    Search results for "{{ $search }}"
                @else
                    Premium bases, bulk packaging, high-temp gear, and fresh preps.
                @endif</p>
        </div>

        <button class="btn btn-outline filter-toggle" id="filter-toggle" type="button">Filters</button>

        <div class="shop-layout">
            <aside class="filter-card" id="filter-card">
                <form id="filter-form" autocomplete="off" method="get" action="{{ route('shop') }}">
                    {{-- preserve search across filters --}}
                    @if ($search) <input type="hidden" name="q" value="{{ $search }}"> @endif

                    <div class="filter-head">
                        <h3>Filter Products</h3>
                        <a class="link-red" id="clear-filters" href="{{ route('shop') }}">Clear All</a>
                    </div>
                    <div class="filter-group">
                        <h4>Category</h4>
                        <div id="filter-cats">
                            @foreach ($categories as $category)
                                <label class="check-row">
                                    <input type="checkbox" name="category[]" value="{{ $category->key }}" {{ in_array($category->key, $selectedCats) ? 'checked' : '' }}>
                                    <span>{{ $category->label }}</span>
                                    <span class="count">{{ $category->products()->count() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-row">
                            <input type="number" name="min" min="0" placeholder="₱ Min" value="{{ $min ?? '' }}" aria-label="Minimum price">
                            <span>to</span>
                            <input type="number" name="max" min="0" placeholder="₱ Max" value="{{ $max ?? '' }}" aria-label="Maximum price">
                        </div>
                    </div>
                    <div class="filter-group">
                        <h4>Best Suited For</h4>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="home" {{ in_array('home', $selectedSuit) ? 'checked' : '' }}><span>Home Baking</span></label>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="commercial" {{ in_array('commercial', $selectedSuit) ? 'checked' : '' }}><span>Commercial Pizzeria</span></label>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="bulk" {{ in_array('bulk', $selectedSuit) ? 'checked' : '' }}><span>Bulk Events</span></label>
                    </div>
                    <button type="submit" class="btn" style="margin-top:1rem">Apply Filters</button>
                </form>
            </aside>

            <section>
                <div class="results-bar" id="results-bar">
                    <span id="results-info">Showing <strong>{{ $count }}</strong> of {{ $total }} pizza item{{ $total === 1 ? '' : 's' }}</span>
                    <label class="sort-wrap">Sort By:
                        <form method="get" action="{{ route('shop') }}" id="sort-form" style="display:inline">
                            @csrf
                            @if ($search) <input type="hidden" name="q" value="{{ $search }}"> @endif
                            @foreach ($selectedCats as $c) <input type="hidden" name="category[]" value="{{ $c }}"> @endforeach
                            @foreach ($selectedSuit as $s) <input type="hidden" name="suit[]" value="{{ $s }}"> @endforeach
                            @if (is_numeric($min)) <input type="hidden" name="min" value="{{ $min }}"> @endif
                            @if (is_numeric($max)) <input type="hidden" name="max" value="{{ $max }}"> @endif
                            <select id="sort-select" name="sort" onchange="document.getElementById('sort-form').submit()">
                                <option value="best" {{ $sort === 'best' ? 'selected' : '' }}>Best Selling</option>
                                <option value="low" {{ $sort === 'low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="high" {{ $sort === 'high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name: A to Z</option>
                            </select>
                        </form>
                    </label>
                </div>

                @if ($products->isEmpty())
                    <div class="no-results">
                        <h3>No products match your filters</h3>
                        <p>Try clearing a filter or widening the price range.</p>
                    </div>
                @else
                    <div class="product-grid three">
                        @foreach ($products as $product)
                            @include('partials.product-card')
                        @endforeach
                    </div>
                @endif

                @if ($pages > 1)
                    <div class="pagination">
                        @if ($page > 1)
                            <a class="page-btn" href="{{ \App\Helpers\Format::shopPageUrl($page - 1, request()->query()) }}" aria-label="Previous page">←</a>
                        @else
                            <button class="page-btn" disabled>←</button>
                        @endif

                        @for ($i = 1; $i <= $pages; $i++)
                            <a class="page-btn {{ $i === $page ? 'active' : '' }}" href="{{ \App\Helpers\Format::shopPageUrl($i, request()->query()) }}">{{ $i }}</a>
                        @endfor

                        @if ($page < $pages)
                            <a class="page-btn" href="{{ \App\Helpers\Format::shopPageUrl($page + 1, request()->query()) }}" aria-label="Next page">→</a>
                        @else
                            <button class="page-btn" disabled>→</button>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </main>
@endsection

@push('scripts')
<script>
(function () {
    var toggle = document.getElementById('filter-toggle');
    if (toggle) {
        toggle.onclick = function () {
            document.getElementById('filter-card').classList.toggle('open');
        };
    }
})();
</script>
@endpush
