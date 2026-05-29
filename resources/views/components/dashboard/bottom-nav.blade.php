@props([
    'active' => 'home'
])

@php
    $navItems = [
        'home' => ['icon' => 'fa-home', 'label' => 'Home', 'href' => route('dashboard')],
        'crops' => ['icon' => 'fa-seedling', 'label' => 'Crops', 'href' => route('crop_cycles.index')],
        'livestock' => ['icon' => 'fa-cow', 'label' => 'Livestock', 'href' => route('livestock.index')],
        'insights' => ['icon' => 'fa-lightbulb', 'label' => 'Insights', 'href' => '#'],
        'settings' => ['icon' => 'fa-cog', 'label' => 'Settings', 'href' => route('settings.index')],
    ];
@endphp

<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
    <div class="grid grid-cols-5 h-16">
        @foreach($navItems as $key => $item)
            <a href="{{ $item['href'] }}" 
               class="flex flex-col items-center justify-center gap-1 transition-colors min-h-[48px] {{ $active === $key ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <i class="fas {{ $item['icon'] }} text-lg"></i>
                <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

<style>
/* Ensure content isn't hidden behind bottom nav on mobile */
@media (max-width: 1023px) {
    main {
        padding-bottom: 4rem;
    }
}
</style>