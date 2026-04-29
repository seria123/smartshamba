@props([
    'type',
    'icon' => null,
])

@php
    $typeConfig = [
        'critical' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'icon' => 'fa-exclamation-circle'],
        'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'icon' => 'fa-warning'],
        'info' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'icon' => 'fa-info-circle'],
        'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => 'fa-check-circle'],
        'error' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'icon' => 'fa-times-circle'],
        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'fa-clock'],
    ];
    
    $config = $typeConfig[strtolower($type)] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'fa-circle'];
    $displayIcon = $icon ?? $config['icon'];
@endphp

<span class="inline-flex items-center {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} border px-3 py-1.5 rounded-full text-xs font-semibold">
    <i class="fas {{ $displayIcon }} mr-1.5"></i>
    {{ ucfirst($slot ?? $type) }}
</span>
