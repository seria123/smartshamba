@props([
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200';
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
