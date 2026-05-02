@extends('layouts.MainLayout')

@section('title', 'Crop Cycles - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌾 Crop Cycles</h1>
        <a href="{{ route('crop_cycles.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> New Crop Cycle
        </a>
    </div>

    <!-- Crop Cycles List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($cropCycles->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Crop</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Field</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Variety</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Planted</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Expected Harvest</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Stage</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($cropCycles as $cycle)
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6">
                        <p class="font-medium">{{ $cycle->crop_name ?? $cycle->crop->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $cycle->category ?? 'N/A' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="font-medium">{{ $cycle->field->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $cycle->farm->name ?? 'N/A' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm">{{ $cycle->variety ?? 'N/A' }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $cycle->start_date->format('M d, Y') }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @if($cycle->expected_harvest_date)
                            <p class="text-sm {{ $cycle->expected_harvest_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                {{ $cycle->expected_harvest_date->format('M d, Y') }}
                                @if($cycle->expected_harvest_date->isPast())
                                    <span class="text-xs text-red-500">(overdue)</span>
                                @endif
                            </p>
                        @else
                            <p class="text-sm text-gray-400">Not set</p>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            {{ ucfirst($cycle->current_stage) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <a href="{{ route('crop_cycles.show', $cycle) }}" class="text-green-600 hover:text-green-800 text-sm">
                                View
                            </a>
                            <a href="{{ route('crop_cycles.edit', $cycle) }}" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12">
            <i class="fas fa-seedling text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-500 mb-2">No crop cycles yet</h3>
            <p class="text-gray-400 mb-4">Start tracking your crops by creating a new crop cycle</p>
            <a href="{{ route('crop_cycles.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                Create Your First Crop Cycle
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
