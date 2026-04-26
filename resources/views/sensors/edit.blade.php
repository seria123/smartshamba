@extends('layouts.MainLayout')

@section('title', 'Edit Sensor - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Sensor</h1>
        <a href="{{ route('sensors.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Sensors
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('sensors.update', $sensor->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Field -->
                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field *</label>
                    <select name="field_id" id="field_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('field_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id', $sensor->field_id) == $field->id ? 'selected' : '' }}>{{ $field->name }} ({{ $field->farm->name }})</option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Sensor Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $sensor->name) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Sensor Type *</label>
                    <select name="type" id="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('type') border-red-500 @enderror"
                        required>
                        <option value="">Select Type</option>
                        <option value="soil_moisture" {{ old('type', $sensor->type) == 'soil_moisture' ? 'selected' : '' }}>Soil Moisture 💧</option>
                        <option value="soil_ph" {{ old('type', $sensor->type) == 'soil_ph' ? 'selected' : '' }}>Soil pH 🧪</option>
                        <option value="temperature" {{ old('type', $sensor->type) == 'temperature' ? 'selected' : '' }}>Temperature 🌡️</option>
                        <option value="humidity" {{ old('type', $sensor->type) == 'humidity' ? 'selected' : '' }}>Humidity 💨</option>
                        <option value="light_intensity" {{ old('type', $sensor->type) == 'light_intensity' ? 'selected' : '' }}>Light Intensity ☀️</option>
                        <option value="rain_detection" {{ old('type', $sensor->type) == 'rain_detection' ? 'selected' : '' }}>Rain Detection 🌧️</option>
                        <option value="multi" {{ old('type', $sensor->type) == 'multi' ? 'selected' : '' }}>Multi-sensor</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number *</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $sensor->serial_number) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('serial_number') border-red-500 @enderror"
                        required>
                    @error('serial_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                        <option value="active" {{ old('status', $sensor->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $sensor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ old('status', $sensor->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description', $sensor->description) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('sensors.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    <i class="fas fa-save mr-2"></i>Update Sensor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
