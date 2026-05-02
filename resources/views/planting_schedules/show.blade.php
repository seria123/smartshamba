@extends('layouts.MainLayout')

@section('title', 'Planting Schedule Details - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Planting Schedule Details</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('planting-schedules.edit', $plantingSchedule->id) }}" class="inline-flex items-center space-x-2 bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-xl border-2 border-yellow-700 hover:border-yellow-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('planting-schedules.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Crop</h3>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $plantingSchedule->crop->name ?? 'N/A' }}</p>
            @if($plantingSchedule->variety)
                <p class="text-sm text-gray-500 mt-1">{{ $plantingSchedule->variety }}</p>
            @endif
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Field</h3>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $plantingSchedule->field->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $plantingSchedule->farm->name ?? 'N/A' }}</p>
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Status</h3>
            <span class="inline-block px-3 py-1 mt-2 rounded-full text-sm font-bold {{ 
                $plantingSchedule->status === 'planned' ? 'bg-yellow-100 text-yellow-800' : 
                ($plantingSchedule->status === 'planted' ? 'bg-blue-100 text-blue-800' : 
                ($plantingSchedule->status === 'growing' ? 'bg-green-100 text-green-800' : 
                ($plantingSchedule->status === 'ready_for_harvest' ? 'bg-orange-100 text-orange-800' : 
                ($plantingSchedule->status === 'harvested' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))))
            }}">
                {{ ucfirst(str_replace('_', ' ', $plantingSchedule->status)) }}
            </span>
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Progress</h3>
            <p class="text-2xl font-bold text-orange-600 mt-2">{{ $plantingSchedule->completion_percentage }}%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $plantingSchedule->completion_percentage }}%"></div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Schedule Details -->
        <x-ui.card>
            <h3 class="font-bold text-lg text-gray-800 mb-4">Schedule Information</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Season</span>
                    <span class="font-medium text-gray-900 capitalize">{{ $plantingSchedule->season }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Planting Date</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->planting_date->format('F j, Y') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Expected Harvest</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->expected_harvest_date ? $plantingSchedule->expected_harvest_date->format('F j, Y') : 'TBD' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Actual Harvest</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->actual_harvest_date ? $plantingSchedule->actual_harvest_date->format('F j, Y') : 'Not harvested' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Planting Window</span>
                    <span class="font-medium text-gray-900">
                        @if($plantingSchedule->planting_window_start && $plantingSchedule->planting_window_end)
                            {{ $plantingSchedule->planting_window_start->format('M d') }} - {{ $plantingSchedule->planting_window_end->format('M d') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </x-ui.card>

        <!-- Quantity Information -->
        <x-ui.card>
            <h3 class="font-bold text-lg text-gray-800 mb-4">Quantity Details</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Estimated Quantity</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->estimated_quantity ?? 'N/A' }} {{ $plantingSchedule->quantity_unit ?? '' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Actual Quantity</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->actual_quantity ?? 'N/A' }} {{ $plantingSchedule->actual_quantity_unit ?? $plantingSchedule->quantity_unit ?? '' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Unit Type</span>
                    <span class="font-medium text-gray-900">{{ $plantingSchedule->quantity_unit ?? 'N/A' }}</span>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Notes -->
    @if($plantingSchedule->notes)
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Notes</h3>
        <p class="text-gray-600 leading-relaxed">{{ $plantingSchedule->notes }}</p>
    </x-ui.card>
    @endif

    <!-- Crop Cycle Link -->
    @if($plantingSchedule->crop_cycle_id)
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Linked Crop Cycle</h3>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <h4 class="font-semibold text-gray-900">{{ $plantingSchedule->crop_cycle->crop->name ?? 'N/A' }}</h4>
                <p class="text-sm text-gray-500">Cycle: {{ $plantingSchedule->crop_cycle->cycle_number ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('crop_cycles.show', $plantingSchedule->crop_cycle_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                View Cycle →
            </a>
        </div>
    </x-ui.card>
    @endif

    <!-- Actions -->
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('planting-schedules.edit', $plantingSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>
                Edit Schedule
            </a>
            <form action="{{ route('planting-schedules.destroy', $plantingSchedule->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to delete this schedule? This action cannot be undone.')">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Schedule
                </button>
            </form>
        </div>
    </x-ui.card>

</div>
@endsection
