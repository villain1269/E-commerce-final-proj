<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Secure Checkout | Danielle\'s Crafted Pizza')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body data-layout="checkout">
    @include('partials.header-checkout')

    <main id="checkout-wrap">
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.toast')

    <script src="{{ asset('js/dcp.js') }}"></script>
    @stack('scripts')
</body>
</html>
