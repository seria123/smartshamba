<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartShamba') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            <aside class="sidebar" aria-label="Primary">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">SS</span>
                    <span>SmartShamba</span>
                </a>

                <nav class="nav-list">
                    <span class="nav-item is-active">Foundation</span>
                    <span class="nav-item">Modules</span>
                    <span class="nav-item">Settings</span>
                </nav>
            </aside>

            <main class="main-content">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </body>
</html>
