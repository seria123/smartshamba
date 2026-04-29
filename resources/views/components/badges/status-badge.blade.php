@props([
    'status',
    'icon' => null,
])

@php
    $statusConfig = [
        'healthy' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => 'fa-check-circle'],
        'sick' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'icon' => 'fa-exclamation-circle'],
        'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => 'fa-check-circle'],
        'inactive' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'fa-times-circle'],
        'sold' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'fa-handshake'],
        'dead' => ['bg' => 'bg-slate-800', 'text' => 'text-white', 'border' => 'border-slate-900', 'icon' => 'fa-times-circle'],
        'read' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => 'fa-check-circle'],
        'unread' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'icon' => 'fa-envelope'],
    ];
    
    $config = $statusConfig[strtolower($status)] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'fa-circle'];
    $displayIcon = $icon ?? $config['icon'];
@endphp

<span class="inline-flex items-center {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} border px-3 py-1.5 rounded-full text-xs font-semibold">
    <i class="fas {{ $displayIcon }} mr-1.5"></i>
    {{ ucfirst($slot ?? $status) }}
</span>
