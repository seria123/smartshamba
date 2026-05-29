@props([
    'icon',
    'title',
    'status' => 'good',
    'metric' => null,
    'metricLabel' => null,
    'href' => '#',
    'color' => 'green'
])

@php
    $statusColors = [
        'excellent' => 'bg-green-500',
        'good' => 'bg-green-400',
        'warning' => 'bg-yellow-400',
        'fair' => 'bg-yellow-400',
        'poor' => 'bg-orange-500',
        'critical' => 'bg-red-500'
    ];
    
    $iconColors = [
        'green' => 'text-green-600',
        'blue' => 'text-blue-600',
        'yellow' => 'text-yellow-600',
        'brown' => 'text-brown-600',
        'red' => 'text-red-600'
    ];
    
    $borderColors = [
        'green' => 'border-l-green-600',
        'blue' => 'border-l-blue-600',
        'yellow' => 'border-l-yellow-600',
        'brown' => 'border-l-brown-600',
        'red' => 'border-l-red-600'
    ];
    
    $statusDot = $statusColors[$status] ?? $statusColors['good'];
    $iconColor = $iconColors[$color] ?? $iconColors['green'];
    $borderColor = $borderColors[$color] ?? $borderColors['green'];
@endphp

<a href="{{ $href }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 {{ $borderColor }} p-5 hover:shadow-md transition-shadow duration-150 min-h-[120px]">
    <div class="flex items-start justify-between mb-3">
        <div class="p-2.5 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
            <i class="{{ $icon }} text-xl {{ $iconColor }} dark:{{ str_replace('text-', 'text-', $iconColor) }} group-hover:scale-105 transition-transform"></i>
        </div>
        <div class="w-3 h-3 rounded-full {{ $statusDot }} shadow-sm flex-shrink-0 mt-1"></div>
    </div>
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $title }}</h3>
    @if($metric)
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metric }}</span>
            @if($metricLabel)
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $metricLabel }}</span>
            @endif
        </div>
    @endif
    {{ $slot }}
</a>