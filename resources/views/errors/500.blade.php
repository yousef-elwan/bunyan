<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.error_500_title') }} - {{ config('app.name', 'Laravel') }}</title>

    @vite('resources/css/app.css')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>

<body class="bg-white flex items-center justify-center min-h-screen">

    <div class="text-center px-6 py-12 max-w-2xl mx-auto">

        <!-- Icon - a wrench and gear icon is suitable for a server error -->
        <div>
            <svg class="mx-auto h-16 w-16 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.472-2.472a3.75 3.75 0 00-5.303-5.303L6.25 5.25l-2.472 2.472a3.75 3.75 0 005.303 5.303z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12.932 8.355c.346-.346.753-.593 1.199-.741a5.625 5.625 0 016.14 6.14c-.148.446-.395.853-.741 1.199L15.17 11.42l1.763-1.763a.75.75 0 10-1.06-1.06L14.11 9.42l-1.178-1.065z" />
            </svg>
        </div>

        <p class="mt-4 text-sm font-semibold uppercase text-gray-500 tracking-wider">
            {{ __('messages.error_500_title') }}</p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">
            {{ __('messages.error_500_heading') }}
        </h1>

        <p class="mt-4 text-base leading-7 text-gray-600">
            {{ __('messages.error_500_message') }}
        </p>

        <div class="mt-8 flex items-center justify-center gap-x-6">
            <a href="{{ url('/') }}"
                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                <i class="fas fa-home me-2"></i> {{ __('messages.error_500_button') }}
            </a>
        </div>
    </div>

</body>

</html>
