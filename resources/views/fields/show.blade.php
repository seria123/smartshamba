@extends('layouts.MainLayout')

@section('title', 'Field Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $field->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('fields.edit', $field->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('fields.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Field Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Farm</p>
                <p class="text-lg font-medium text-gray-800">{{ $field->farm->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Size</p>
                <p class="text-lg font-medium text-gray-800">{{ $field->size_hectares ? $field->size_hectares . ' ha' : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="text-lg font-medium text-gray-800">{{ $field->location ?? 'N/A' }}</p>
            </div>
        </div>
        
        <!-- Environmental Characteristics -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($field->rainfall_zone)
            <div>
                <p class="text-sm text-gray-500">Rainfall Zone</p>
                <p class="text-lg font-medium text-gray-800">{{ $field->rainfall_zone }}</p>
            </div>
            @endif
            @if($field->topography)
            <div>
                <p class="text-sm text-gray-500">Topography</p>
                <p class="text-lg font-medium text-gray-800">{{ ucfirst($field->topography) }}</p>
            </div>
            @endif
            @if($field->water_source)
            <div>
                <p class="text-sm text-gray-500">Water Source</p>
                <p class="text-lg font-medium text-gray-800">{{ ucfirst($field->water_source) }}</p>
            </div>
            @endif
        </div>

        <!-- Soil & GPS -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($field->soil_type)
            <div>
                <p class="text-sm text-gray-500">Soil Type</p>
                <p class="text-lg font-medium text-gray-800">{{ ucfirst($field->soil_type) }}</p>
            </div>
            @endif
            @if($field->gps_latitude || $field->gps_longitude)
            <div>
                <p class="text-sm text-gray-500">GPS Coordinates</p>
                <p class="text-lg font-medium text-gray-800">
                    {{ $field->gps_latitude ? $field->gps_latitude : 'N/A' }}, 
                    {{ $field->gps_longitude ? $field->gps_longitude : 'N/A' }}
                </p>
            </div>
            @endif
        </div>

        @if($field->description)
        <div class="mt-6">
            <p class="text-sm text-gray-500">Description</p>
            <p class="text-gray-800">{{ $field->description }}</p>
        </div>
        @endif

    </div>

    <!-- Current Crop -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-seedling text-green-500 mr-2"></i>Current Crop
        </h2>
        @if($field->crop)
        <div class="border rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Crop Name</p>
                    <p class="text-lg font-medium text-gray-800">{{ $field->crop->name }}</p>
                </div>
                @if($field->crop->variety)
                <div>
                    <p class="text-sm text-gray-500">Variety</p>
                    <p class="text-lg font-medium text-gray-800">{{ $field->crop->variety }}</p>
                </div>
                @endif
                @if($field->crop->planting_date)
                <div>
                    <p class="text-sm text-gray-500">Planting Date</p>
                    <p class="text-lg font-medium text-gray-800">{{ $field->crop->planting_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($field->crop->expected_harvest_date)
                <div>
                    <p class="text-sm text-gray-500">Expected Harvest</p>
                    <p class="text-lg font-medium text-gray-800">{{ $field->crop->expected_harvest_date->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
            @if($field->crop->notes)
            <div class="mt-4">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-gray-800">{{ $field->crop->notes }}</p>
            </div>
            @endif
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No crop planted in this field.</p>
        @endif
        <div class="mt-4">
            <a href="{{ route('crops.create') }}?field_id={{ $field->id }}" class="text-primary hover:text-primary-dark font-medium">
                <i class="fas fa-plus mr-2"></i>Add Crop
            </a>
        </div>
    </div>

    <!-- Sensors -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-satellite-dish text-blue-500 mr-2"></i>Sensors
        </h2>
        @if($field->sensors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($field->sensors as $sensor)
            <div class="border rounded-lg p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-800">{{ $sensor->name }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full {{ $sensor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $sensor->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mb-2">Type: {{ $sensor->type }}</p>
                <p class="text-sm text-gray-500 mb-2">Serial: {{ $sensor->serial_number }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-primary">{{ $sensor->sensorReadings->count() }} readings</span>
                    <a href="{{ route('sensors.show', $sensor->id) }}" class="text-sm text-primary hover:text-primary-dark">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No sensors installed in this field.</p>
        @endif
        <div class="mt-4">
            <a href="{{ route('sensors.create') }}?field_id={{ $field->id }}" class="text-primary hover:text-primary-dark font-medium">
                <i class="fas fa-plus mr-2"></i>Add Sensor
            </a>
        </div>
    </div>
</div>
@endsection
