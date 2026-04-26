@extends('layouts.MainLayout')

@section('title', 'Edit Alert - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Alert</h1>
        <a href="{{ route('alerts.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Alerts
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('alerts.update', $alert->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sensor Reading -->
                <div>
                    <label for="sensor_reading_id" class="block text-sm font-medium text-gray-700 mb-2">Sensor Reading *</label>
                    <select name="sensor_reading_id" id="sensor_reading_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('sensor_reading_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a Sensor Reading</option>
                        @foreach($sensorReadings as $reading)
                            <option value="{{ $reading->id }}" {{ old('sensor_reading_id', $alert->sensor_reading_id) == $reading->id ? 'selected' : '' }}>
                                {{ $reading->sensor->name ?? 'Unknown' }} - {{ $reading->timestamp->format('M d, Y H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('sensor_reading_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Alert Type *</label>
                    <select name="type" id="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('type') border-red-500 @enderror"
                        required>
                        <option value="">Select Type</option>
                        <option value="info" {{ old('type', $alert->type) == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ old('type', $alert->type) == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="critical" {{ old('type', $alert->type) == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Severity -->
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">Severity *</label>
                    <select name="severity" id="severity" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('severity') border-red-500 @enderror"
                        required>
                        <option value="">Select Severity</option>
                        <option value="low" {{ old('severity', $alert->severity) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity', $alert->severity) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity', $alert->severity) == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('severity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parameter -->
                <div>
                    <label for="parameter" class="block text-sm font-medium text-gray-700 mb-2">Parameter *</label>
                    <select name="parameter" id="parameter" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('parameter') border-red-500 @enderror"
                        required>
                        <option value="">Select Parameter</option>
                        <option value="soil_moisture" {{ old('parameter', $alert->parameter) == 'soil_moisture' ? 'selected' : '' }}>Soil Moisture</option>
                        <option value="temperature" {{ old('parameter', $alert->parameter) == 'temperature' ? 'selected' : '' }}>Temperature</option>
                        <option value="humidity" {{ old('parameter', $alert->parameter) == 'humidity' ? 'selected' : '' }}>Humidity</option>
                        <option value="soil_ph" {{ old('parameter', $alert->parameter) == 'soil_ph' ? 'selected' : '' }}>Soil pH</option>
                    </select>
                    @error('parameter')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Value -->
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                    <input type="number" name="value" id="value" value="{{ old('value', $alert->value) }}" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <!-- Threshold -->
                <div>
                    <label for="threshold" class="block text-sm font-medium text-gray-700 mb-2">Threshold</label>
                    <input type="number" name="threshold" id="threshold" value="{{ old('threshold', $alert->threshold) }}" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <!-- Is Read -->
                <div>
                    <label for="is_read" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_read" id="is_read" value="1" {{ old('is_read', $alert->is_read) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-600">Mark as read</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div class="mt-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                <textarea name="message" id="message" rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('message') border-red-500 @enderror"
                    required>{{ old('message', $alert->message) }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('alerts.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    <i class="fas fa-save mr-2"></i>Update Alert
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
