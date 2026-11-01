<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="SIAP AUM, layanan perizinan online Pemerintah Kabupaten Tabanan">
    <title>SIAP AUM - Perizinan Kabupaten Tabanan</title>
    <link rel="preload" href="{{ asset('assets/images/siap-aum/figma-raw-2.webp') }}" as="image" type="image/webp" fetchpriority="high">
    @vite(['resources/sass/app.scss'])
    <link href="{{ asset('assets/vendors/iconsax/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/images/siap-aum/figma-raw-3.png') }}" rel="shortcut icon" type="image/png">
    @stack('styles')
</head>
<body id="app">
    @include('components.page-loader')
    <div class="min-vh-100 d-flex flex-column">
        <div class="sa-navbar-hover-zone d-none d-lg-block" aria-hidden="true"
             onmouseenter="document.querySelector('.sa-navbar-wrap')?.classList.remove('is-hidden')"></div>
        <div class="sa-navbar-wrap d-none d-lg-block">@include('layouts.landing.partials.navbar-dekstop')</div>
        @include('layouts.landing.partials.navbar-mobile')
        @yield('content')
        <div class="mt-auto">@include('layouts.landing.partials.footer')</div>
    </div>
    <script src="{{ asset('assets/vendors/jquery/jquery-3.4.1.min.js') }}"></script>
    @vite(['resources/js/app.js', 'resources/js/landing/landing.js'])
    @include('components.toastr')
</body>
</html>
