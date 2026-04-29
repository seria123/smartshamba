@props([
    'href' => null,
    'icon' => null,
    'type' => 'button',
    'confirm' => false,
    'confirmMessage' => 'Are you sure?'
])

@php
    $baseClasses = 'inline-flex items-center space-x-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white px-6 py-3 rounded-lg border-2 border-red-700 hover:border-red-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm';
    $confirmAttr = $confirm ? "onclick=\"return confirm('$confirmMessage')\"" : '';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {!! $confirmAttr !!} {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
