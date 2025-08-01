<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('errors.403.title') }} - {{ config('app.name', 'Laravel') }}</title>

    @vite('resources/css/app.css')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Using a font that supports both languages well --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>

<body class="bg-white">
    <div class="flex flex-col lg:flex-row items-center justify-center min-h-screen text-center lg:text-start px-6">

        <!-- Image Section -->
        <div class="lg:w-1/2 flex justify-center lg:justify-end lg:pr-16">
            {{-- You can use an SVG for better quality or a relevant image --}}
            {{-- This SVG is an example of a "house not found" concept --}}
            <svg class="w-64 h-64 lg:w-96 lg:h-96 text-gray-300" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"></path>
                <path
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.043 3.024a10 10 0 11-5.71 13.918A10.003 10.003 0 0110 2a10 10 0 018.905 14.864 1 1 0 11-1.81-1.09A8 8 0 1010.002 4.001a1 1 0 01-.959-1.002zM15 9a1 1 0 112 0 1 1 0 01-2 0zM7 9a1 1 0 112 0 1 1 0 01-2 0z"
                    opacity="0.4"></path>
            </svg>
        </div>

        <!-- Text Section -->
        <div class="lg:w-1/2 max-w-md">
            <p class="text-2xl font-semibold text-blue-600">403</p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900 md:text-5xl">
                {{ __('errors.403.heading') }}
            </h1>
            <p class="mt-6 text-base leading-7 text-gray-600">
                {{ __('errors.403.message') }}
            </p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                <a href="{{ url('/') }}"
                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                    <i class="fas fa-home me-2"></i> {{ __('errors.403.button') }}
                </a>
            </div>
        </div>

    </div>
</body>

</html>
