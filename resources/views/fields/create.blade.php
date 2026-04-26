@extends('layouts.MainLayout')

@section('title', 'Create Field - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Field</h1>
        <a href="{{ route('fields.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Fields
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('fields.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Farm -->
                <div>
                    <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm *</label>
                    <select name="farm_id" id="farm_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('farm_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a Farm</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Field Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <!-- Soil Type -->
                <div>
                    <label for="soil_type" class="block text-sm font-medium text-gray-700 mb-2">Soil Type</label>
                    <select name="soil_type" id="soil_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select Soil Type</option>
                        <option value="clay" {{ old('soil_type') == 'clay' ? 'selected' : '' }}>Clay</option>
                        <option value="sandy" {{ old('soil_type') == 'sandy' ? 'selected' : '' }}>Sandy</option>
                        <option value="loamy" {{ old('soil_type') == 'loamy' ? 'selected' : '' }}>Loamy</option>
                        <option value="silty" {{ old('soil_type') == 'silty' ? 'selected' : '' }}>Silty</option>
                        <option value="peaty" {{ old('soil_type') == 'peaty' ? 'selected' : '' }}>Peaty</option>
                        <option value="chalky" {{ old('soil_type') == 'chalky' ? 'selected' : '' }}>Chalky</option>
                    </select>
                </div>

                <!-- Size -->
                <div>
                    <label for="size_hectares" class="block text-sm font-medium text-gray-700 mb-2">Size (Hectares)</label>
                    <input type="number" name="size_hectares" id="size_hectares" value="{{ old('size_hectares') }}" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <!-- GPS Coordinates -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 mb-3">GPS Coordinates</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gps_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <input type="number" name="gps_latitude" id="gps_latitude" value="{{ old('gps_latitude') }}" step="0.00000001" min="-90" max="90"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="-90 to 90">
                        @error('gps_latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="gps_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <input type="number" name="gps_longitude" id="gps_longitude" value="{{ old('gps_longitude') }}" step="0.00000001" min="-180" max="180"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="-180 to 180">
                        @error('gps_longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('fields.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    <i class="fas fa-save mr-2"></i>Create Field
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
