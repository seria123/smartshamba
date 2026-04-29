@extends('layouts.MainLayout')

@section('title', 'Crops - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Crops</h1>
            <p class="text-sm text-gray-500 mt-2">Manage and monitor all your crop plantings</p>
        </div>
        <x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
            Add Crop
        </x-buttons.primary-button>
    </div>

    <!-- Crops Table -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Variety</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Field</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Planting Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Expected Harvest</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($crops as $crop)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $crop->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $crop->variety ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $crop->field->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $crop->planting_date ? $crop->planting_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $crop->expected_harvest_date ? $crop->expected_harvest_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <x-buttons.icon-button 
                                    href="{{ route('crops.show', $crop->id) }}"
                                    icon="fa-eye"
                                    color="emerald"
                                    title="View Details"
                                />
                                <x-buttons.icon-button
                                    href="{{ route('crops.edit', $crop->id) }}"
                                    icon="fa-edit"
                                    color="yellow"
                                    title="Edit"
                                />
                                <form action="{{ route('crops.destroy', $crop->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-buttons.icon-button
                                        type="submit"
                                        icon="fa-trash"
                                        color="red"
                                        title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this crop?')"
                                    />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-seedling text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium mb-4">No crops found. Create one to get started.</p>
                                <x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
                                    Add First Crop
                                </x-buttons.primary-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
