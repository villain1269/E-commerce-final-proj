@extends('layouts.app')

@section('title', 'Secure Checkout | Danielle\'s Crafted Pizza')

@section('content')
    <div class="container" style="padding:3rem 0 4rem">
        <div class="empty-state">
            <h2>Your cart is empty</h2>
            <p>Add something before checking out.</p>
            <a class="btn" href="{{ route('shop') }}">Go to Shop</a>
        </div>
    </div>
@endsection
