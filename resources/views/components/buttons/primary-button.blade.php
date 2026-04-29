@props([
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm';
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
