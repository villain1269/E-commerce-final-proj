@extends('layouts.app')

@section('title', 'Starter Kit Builder | Danielle\'s Crafted Pizza')

@section('content')
    <main class="starter-kit-page">
        <div class="page-title">
            <span class="tag-label">Starter Kit Builder</span>
            <h1>Start your pizza business in one click</h1>
            <p>Tell us your business type and budget. We'll recommend a starter package of dough, boxes and equipment with an estimated total.</p>
        </div>

        @if (session('status'))
            <div class="sandbox-banner" style="background:#fdecea;border-left-color:var(--red);color:#6e1f1a">
                {{ session('status') }}
            </div>
        @endif

        <div class="starter-kit-layout">
            <!-- LEFT: Form -->
            <div class="kit-form-card">
                <h2>Build Your Kit</h2>
                <form id="kit-form" method="post" action="{{ route('starter-kit.recommend') }}" novalidate>
                    @csrf
                    <fieldset>
                        <legend>Business Type</legend>
                        <div class="option-row">
                            @foreach ($kitBusinessLabels as $value => $label)
                                <label>
                                    <input type="radio" name="businessType" value="{{ $value }}" {{ old('businessType') === $value ? 'checked' : '' }}>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Budget</legend>
                        <div class="option-row">
                            <label>
                                <input type="radio" name="budget" value="3000" {{ old('budget') === '3000' ? 'checked' : '' }}>
                                <span>₱3,000</span>
                            </label>
                            <label>
                                <input type="radio" name="budget" value="5000" {{ old('budget') === '5000' ? 'checked' : '' }}>
                                <span>₱5,000</span>
                            </label>
                            <label>
                                <input type="radio" name="budget" value="10000" {{ old('budget') === '10000' ? 'checked' : '' }}>
                                <span>₱10,000</span>
                            </label>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-large" style="width:100%">Recommend My Kit</button>
                </form>
            </div>

            <!-- RIGHT: Result -->
            <div class="kit-result-card" id="kit-result">
                @if ($kit)
                    @php $kitData = $kit; @endphp
                    <span class="tag">{{ $kitData['business_label'] }} · ₱{{ number_format($kitData['budget']) }} budget</span>
                    <h3>{{ $kitData['kit']['name'] }}</h3>
                    <p>{{ $kitData['kit']['note'] }}</p>
                    <div class="table-wrap">
                        <table class="kit-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="num">Qty</th>
                                    <th class="num">Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kitData['items'] as $i)
                                    <tr>
                                        <td>
                                            <a href="{{ route('product.show', ['id' => $i['product']->public_id]) }}">{{ $i['product']->name }}</a>
                                            <span class="product-cat">{{ $i['product']->category->label }}</span>
                                        </td>
                                        <td class="num">{{ $i['qty'] }}</td>
                                        <td class="num">{{ $i['product']->peso($i['qty']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Estimated Total</td>
                                    <td class="num">{{ \App\Helpers\Format::peso($kitData['total']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="note ok">{{ \App\Helpers\Format::peso($kitData['left']) }} left in your budget.</p>
                    <form method="post" action="{{ route('starter-kit.recommend') }}">
                        @csrf
                        <input type="hidden" name="businessType" value="{{ $kitData['type'] }}">
                        <input type="hidden" name="budget" value="{{ $kitData['budget'] }}">
                        <input type="hidden" name="add_to_cart" value="1">
                        <button class="btn btn-large" type="submit">Add Entire Kit To Cart</button>
                    </form>
                @else
                    <div class="kit-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" style="margin:0 auto 1rem;display:block">
                            <path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/>
                        </svg>
                        <h3>Your recommended kit will appear here</h3>
                        <p style="margin:0">Pick a business type and budget, then click "Recommend My Kit".</p>
                    </div>
                @endif
            </div>
        </div>

        <p style="text-align:center;margin-top:2.5rem">
            <a href="{{ route('home') }}" style="font-size:.9rem;color:var(--muted)">← Back to Home</a>
            &nbsp;·&nbsp;
            <a href="{{ route('shop') }}" style="font-size:.9rem;color:var(--muted)">Browse all products →</a>
        </p>
    </main>
@endsection
