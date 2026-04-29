@props([
    'color' => 'emerald',
])

@php
    $colorClasses = match($color) {
        'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'red' => 'bg-red-100 text-red-800 border-red-300',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'green' => 'bg-green-100 text-green-800 border-green-300',
        'blue' => 'bg-blue-100 text-blue-800 border-blue-300',
        'orange' => 'bg-orange-100 text-orange-800 border-orange-300',
        'purple' => 'bg-purple-100 text-purple-800 border-purple-300',
        'pink' => 'bg-pink-100 text-pink-800 border-pink-300',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-300',
        default => 'bg-emerald-100 text-emerald-800 border-emerald-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold tracking-wider border $colorClasses"]) }}>
    {{ $slot }}
</span>
