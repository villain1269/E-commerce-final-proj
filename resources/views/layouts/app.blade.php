<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', "Danielle's Crafted Pizza | Dough, Boxes & Supplies for Pizza Sellers")</title>
    <meta name="description" content="@yield('description', 'Pizza dough, bases, packaging, equipment and ready-made pizza for home bakers, food entrepreneurs and pizza lovers.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body class="@yield('body-class')">
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.toast')

    <script src="{{ asset('js/dcp.js') }}"></script>
    @stack('scripts')
</body>
</html>
