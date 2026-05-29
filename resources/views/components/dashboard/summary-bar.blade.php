@props([
    'weather' => null,
    'alertsCount' => 0,
    'cropHealth' => 'good',
    'waterStatus' => 'adequate',
    'livestockAlerts' => 0
])

@php
    $statusColors = [
        'excellent' => 'bg-green-500',
        'good' => 'bg-green-400',
        'fair' => 'bg-yellow-400',
        'poor' => 'bg-orange-500',
        'critical' => 'bg-red-500'
    ];
@endphp

<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 shadow-sm">
    <div class="flex items-center justify-between gap-2 overflow-x-auto">
        <!-- Weather -->
        <div class="flex items-center gap-2 min-w-0 flex-1">
            <i class="fas fa-sun text-yellow-500 text-lg"></i>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400">Weather</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                    {{ $weather['temperature'] ?? '24°C' }} • {{ $weather['condition'] ?? 'Partly Cloudy' }}
                </p>
            </div>
        </div>

        <!-- Alerts -->
        <a href="{{ route('alerts.index') }}" class="flex items-center gap-2 min-w-0 flex-1 justify-center">
            <div class="relative">
                <i class="fas fa-bell text-red-500 text-lg"></i>
                @if($alertsCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-[16px] h-4 flex items-center justify-center px-0.5">{{ $alertsCount }}</span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400">Alerts</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $alertsCount }} active</p>
            </div>
        </a>

        <!-- Crop Health -->
        <div class="flex items-center gap-2 min-w-0 flex-1 justify-center">
            <div class="w-3 h-3 rounded-full {{ $statusColors[$cropHealth] ?? 'bg-green-400' }}"></div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400">Crop Health</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ $cropHealth }}</p>
            </div>
        </div>

        <!-- Water Status -->
        <div class="flex items-center gap-2 min-w-0 flex-1 justify-center">
            <i class="fas fa-tint text-blue-500 text-lg"></i>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400">Water</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ $waterStatus }}</p>
            </div>
        </div>

        <!-- Livestock Alerts -->
        <a href="{{ route('livestock.index') }}" class="flex items-center gap-2 min-w-0 flex-1 justify-end">
            <i class="fas fa-cow text-brown-600 text-lg"></i>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400">Livestock</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $livestockAlerts }} alerts</p>
            </div>
        </a>
    </div>
</div>