@extends('layouts.MainLayout')

@section('title', 'Farm Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $farm->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('farms.edit', $farm->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('farms.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Farm Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="text-lg font-medium text-gray-800">{{ $farm->location ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Size</p>
                <p class="text-lg font-medium text-gray-800">{{ $farm->size_hectares ? $farm->size_hectares . ' ha' : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Fields</p>
                <p class="text-lg font-medium text-gray-800">{{ $farm->fields->count() }}</p>
            </div>
        </div>
        @if($farm->description)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Description</p>
                <p class="text-gray-800">{{ $farm->description }}</p>
            </div>
        @endif
    </div>

    <!-- Fields -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Fields</h2>
        @if($farm->fields->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($farm->fields as $field)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-800">{{ $field->name }}</h3>
                            <span class="text-sm text-gray-500">{{ $field->size_hectares ?? 'N/A' }} ha</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">{{ $field->location ?? 'No location' }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-primary">{{ $field->sensors->count() }} sensors</span>
                            <a href="{{ route('fields.show', $field->id) }}" class="text-sm text-primary hover:text-primary-dark">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No fields available for this farm.</p>
        @endif
        <div class="mt-4">
            <a href="{{ route('fields.create') }}?farm_id={{ $farm->id }}" class="text-primary hover:text-primary-dark font-medium">
                <i class="fas fa-plus mr-2"></i>Add Field
            </a>
        </div>
    </div>
</div>
@endsection
