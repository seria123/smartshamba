@props([
    'href' => null,
    'icon',
    'color' => 'emerald',
    'title' => '',
    'type' => 'button',
])

@php
    $colorClasses = match($color) {
        'emerald' => 'border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400',
        'yellow' => 'border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400',
        'red' => 'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400',
        'green' => 'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-400',
        'blue' => 'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-400',
        default => 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-400',
    };
    
    $baseClasses = "inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 transition-all duration-200 $colorClasses";
@endphp

@if($href)
    <a href="{{ $href }}" 
       {{ $attributes->merge(['class' => $baseClasses]) }}
       @if($title) title="{{ $title }}" @endif>
        <i class="fas {{ $icon }}"></i>
    </a>
@else
    <button type="{{ $type }}" 
            {{ $attributes->merge(['class' => $baseClasses]) }}
            @if($title) title="{{ $title }}" @endif>
        <i class="fas {{ $icon }}"></i>
    </button>
@endif
