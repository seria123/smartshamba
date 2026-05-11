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
                    @auth
                        @if (auth()->user()->canAccessAdmin('core.view'))
                            <a class="nav-item {{ request()->routeIs('core.dashboard') ? 'is-active' : '' }}" href="{{ route('core.dashboard') }}">Core</a>
                            <a class="nav-item {{ request()->routeIs('core.organizations.*') ? 'is-active' : '' }}" href="{{ route('core.organizations.index') }}">Organizations</a>
                            <a class="nav-item {{ request()->routeIs('core.farms.*') ? 'is-active' : '' }}" href="{{ route('core.farms.index') }}">Farms</a>
                            <a class="nav-item {{ request()->routeIs('core.modules.*') ? 'is-active' : '' }}" href="{{ route('core.modules.index') }}">Modules</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('access.manage'))
                            <a class="nav-item {{ request()->routeIs('access.*') ? 'is-active' : '' }}" href="{{ route('access.dashboard') }}">Access</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('workers.view'))
                            <a class="nav-item {{ request()->routeIs('labour.*') ? 'is-active' : '' }}" href="{{ route('labour.dashboard') }}">Labour</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('tasks.view'))
                            <a class="nav-item {{ request()->routeIs('tasks.*') ? 'is-active' : '' }}" href="{{ route('tasks.dashboard') }}">Tasks / Work Orders</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('inventory.view'))
                            <a class="nav-item {{ request()->routeIs('inventory.*') ? 'is-active' : '' }}" href="{{ route('inventory.dashboard') }}">Inventory / Inputs</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('crops.view'))
                            <a class="nav-item {{ request()->routeIs('crops.*') ? 'is-active' : '' }}" href="{{ route('crops.dashboard') }}">Crops</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('livestock.view'))
                            <a class="nav-item {{ request()->routeIs('livestock.*') ? 'is-active' : '' }}" href="{{ route('livestock.dashboard') }}">Livestock</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('irrigation.view'))
                            <a class="nav-item {{ request()->routeIs('irrigation.*') ? 'is-active' : '' }}" href="{{ route('irrigation.dashboard') }}">Irrigation / Water</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('assets.view'))
                            <a class="nav-item {{ request()->routeIs('assets.*') ? 'is-active' : '' }}" href="{{ route('assets.dashboard') }}">Assets / Maintenance</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('finance.view'))
                            <a class="nav-item {{ request()->routeIs('finance.*') ? 'is-active' : '' }}" href="{{ route('finance.dashboard') }}">Finance / Costing</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('sales.view'))
                            <a class="nav-item {{ request()->routeIs('sales.*') ? 'is-active' : '' }}" href="{{ route('sales.dashboard') }}">Sales / Revenue</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('reports.view'))
                            <a class="nav-item {{ request()->routeIs('reports.*') ? 'is-active' : '' }}" href="{{ route('reports.dashboard') }}">Reports</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('notifications.view'))
                            <a class="nav-item {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" href="{{ route('notifications.dashboard') }}">Notifications / Alerts</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('documents.view'))
                            <a class="nav-item {{ request()->routeIs('documents.*') ? 'is-active' : '' }}" href="{{ route('documents.dashboard') }}">Documents / Attachments</a>
                        @endif
                        @if (auth()->user()->canAccessAdmin('audit.view'))
                            <a class="nav-item {{ request()->routeIs('audit.*') ? 'is-active' : '' }}" href="{{ route('audit.dashboard') }}">Audit Log</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-item logout-button" type="submit">Log out</button>
                        </form>
                    @else
                        <a class="nav-item {{ request()->routeIs('login') ? 'is-active' : '' }}" href="{{ route('login') }}">Log in</a>
                    @endauth
                </nav>
            </aside>

            <main class="main-content">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </body>
</html>
