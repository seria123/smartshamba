@extends('layouts.MainLayout')

@section('title', 'Create Field - SmartShamba')

@section('content')
<div class="space-y-1">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Field</h1>
        <a href="{{ route('fields.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Fields
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-md p-2">
        <form action="{{ route('fields.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <!-- Rainfall Zone -->
                <div>
                    <label for="rainfall_zone" class="block text-sm font-medium text-gray-700 mb-2">Rainfall Zone</label>
                    <input type="text" name="rainfall_zone" id="rainfall_zone" value="{{ old('rainfall_zone') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="e.g., High, Medium, Low">
                </div>

                <!-- Topography -->
                <div>
                    <label for="topography" class="block text-sm font-medium text-gray-700 mb-2">Topography</label>
                    <select name="topography" id="topography" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select Topography</option>
                        <option value="flat" {{ old('topography') == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="sloped" {{ old('topography') == 'sloped' ? 'selected' : '' }}>Sloped</option>
                        <option value="mixed" {{ old('topography') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                    </select>
                </div>

                <!-- Water Source -->
                <div>
                    <label for="water_source" class="block text-sm font-medium text-gray-700 mb-2">Water Source</label>
                    <select name="water_source" id="water_source" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select Water Source</option>
                        <option value="borehole" {{ old('water_source') == 'borehole' ? 'selected' : '' }}>Borehole</option>
                        <option value="river" {{ old('water_source') == 'river' ? 'selected' : '' }}>River</option>
                        <option value="rain" {{ old('water_source') == 'rain' ? 'selected' : '' }}>Rain</option>
                        <option value="irrigation system" {{ old('water_source') == 'irrigation system' ? 'selected' : '' }}>Irrigation System</option>
                    </select>
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
            <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700 flex items-center">
                        <i class="fas fa-satellite-dish text-emerald-600 mr-2"></i>
                        GPS Coordinates
                    </label>
                    <button type="button" id="getFieldLocationBtn" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-location-arrow mr-2"></i>
                        Get My Location
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gps_latitude" class="block text-sm font-medium text-gray-600 mb-1">
                            Latitude
                        </label>
                        <input type="number" name="gps_latitude" id="gps_latitude" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-lg"
                            step="any" placeholder="-90.00000000" min="-90" max="90"
                            value="{{ old('gps_latitude') }}">
                        @error('gps_latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="gps_longitude" class="block text-sm font-medium text-gray-600 mb-1">
                            Longitude
                        </label>
                        <input type="number" name="gps_longitude" id="gps_longitude" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-lg"
                            step="any" placeholder="-180.00000000" min="-180" max="180"
                            value="{{ old('gps_longitude') }}">
                        @error('gps_longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div id="fieldLocationStatus" class="mt-3 text-sm">
                    <span class="text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click "Get My Location" to automatically capture GPS coordinates
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <!-- Livestock Management Details -->
            <div class="mt-6 p-4 border rounded bg-blue-50">
                <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-cow text-blue-600 mr-2"></i>
                    Livestock Management Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="feed_requirements" class="block text-sm font-medium text-gray-700 mb-2">Feed Requirements</label>
                        <textarea name="feed_requirements" id="feed_requirements" rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('feed_requirements') }}</textarea>
                    </div>
                    <div>
                        <label for="vaccination_schedule" class="block text-sm font-medium text-gray-700 mb-2">Vaccination Schedule</label>
                        <textarea name="vaccination_schedule" id="vaccination_schedule" rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('vaccination_schedule') }}</textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="housing_requirements" class="block text-sm font-medium text-gray-700 mb-2">Housing Requirements</label>
                    <textarea name="housing_requirements" id="housing_requirements" rows="2" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('housing_requirements') }}</textarea>
                </div>
            </div>

<!-- Submit Buttons -->
            <div class="mt-8 flex justify-between items-center">

    <!-- Cancel -->
    <a href="{{ route('fields.index') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Cancel
    </a>

    <!-- Create Button -->
    <button type="submit"
        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg active:scale-95 transition duration-200">

        <i class="fas fa-seedling mr-2"></i>
        Create Field

</div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const getLocationBtn = document.getElementById('getFieldLocationBtn');
    const latInput = document.getElementById('gps_latitude');
    const lngInput = document.getElementById('gps_longitude');
    const status = document.getElementById('fieldLocationStatus');

    if (!getLocationBtn || !latInput || !lngInput || !status) return;

    function setStatus(message, color = 'gray') {
        status.innerHTML = `<span class="text-${color}-600 flex items-center">${message}</span>`;
    }

    getLocationBtn.addEventListener('click', function() {
        const originalHTML = getLocationBtn.innerHTML;
        getLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Getting...';
        getLocationBtn.disabled = true;

        if (!navigator.geolocation) {
            setStatus('<i class="fas fa-exclamation-circle mr-1"></i>Geolocation not supported by this browser', 'red');
            getLocationBtn.innerHTML = originalHTML;
            getLocationBtn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                latInput.value = pos.coords.latitude.toFixed(8);
                lngInput.value = pos.coords.longitude.toFixed(8);
                setStatus(
                    `<i class="fas fa-check-circle mr-1"></i>
                    Location captured: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)} (±${Math.round(pos.coords.accuracy)}m)`,
                    'green'
                );
                getLocationBtn.innerHTML = originalHTML;
                getLocationBtn.disabled = false;
            },
            function(err) {
                let message = 'GPS error';
                if (err.code === 1) {
                    message = 'Location permission denied. Please allow location access in your browser settings.';
                } else if (err.code === 2) {
                    message = 'Location unavailable. Ensure GPS/location services are enabled on your device.';
                } else if (err.code === 3) {
                    message = 'Location request timed out. Move to an open area with clear sky view.';
                }
                setStatus(`<i class="fas fa-exclamation-circle mr-1"></i>${message}`, 'red');
                getLocationBtn.innerHTML = originalHTML;
                getLocationBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 300000
            }
        );
    });
});
</script>
@endpush
