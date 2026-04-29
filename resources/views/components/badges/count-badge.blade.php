@props([
    'count',
    'icon' => null,
    'color' => 'emerald',
])

@php
    $colorClasses = match($color) {
        'emerald' => 'bg-emerald-100 text-emerald-800',
        'red' => 'bg-red-100 text-red-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'green' => 'bg-green-100 text-green-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-gray-100 text-gray-800',
        default => 'bg-emerald-100 text-emerald-800',
    };
@endphp

<span class="inline-flex items-center {{ $colorClasses }} px-3 py-1 rounded-lg text-xs font-bold tracking-wider">
    @if($icon)
        <i class="fas {{ $icon }} mr-1.5"></i>
    @endif
    {{ $count }}
</span>
