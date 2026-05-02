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

    <!-- Disease Analysis Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-virus mr-2"></i>
                Disease Analysis
            </h2>
            <a href="{{ route('crop_analyses.create') }}?field_id={{ $crop->field_id ?? '' }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                <i class="fas fa-camera mr-2"></i>
                Analyze Disease
            </a>
        </div>
        
        @if(isset($latestAnalysis) && $latestAnalysis)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Latest Diagnosis</p>
                <p class="text-lg font-bold text-gray-900">{{ $latestAnalysis->diagnosis ?? 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Severity</p>
                <p class="text-lg font-bold text-{{ $latestAnalysis->severity_color ?? 'gray' }}-600">
                    {{ ucfirst($latestAnalysis->severity ?? 'N/A') }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Confidence</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($latestAnalysis->confidence_score ?? 0, 1) }}%</p>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <h3 class="text-md font-semibold text-gray-700 mb-2">Recommended Action</h3>
            <p class="bg-emerald-50 text-emerald-800 p-3 rounded-lg">{{ $latestAnalysis->recommendation ?? 'No recommendation available' }}</p>
        </div>
        
        <div class="mt-4 flex space-x-2">
            <a href="{{ route('crop_analyses.show', $latestAnalysis) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                View Full Analysis →
            </a>
            <a href="{{ route('crop_analyses.index') }}?crop_cycle_id=&field_id={{ $crop->field_id ?? '' }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                View All Analyses →
            </a>
        </div>
        @else
        <p class="text-gray-500">No disease analyses yet for this crop.</p>
        @endif
    </div>
</div>
@endsection
