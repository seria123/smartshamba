<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'SmartShamba')</title>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="flex bg-gray-100 font-sans">

        <!-- TOP NAVBAR -->
        <nav class="bg-emerald-700 text-white shadow-lg w-full h-16 fixed top-0 left-0 z-50">
            <div class="flex items-center justify-between h-full px-6">
                
                <!-- Left: Logo + Mobile Menu Toggle -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Sidebar Toggle -->
                    <button id="sidebarToggle" class="lg:hidden text-white hover:text-emerald-200 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-emerald-800 rounded-full flex items-center justify-center">
                            <i class="fas fa-leaf text-xl"></i>
                        </div>
                        <span class="text-xl font-bold hidden sm:block">SmartShamba</span>
                    </a>
                </div>

                <!-- Center: Search Bar (hidden on mobile) -->
                <div class="hidden md:flex flex-1 max-w-md mx-6">
                    <div class="relative w-full">
                        <input type="text" id="navbarSearch" 
                            class="w-full pl-10 pr-4 py-2 rounded-lg bg-emerald-800 border border-emerald-600 text-white placeholder-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent"
                            placeholder="Search farms, crops, sensors...">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-search text-emerald-300"></i>
                        </div>
                    </div>
                </div>

                <!-- Right: Notifications + User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notificationBell" class="relative p-2 hover:bg-emerald-800 rounded-full transition">
                            <i class="fas fa-bell text-xl"></i>
                            @php
                                $unreadCount = \App\Models\Alert::where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                            </div>
                            <div id="notificationList">
                                @php
                                    $recentAlerts = \App\Models\Alert::with('sensorReading.sensor.field')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                @forelse($recentAlerts as $alert)
                                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 {{ !$alert->is_read ? 'bg-blue-50' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="mt-1">
                                                @if($alert->severity == 'high')
                                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                @elseif($alert->severity == 'medium')
                                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                                @else
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 truncate">{{ $alert->message }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-2xl mb-2"></i>
                                        <p>No notifications</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-2 border-t border-gray-200">
                                <a href="{{ route('alerts.index') }}" class="block text-center text-sm text-emerald-700 hover:text-emerald-800 font-medium py-2">
                                    View All Alerts
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center space-x-2 hover:bg-emerald-800 rounded-lg px-3 py-2 transition">
                            <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span class="hidden sm:block font-medium">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm text-gray-600">Signed in as</p>
                                <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user-circle mr-2"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-gray-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- SIDEBAR + MAIN CONTENT WRAPPER -->
        <div class="flex pt-16">
            
            <!-- SIDEBAR (Desktop) -->
            <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-gray-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 pt-6 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Menu</h3>
                    
                    <nav class="space-y-2">
                        <!-- Core Features (All authenticated users) -->
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('farms.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-tractor w-5"></i>
                            <span>Farms</span>
                        </a>
                        
                        <a href="{{ route('fields.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-seedling w-5"></i>
                            <span>Fields</span>
                        </a>
                        
                        <a href="{{ route('crops.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-wheat-awn w-5"></i>
                            <span>Crops</span>
                        </a>

                        <a href="{{ route('crop_analysis.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-robot w-5"></i>
                            <span>AI Crop Analysis</span>
                        </a>

                        <a href="{{ route('sensors.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-satellite-dish w-5"></i>
                            <span>Sensors</span>
                        </a>

                        <a href="{{ route('alerts.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-bell w-5"></i>
                            <span>Alerts</span>
                            @php
                                $unreadAlerts = \App\Models\Alert::where('is_read', false)->count();
                            @endphp
                            @if($unreadAlerts > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadAlerts }}</span>
                            @endif
                        </a>

                        <a href="{{ route('irrigation.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-tint w-5"></i>
                            <span>Irrigation</span>
                        </a>

                        <a href="{{ route('automation.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-robot w-5"></i>
                            <span>Automation</span>
                        </a>

                        <a href="{{ route('automation_rules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-sliders-h w-5"></i>
                            <span>Automation Rules</span>
                        </a>

                        <a href="{{ route('tasks.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-tasks w-5"></i>
                            <span>Tasks</span>
                        </a>

                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Reports</span>
                        </a>

                        <!-- Livestock Section -->
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 mt-6">Livestock</h3>

                        <a href="{{ route('livestock.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-cow w-5"></i>
                            <span>Livestock</span>
                        </a>

                        <a href="{{ route('livestock-types.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-paw w-5"></i>
                            <span>Livestock Types</span>
                        </a>

                        <a href="{{ route('feed_types.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                            <i class="fas fa-sack w-5"></i>
                            <span>Feed Types</span>
                        </a>

                        @auth
                            @if(Auth::user()->isAdmin())
                                <!-- Admin Section Divider -->
                                <hr class="border-emerald-800 my-4">

                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Admin Panel</h3>

                                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-chart-bar w-5"></i>
                                    <span>Admin Dashboard</span>
                                </a>

                                <a href="{{ route('workers.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-users w-5"></i>
                                    <span>Workers</span>
                                </a>

                                <a href="{{ route('expenses.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-money-bill-wave w-5"></i>
                                    <span>Expenses</span>
                                </a>

                                <a href="{{ route('revenues.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-dollar-sign w-5"></i>
                                    <span>Revenues</span>
                                </a>

                                <a href="{{ route('food-stocks.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-boxes w-5"></i>
                                    <span>Food Stocks</span>
                                </a>

                                <a href="{{ route('harvests.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-basket-shopping w-5"></i>
                                    <span>Harvests</span>
                                </a>

                                <a href="{{ route('buyers.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-handshake w-5"></i>
                                    <span>Buyers</span>
                                </a>

                                <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-emerald-800 transition group">
                                    <i class="fas fa-clipboard-list w-5"></i>
                                    <span>Orders</span>
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </aside>

            <!-- Overlay for mobile sidebar -->
            <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 p-6 w-full">
                @yield('content')
            </main>

        </div>

        <!-- JavaScript for sidebar toggle and dropdowns -->
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            // Notification dropdown
            document.getElementById('notificationBell').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('notificationDropdown').classList.toggle('hidden');
                document.getElementById('userDropdown').classList.add('hidden');
            });

            // User menu dropdown
            document.getElementById('userMenuButton').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('userDropdown').classList.toggle('hidden');
                document.getElementById('notificationDropdown').classList.add('hidden');
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.getElementById('notificationDropdown').classList.add('hidden');
                document.getElementById('userDropdown').classList.add('hidden');
            });

            // Prevent dropdown from closing when clicking inside
            document.getElementById('notificationDropdown').addEventListener('click', function(e) {
                e.stopPropagation();
            });
            document.getElementById('userDropdown').addEventListener('click', function(e) {
                e.stopPropagation();
            });
        </script>

        <!-- Page-specific scripts -->
        @stack('scripts')

    </body>
</html>
