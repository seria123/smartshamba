<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartShamba') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 via-emerald-50 to-teal-100">
            <div class="w-full sm:max-w-md">
                <a href="/" class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L18 7.27L14.5 11.14L16.18 17L12 14.27L7.82 17L9.5 11.14L6 7.27L10.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                </a>

                <div class="bg-white rounded-2xl shadow-xl p-8 mx-4 sm:mx-0">
                    {{ $slot ?? '' }}
                </div>

                <div class="text-center mt-6 text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} SmartShamba. All rights reserved.</p>
                </div>
            </div>
        </div>
    </body>
</html>