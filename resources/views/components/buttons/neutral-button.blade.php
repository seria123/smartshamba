@props([
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center space-x-2 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white px-5 py-2.5 rounded-lg border-2 border-gray-700 hover:border-gray-600 transition-all duration-200 font-semibold text-sm';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
