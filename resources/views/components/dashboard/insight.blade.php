@props([
    'type' => 'info',
    'priority' => 'medium'
])

@php
    $typeConfig = [
        'recommendation' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-200 dark:border-green-700', 'icon' => 'fa-lightbulb text-green-600', 'label' => 'AI Recommendation'],
        'warning' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-200 dark:border-yellow-700', 'icon' => 'fa-exclamation-triangle text-yellow-600', 'label' => 'Warning'],
        'task' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-700', 'icon' => 'fa-tasks text-blue-600', 'label' => 'Upcoming Task'],
        'irrigation' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-700', 'icon' => 'fa-tint text-blue-600', 'label' => 'Irrigation'],
        'info' => ['bg' => 'bg-gray-50 dark:bg-gray-800', 'border' => 'border-gray-200 dark:border-gray-700', 'icon' => 'fa-info-circle text-gray-600', 'label' => 'Info'],
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="{{ $config['bg'] }} {{ $config['border'] }} border rounded-lg p-4 hover:shadow-sm transition-shadow">
    <div class="flex items-start gap-3">
        <i class="fas {{ $config['icon'] }} mt-0.5 flex-shrink-0"></i>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold {{ str_replace('text-', 'text-', $config['icon']) }}">{{ $config['label'] }}</span>
                @if($priority === 'high')
                    <span class="text-xs bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full font-medium">Urgent</span>
                @elseif($priority === 'medium')
                    <span class="text-xs bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full font-medium">Soon</span>
                @else
                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full font-medium">Later</span>
                @endif
            </div>
            <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed">{{ $slot }}</p>
        </div>
    </div>
</div>