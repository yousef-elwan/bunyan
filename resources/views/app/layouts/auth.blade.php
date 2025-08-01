@php
    $direction = $defaultLang == 'ar' ? 'rtl' : 'ltr';
@endphp


<!DOCTYPE html>

<html lang="{{ $defaultLang }}" dir="{{ $direction }}"
    style="direction: {{ $direction }}; text-align: {{ $defaultLang == 'ar' ? 'right' : 'left' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $web_config['company_name'] ?? '' }} - @yield('title')
    </title>

    <link type="image/x-icon" rel="shortcut icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">
    <link type="image/x-icon" rel="icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">

    <link rel="shortcut icon" href="{{ $web_config['company_logo'] ?? '' }}">
    <link rel="apple-touch-icon-precomposed" href="{{ $web_config['company_logo'] ?? '' }}">

    @include('app.partials.meta')

    @include('app.partials.styles')


    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

    <link rel="stylesheet" href="{{ asset('style/mobileInput.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/authPage.css') }}"> 
    <link rel="stylesheet" href="{{ asset('website/home/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/mobile-drawer-nav.css') }}">

    @include('shared.partials.paramToJs')

</head>

<body>

    <div class="drawer-backdrop" id="mainDrawerBackdrop"></div>


    <div class="app-container">
        @include('app.partials.header')

        <div class="main-layout">

            <div class="auth-page-container">

                <div class="auth-page-content"> 
                    <div class="auth-page-form-side">
                        <div class="form-container">
                            @yield('content')
                        </div>
                    </div>
                    <div class="auth-page-image-side">
                    </div>
                </div>
            </div>

        </div>

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>

    <script type="module" src="{{ asset('auth/js/auth-pages.js') }}"></script>

    @stack('scripts')
    @vite(['resources/js/app.js'])
    @vite(['resources/js/alpine/app/auth/main.js'])


</body>

</html>

@php
    if (!function_exists('getLocaleDirection')) {
        function getLocaleDirection()
        {
            $rtlLocales = ['ar', 'he', 'fa', 'ur'];
            return in_array(app()->getLocale(), $rtlLocales) ? 'rtl' : 'ltr';
        }
    }

@endphp
