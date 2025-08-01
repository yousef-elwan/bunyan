@php
    $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ $direction }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>@yield('title', $web_config['company_name'] ?? '')</title>

    @include('dashboard.partials.meta')

    <link type="image/x-icon" rel="shortcut icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">
    <link type="image/x-icon" rel="icon" href="{{ $web_config['company_fav_icon'] ?? '' }}">

    @include('shared.partials.paramToJs')

    @stack('css_or_js')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('css/my-modal.css') }}">


    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>


</head>

<body class="font-sans bg-gray-100" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    sidebarHover: false
}" @resize.window="sidebarOpen = window.innerWidth >= 1024">
    {{-- ==================== الطبقة المعتمة (Overlay) ==================== --}}
    <!-- Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
    </div>
    {{-- ================================================================= --}}


    <div class="flex min-h-screen">
        {{-- الشريط الجانبي --}}
        @include('dashboard.partials.aside')

        {{-- منطقة المحتوى الرئيسية --}}
        <div class="flex-1 flex flex-col transition-all duration-300"
            :class="{
                'lg:ms-sidebar-open': sidebarOpen,
                'lg:ms-sidebar-closed': !sidebarOpen
            }">

            {{-- الهيدر --}}
            @include('dashboard.partials.header')

            {{-- المحتوى --}}
            <main class="flex-1 p-4 md:p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @include('dashboard.partials.scripts')
    @stack('script')
    @vite(['resources/js/alpine/dashboard/app.js'])
    {{-- يمكنك إزالة @vite الخاص بـ app.js إذا كنت تعتمد على CDN الخاص بـ Alpine --}}
</body>

</html>
