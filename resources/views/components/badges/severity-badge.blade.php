@props([
    'severity',
])

@php
    $severityConfig = [
        'high' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300'],
        'medium' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300'],
        'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300'],
        'critical' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300'],
        'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300'],
        'info' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300'],
    ];
    
    $config = $severityConfig[strtolower($severity)] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300'];
@endphp

<span class="inline-flex items-center {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} border px-3 py-1.5 rounded-full text-xs font-semibold">
    {{ ucfirst($slot ?? $severity) }}
</span>
