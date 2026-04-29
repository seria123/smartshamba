@extends('layouts.MainLayout')

@section('title', 'Fertilizer Type Details - SmartShamba')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $fertilizerType->name }}
                    </h1>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('fertilizer_types.edit', $fertilizerType) }}" class="btn btn-outline btn-secondary">
                            Edit
                        </a>
                        <a href="{{ route('fertilizer_types.index') }}" class="btn btn-outline">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <p class="text-gray-600"><strong>Name:</strong> {{ $fertilizerType->name }}</p>
                        <p class="text-gray-600"><strong>Type:</strong> {{ ucfirst($fertilizerType->type) }}</p>
                        <p class="text-gray-600"><strong>Description:</strong> {{ $fertilizerType->description ?? 'N/A' }}</p>
                    </div>

                    <!-- Stock Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Information</h3>
                        <p class="text-gray-600"><strong>Default Unit:</strong> {{ $fertilizerType->default_unit }}</p>
                        <p class="text-gray-600"><strong>Minimum Threshold:</strong> {{ $fertilizerType->min_threshold }} {{ $fertilizerType->default_unit }}</p>
                        <p class="text-gray-600"><strong>Current Stock:</strong> {{ $fertilizerType->totalQuantity() }} {{ $fertilizerType->default_unit }}</p>
                        <p class="text-gray-600"><strong>Status:</strong> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $fertilizerType->isBelowThreshold() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $fertilizerType->isBelowThreshold() ? 'Low Stock' : 'In Stock' }}
                            </span>
                        </p>
                        <p class="text-gray-600"><strong>Active:</strong> {{ $fertilizerType->is_active ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection