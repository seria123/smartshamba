@props([
    'status' => 'good',
    'size' => 'md'
])

@php
    $statusConfig = [
        'excellent' => ['bg' => 'bg-green-500', 'icon' => 'fa-check-circle'],
        'good' => ['bg' => 'bg-green-400', 'icon' => 'fa-check-circle'],
        'warning' => ['bg' => 'bg-yellow-400', 'icon' => 'fa-exclamation-triangle'],
        'fair' => ['bg' => 'bg-yellow-400', 'icon' => 'fa-exclamation-triangle'],
        'poor' => ['bg' => 'bg-orange-500', 'icon' => 'fa-exclamation-circle'],
        'critical' => ['bg' => 'bg-red-500', 'icon' => 'fa-times-circle'],
    ];
    
    $sizes = [
        'sm' => 'w-2 h-2',
        'md' => 'w-3 h-3',
        'lg' => 'w-4 h-4'
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['good'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-center gap-2">
    <div class="{{ $sizeClass }} rounded-full {{ $config['bg'] }} shadow-sm"></div>
    <span class="text-xs text-gray-600 dark:text-gray-400 capitalize">{{ $status }}</span>
</div>