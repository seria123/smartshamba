<aside class="w-64 bg-green-800 text-white flex flex-col h-screen">

    <!-- Logo -->
    <div class="p-6 text-2xl font-bold border-b border-green-700">
        🌾 SmartShamba
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-6 overflow-y-auto">

        <!-- Main Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Main</h3>
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('dashboard') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Farm Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Farm Management</h3>

            <a href="{{ route('farms.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('farms.*') && !request()->routeIs('farms.create') && !request()->routeIs('farms.onboarding') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-list w-5"></i>
                <span>My Farms</span>
            </a>

            <a href="{{ route('farms.create') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('farms.create') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Add Farm</span>
            </a>

            @auth
                @if (!auth()->user()->farm)
                    <a href="{{ route('farms.onboarding') }}"
                       class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded bg-yellow-600 text-white hover:bg-yellow-700">
                        <i class="fas fa-seedling w-5"></i>
                        <span>Complete Setup</span>
                    </a>
                @endif
            @endauth
        </div>

        <!-- Fields Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Fields</h3>
            <a href="{{ route('fields.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('fields.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-map w-5"></i>
                <span>Manage Fields</span>
            </a>
            <a href="{{ route('fields.create') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('fields.create') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Add Field</span>
            </a>
        </div>

        <!-- Crops Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Crops</h3>
            <a href="{{ route('crop_cycles.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('crop_cycles.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-seedling w-5"></i>
                <span>Crop Management</span>
            </a>
            
            <!-- Planning Section -->
            <a href="{{ route('planting-schedules.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('planting-schedules.*') && !request()->routeIs('planting-schedules.calendar') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-calendar-alt w-5"></i>
                <span>Planting Schedule</span>
            </a>
            <a href="{{ route('planting-schedules.calendar') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('planting-schedules.calendar') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-calendar-days w-5"></i>
                <span>Crop Calendar</span>
            </a>
        </div>

        <!-- Livestock Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Livestock</h3>
            <a href="{{ route('livestock.index') }}"
                               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('livestock.*') && !in_array(request()->route()->getName(), ['livestock.types.*', 'livestock.locations.*', 'livestock.movements.*', 'livestock.grazing.*', 'livestock-analysis.*']) ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-cow w-5"></i>
                <span>Animals</span>
            </a>
            <a href="{{ route('livestock.create') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('livestock.create') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Add Animal</span>
            </a>
            <a href="{{ route('livestock-types.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('livestock-types.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-tags w-5"></i>
                <span>Animal Types</span>
            </a>
            <a href="{{ route('livestock-analysis.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('livestock-analysis.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-virus w-5"></i>
                <span>Disease Analysis</span>
            </a>
        </div>

        <!-- Finance Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Finance</h3>

            <a href="{{ route('revenues.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('revenues.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-dollar-sign w-5"></i>
                <span>Income / Sales</span>
            </a>

            <a href="{{ route('expenses.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('expenses.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-receipt w-5"></i>
                <span>Expenses</span>
            </a>

            <a href="{{ route('expenses.summary') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('expenses.summary') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Profit & Loss</span>
            </a>
        </div>

        <!-- Analytics Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Analytics</h3>
            <a href="{{ route('reports.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('reports.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-chart-line w-5"></i>
                <span>Reports</span>
            </a>
            <a href="#"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded opacity-50 cursor-not-allowed">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Yield Analysis</span>
            </a>
        </div>

    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-green-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-green-300 truncate">{{ Auth::user()->role ?? 'farmer' }}</p>
            </div>
        </div>
    </div>

</aside>
