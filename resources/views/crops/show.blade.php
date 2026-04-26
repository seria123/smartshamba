@extends('layouts.MainLayout')

@section('title', 'Crop Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $crop->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('crops.edit', $crop->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('crops.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Crop Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">Field</p>
                <p class="text-lg font-medium text-gray-800">{{ $crop->field->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Variety</p>
                <p class="text-lg font-medium text-gray-800">{{ $crop->variety ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Planting Date</p>
                <p class="text-lg font-medium text-gray-800">{{ $crop->planting_date ? $crop->planting_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Expected Harvest</p>
                <p class="text-lg font-medium text-gray-800">{{ $crop->expected_harvest_date ? $crop->expected_harvest_date->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>
        @if($crop->notes)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-gray-800">{{ $crop->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
