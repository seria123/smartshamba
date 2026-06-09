<aside class="w-64 min-w-[16rem] bg-green-800 text-white flex flex-col h-screen overflow-y-auto">

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
                    <a href="{{ route('farms.create') }}"
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
            
            <!-- Crop Management Dropdown -->
            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-seedling w-5"></i>
                    <span>Crop Management</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('crop_cycles.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('crop_stages.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-layer-group text-sm"></i>
                        <span>Crop Cycle</span>
                    </a>
                    <a href="{{ route('crop_stages.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('crop_cycles.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-seedling text-sm"></i>
                        <span>Crop Stage</span>
                    </a>
                    <a href="{{ route('equipment.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('equipment.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-tools text-sm"></i>
                        <span>Equipment</span>
                    </a>
                </div>
            </div>

            <!-- Crop Maintenance Section -->
            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-spray-can w-5"></i>
                    <span>Crop Maintenance</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('spraying_schedules.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('spraying_schedules.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-spray-can text-sm"></i>
                        <span>Spraying Schedules</span>
                    </a>
                    <a href="{{ route('pest_control_schedules.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('pest_control_schedules.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-bug text-sm"></i>
                        <span>Pest Control</span>
                    </a>
                </div>
            </div>

             <!-- Planning Section -->
             <a href="{{ route('planting_schedules.index') }}"
                class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('planting_schedules.*') && !request()->routeIs('planting_schedules.calendar') ? 'bg-green-700 text-yellow-300' : '' }}">
                 <i class="fas fa-calendar-alt w-5"></i>
                 <span>Planting Schedule</span>
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
            
            <a href="{{ route('feed-types.index') }}"
                class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('feed-types.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-leaf w-5"></i>
                <span>Feed Types</span>
            </a>

<a href="{{ route('feed_usages.index') }}"
                 class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('feed-usages.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-leaf w-5"></i>
                <span>Feed Usages</span>
            </a>

            <!-- Livestock Care Section -->
            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-heartbeat w-5"></i>
                    <span>Livestock Care</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('livestock_vaccination_schedules.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('livestock_vaccination_schedules.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-syringe text-sm"></i>
                        <span>Vaccination Schedules</span>
                    </a>
                    <a href="{{ route('livestock_deworming_schedules.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('livestock_deworming_schedules.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-pills text-sm"></i>
                        <span>Deworming Schedules</span>
                    </a>
                   
                  
                </div>
            </div>

            <a href="{{ route('fumigation_schedules.dashboard') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('fumigation_schedules.*') && !request()->routeIs('fumigation_schedules.create') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-wind w-5"></i>
                <span>Fumigation Dashboard</span>
            </a>
            <a href="{{ route('fumigation_schedules.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('fumigation_schedules.index') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Fumigation Schedules</span>
            </a>
            <a href="{{ route('fumigation_schedules.create') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('fumigation_schedules.create') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-plus-circle w-5"></i>
                <span>New Fumigation</span>
            </a>

         </div>

        <!-- Team / Staff Section (for Labor expenses) -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Labor & Staff</h3>
            
            <a href="{{ route('staff.analytics.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('staff.analytics.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Analytics</span>
            </a>

            <a href="{{ route('staff.presence.fields') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('staff.presence.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-map-marker-alt w-5"></i>
                <span>Field Presence</span>
            </a>

            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-users w-5"></i>
                    <span>Manage Staff</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('staff.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('staff.index') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-list text-sm"></i>
                        <span>All Staff</span>
                    </a>
                    <a href="{{ route('staff.create') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('staff.create') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-user-plus text-sm"></i>
                        <span>Add Staff</span>
                    </a>
                    <a href="{{ route('staff.schedules.all') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('staff.schedules.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-calendar-alt text-sm"></i>
                        <span>Schedules</span>
                    </a>
                </div>
            </div>

            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-tasks w-5"></i>
                    <span>Field Management</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('fields.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('fields.*') && !request()->routeIs('fields.create') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-map text-sm"></i>
                        <span>All Fields</span>
                    </a>
                    <a href="{{ route('fields.create') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('fields.create') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-plus-circle text-sm"></i>
                        <span>Add Field</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Finance Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Finance</h3>

            <a href="{{ route('finance.dashboard') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('finance.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-wallet w-5"></i>
                <span>Finance Dashboard</span>
            </a>

            <div class="relative group">
                <button class="flex items-center space-x-2 w-full hover:bg-green-700 p-2 rounded transition-all duration-200">
                    <i class="fas fa-coins w-5"></i>
                    <span>Money Tracking</span>
                    <i class="fas fa-chevron-down text-xs ml-auto transition-transform group-hover:rotate-180"></i>
                </button>
                <div class="hidden group-hover:block pl-4 space-y-1 mt-1">
                    <a href="{{ route('revenues.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('revenues.*') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-arrow-trend-up text-sm"></i>
                        <span>Income / Sales</span>
                    </a>
                    <a href="{{ route('expenses.index') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('expenses.index') || request()->routeIs('expenses.create') || request()->routeIs('expenses.edit') || request()->routeIs('expenses.show') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-receipt text-sm"></i>
                        <span>Expenses</span>
                    </a>
                    <a href="{{ route('expenses.summary') }}"
                       class="flex items-center space-x-2 block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('expenses.summary') ? 'bg-green-700 text-yellow-300' : 'text-green-100' }}">
                        <i class="fas fa-chart-pie text-sm"></i>
                        <span>Profit &amp; Loss</span>
                    </a>
                </div>
            </div>

            <a href="{{ route('budgets.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('budgets.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Budgets</span>
            </a>

            <a href="{{ route('loans.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('loans.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-building-columns w-5"></i>
                <span>Loans &amp; Credit</span>
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
            <a href="{{ route('yield-estimations.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('yield-estimations.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Yield Analysis</span>
            </a>
            <a href="{{ route('harvests.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('harvests.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-seedling w-5"></i>
                <span>Harvest Records</span>
            </a>
        </div>

        <!-- Data Tools Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Data Tools</h3>
            <a href="{{ route('exports.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('exports.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-file-export w-5"></i>
                <span>Import &amp; Export</span>
            </a>
            <a href="{{ route('comments.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('comments.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-comments w-5"></i>
                <span>Comments</span>
            </a>
        </div>

        <!-- Customer Support Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Support</h3>
            <a href="{{ route('support-tickets.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('support-tickets.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                <i class="fas fa-headset w-5"></i>
                <span>Customer Support</span>
            </a>
        </div>

        <!-- Settings Section -->
        <div>
            <h3 class="text-xs uppercase text-green-300 font-semibold mb-2 tracking-wider">Settings</h3>
            <a href="{{ route('settings.index') }}"
               class="flex items-center space-x-2 block hover:bg-green-700 p-2 rounded {{ request()->routeIs('settings.*') ? 'bg-green-700 text-yellow-300' : '' }}">
                 <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </div>
        


    </nav>

    <!-- User Info -->
     <div class="p-4 border-t border-green-700">
         <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 hover:bg-green-700 p-2 rounded transition">
             <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                 {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
             </div>
             <div class="flex-1 min-w-0">
                 <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'User' }}</p>
                 <p class="text-xs text-green-300 truncate">{{ Auth::user()->role ?? 'user' }}</p>
             </div>
             <i class="fas fa-user-circle text-green-300"></i>
         </a>
     </div>

</aside>
