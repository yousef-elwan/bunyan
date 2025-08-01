@php
    $direction = $defaultLang == 'ar' ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>

<html lang="{{ $defaultLang }}" dir="{{ $direction }}"
    style="direction: {{ $direction }}; text-align: {{ $defaultLang == 'ar' ? 'right' : 'left' }}">

<head>

    <meta charset="utf-8">

    <title>
        {{ $web_config['company_name'] ?? '' }} - @yield('title') </title>

    <link type="image/x-icon" rel="shortcut icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">
    <link type="image/x-icon" rel="icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">

    <link rel="shortcut icon" href="{{ $web_config['company_logo'] ?? '' }}">
    <link rel="apple-touch-icon-precomposed" href="{{ $web_config['company_logo'] ?? '' }}">

    @include('app.partials.meta')

    <meta name="_token" content="{{ csrf_token() }}">

    @include('app.partials.styles')

    <link rel="stylesheet" href="{{ asset('website/home/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('style/mobileInput.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/mobile-drawer-nav.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/auth-modal.css') }}">

    @include('shared.partials.paramToJs')

    @vite(['resources/css/app.css'])

    @stack('css_or_js')

</head>

<body>

    @if (Session::has('success'))
        <div id="swal-success-message" class="hidden">{{ Session::get('success') }}</div>
    @endif

    <div class="drawer-backdrop" id="mainDrawerBackdrop"></div>

    <div class="app-container">

        @include('app.partials.header')

        <div class="main-layout">

            @include('app.partials.mobileDrawerNav')

            <main class="content-area" id="mainContentArea">
                @yield('content')
            </main>
        </div>
    </div>


    @include('app.partials.footer')

    @include('app.partials.go-top')

    @include('app.auth.auth-modal')

    @include('app.partials.scripts')


    @vite(['resources/js/app.js'])

    @stack('script')

</body>

</html>
