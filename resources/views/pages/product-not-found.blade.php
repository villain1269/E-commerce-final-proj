@extends('layouts.app')

@section('title', 'Product not found | Danielle\'s Crafted Pizza')

@section('content')
    <main class="container">
        <div class="empty-state" style="margin:3rem 0">
            <h2>Product not found</h2>
            <p>That item does not exist.</p>
            <a class="btn" href="{{ route('shop') }}">Back to Shop</a>
        </div>
    </main>
@endsection
