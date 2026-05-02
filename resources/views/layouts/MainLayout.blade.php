<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmartShamba')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- 🔝 NAVBAR -->
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                    <span class="font-bold text-xl text-gray-900 hidden sm:block">SmartShamba</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">

                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'text-gray-600 hover:bg-gray-100 hover:text-emerald-600' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>

                    <a href="{{ route('farms.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('farms.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'text-gray-600 hover:bg-gray-100 hover:text-emerald-600' }}">
                        <i class="fas fa-warehouse mr-2"></i>Farms
                    </a>

                    <div class="relative group">
                        <button class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-emerald-600">
                            <i class="fas fa-seedling mr-2"></i>Livestock
                            <i class="fas fa-chevron-down text-xs ml-1 transition-transform group-hover:rotate-180"></i>
                        </button>
                        <div class="hidden group-hover:block absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('livestock.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-cow w-5"></i>Animals
                            </a>
                            <a href="{{ route('livestock-types.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-tags w-5"></i>Animal Types
                            </a>
                            <a href="{{ route('livestock-analysis.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-virus w-5"></i>Disease Analysis
                            </a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-emerald-600">
                            <i class="fas fa-crop-simple mr-2"></i>Crops
                            <i class="fas fa-chevron-down text-xs ml-1 transition-transform group-hover:rotate-180"></i>
                        </button>
                        <div class="hidden group-hover:block absolute top-full left-0 mt-1 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('crop_cycles.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-seedling w-5"></i>Crop Cycles
                            </a>
                            <a href="{{ route('planting-schedules.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-calendar-alt w-5"></i>Planting Schedule
                            </a>
                            <a href="{{ route('crop_analyses.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-search w-5"></i>Disease Detection
                            </a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('revenues.*') || request()->routeIs('expenses.*') || request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'text-gray-600 hover:bg-gray-100 hover:text-emerald-600' }}">
                            <i class="fas fa-chart-line mr-2"></i>Finance
                            <i class="fas fa-chevron-down text-xs ml-1 transition-transform group-hover:rotate-180"></i>
                        </button>
                        <div class="hidden group-hover:block absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('revenues.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-dollar-sign w-5"></i>Income / Sales
                            </a>
                            <a href="{{ route('expenses.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-receipt w-5"></i>Expenses
                            </a>
                            <a href="{{ route('expenses.summary') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-chart-pie w-5"></i>Profit & Loss
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-file-alt w-5"></i>Reports
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('workers.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('workers.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'text-gray-600 hover:bg-gray-100 hover:text-emerald-600' }}">
                        <i class="fas fa-users mr-2"></i>Team
                    </a>
                </div>

                <!-- User + Mobile Toggle -->
                <div class="flex items-center space-x-3">
                    <!-- User -->
                    <div class="hidden md:flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm text-gray-700 hidden lg:block">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg border border-gray-200 transition-all duration-200">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>

                    <!-- Mobile Button -->
                    <button id="menu-btn" class="md:hidden p-2 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- 📱 MOBILE MENU -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 pb-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
            <a href="{{ route('farms.index') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-warehouse"></i>Farms
            </a>
            <a href="{{ route('livestock.index') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-cow"></i>Livestock
            </a>
            <a href="{{ route('crop_cycles.index') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-seedling"></i>Crops
            </a>
            <a href="{{ route('revenues.index') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-chart-line"></i>Finance
            </a>
            <a href="{{ route('workers.index') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                <i class="fas fa-users"></i>Team
            </a>
            <div class="border-t border-gray-200 pt-2 mt-2">
                <p class="text-sm text-gray-500 px-4 mb-2">{{ Auth::user()->name ?? 'User' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <!-- Page Content -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        @yield('content')
    </main>
</body>
</html>
