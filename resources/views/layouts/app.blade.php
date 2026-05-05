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
                    <a class="nav-item {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Foundation</a>
                    <a class="nav-item {{ request()->routeIs('core.dashboard') ? 'is-active' : '' }}" href="{{ route('core.dashboard') }}">Core</a>
                    <a class="nav-item {{ request()->routeIs('core.organizations.*') ? 'is-active' : '' }}" href="{{ route('core.organizations.index') }}">Organizations</a>
                    <a class="nav-item {{ request()->routeIs('core.farms.*') ? 'is-active' : '' }}" href="{{ route('core.farms.index') }}">Farms</a>
                    <a class="nav-item {{ request()->routeIs('core.modules.*') ? 'is-active' : '' }}" href="{{ route('core.modules.index') }}">Modules</a>
                </nav>
            </aside>

            <main class="main-content">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </body>
</html>
