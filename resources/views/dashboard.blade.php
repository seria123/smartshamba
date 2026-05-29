<x-layout.app-layout title="Dashboard">
    
    <!-- Mobile Bottom Navigation -->
    <x-dashboard.bottom-nav active="home" />

    <!-- Top Summary Bar -->
    @php
        $weather = [
            'temperature' => '24°C',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%'
        ];
        $alertsCount = 3;
        $cropHealth = 'good';
        $waterStatus = 'adequate';
        $livestockAlerts = 1;
    @endphp
    
    <x-dashboard.summary-bar 
        :weather="$weather" 
        :alerts-count="$alertsCount"
        crop-health="{{ $cropHealth }}"
        water-status="{{ $waterStatus }}"
        :livestock-alerts="$livestockAlerts"
    />

    <!-- Main Content -->
    <div class="p-4 sm:p-6 lg:p-8">
        
        @auth
            @if (!auth()->user()->farm)
                <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-5 mb-6">
                    <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-2 flex items-center">
                        <i class="fas fa-seedling mr-2"></i> Complete Your Farm Setup
                    </h3>
                    <p class="text-blue-700 dark:text-blue-400 mb-4 text-sm">Set up your farm profile to get started with SmartShamba.</p>
                    <a href="{{ route('farms.create') }}" class="inline-flex items-center justify-center min-h-[48px] bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i> Set Up Farm
                    </a>
                </div>
            @endif
        @endauth

        <!-- Main Grid - 2 columns mobile, 3-4 desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            
            <!-- Crops Card -->
            <x-dashboard.card 
                icon="fa-seedling" 
                title="Crops" 
                status="good" 
                metric="12" 
                metric-label="Active"
                href="{{ route('crop_cycles.index') }}"
                color="green"
            >
                <x-dashboard.status-indicator status="good" size="sm" class="mt-2" />
            </x-dashboard.card>

            <!-- Livestock Card -->
            <x-dashboard.card 
                icon="fa-cow" 
                title="Livestock" 
                status="fair"
                metric="48"
                metric-label="Animals"
                href="{{ route('livestock.index') }}"
                color="brown"
            >
                <x-dashboard.status-indicator status="fair" size="sm" class="mt-2" />
            </x-dashboard.card>

            <!-- Soil Health Card -->
            <x-dashboard.card 
                icon="fa-mountain" 
                title="Soil Health" 
                status="good"
                metric="7.2"
                metric-label="pH Level"
                href="{{ route('sensors.index') }}"
                color="brown"
            >
                <x-dashboard.status-indicator status="good" size="sm" class="mt-2" />
            </x-dashboard.card>

            <!-- Water Card -->
            <x-dashboard.card 
                icon="fa-tint" 
                title="Water Usage" 
                status="warning"
                metric="68%"
                metric-label="Reserve"
                href="{{ route('irrigation.index') }}"
                color="blue"
            >
                <x-dashboard.status-indicator status="warning" size="sm" class="mt-2" />
            </x-dashboard.card>

            <!-- Harvest Card -->
            <x-dashboard.card 
                icon="fa-basket-shopping" 
                title="Harvest" 
                status="good"
                metric="5"
                metric-label="Ready"
                href="{{ route('harvests.index') }}"
                color="yellow"
            >
                <x-dashboard.status-indicator status="good" size="sm" class="mt-2" />
            </x-dashboard.card>

            <!-- Market Card -->
            <x-dashboard.card 
                icon="fa-chart-line" 
                title="Market" 
                status="good"
                metric="KSH 45K"
                metric-label="Revenue"
                href="{{ route('revenues.index') }}"
                color="green"
            >
                <x-dashboard.status-indicator status="good" size="sm" class="mt-2" />
            </x-dashboard.card>
        </div>

        <!-- Smart Alerts Widget -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
                        <i class="fas fa-bell mr-2 text-red-500"></i>
                        Smart Alerts
                    </h2>
                    <a href="{{ route('alerts.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium min-h-[48px] flex items-center">
                        View All →
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Fertilizer Alert -->
                    <div class="border-l-4 border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-leaf text-yellow-600 mr-2"></i>
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Fertilizer</h3>
                        </div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400">
                            3 crops in nutrient-demanding stages
                        </p>
                    </div>
                    
                    <!-- Weather Alert -->
                    <div class="border-l-4 border-blue-400 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-cloud-rain text-blue-600 mr-2"></i>
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Weather</h3>
                        </div>
                        <p class="text-sm text-blue-700 dark:text-blue-400">
                            Check weather before irrigation
                        </p>
                    </div>
                    
                    <!-- Pest/Disease Alert -->
                    <div class="border-l-4 border-red-400 bg-red-50 dark:bg-red-900/20 p-4 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-bug text-red-600 mr-2"></i>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Pest Watch</h3>
                        </div>
                        <p class="text-sm text-red-700 dark:text-red-400">
                            1 high-severity issue detected
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-20 lg:mb-0">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                AI Insights & Recommendations
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-dashboard.insight type="recommendation" priority="high">
                    Apply nitrogen fertilizer to maize field - soil N levels low based on last sensor reading.
                </x-dashboard.insight>
                
                <x-dashboard.insight type="irrigation" priority="high">
                    Irrigate Field B tomorrow morning - soil moisture below optimal 30%.
                </x-dashboard.insight>
                
                <x-dashboard.insight type="warning" priority="medium">
                    Drought risk detected in northern fields - consider drought-resistant varieties.
                </x-dashboard.insight>
                
                <x-dashboard.insight type="task" priority="medium">
                    Harvest tomatoes ready in 2-3 days - estimated yield 150kg.
                </x-dashboard.insight>
            </div>
        </div>
    </div>
</x-layout.app-layout>