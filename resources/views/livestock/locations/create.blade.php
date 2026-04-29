@extends('layouts.MainLayout')

@section('title', 'Record Location - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock.show', $livestock) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Record Location</h1>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $livestock->name ?? 'Unnamed' }}</span>
        </div>
    </div>

    <!-- Location Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock.locations.store', $livestock) }}" method="POST" id="locationForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Location Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Type *</label>
                    <select name="location_type" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        <option value="field">Field</option>
                        <option value="farm">General Farm Area</option>
                        <option value="pasture">Pasture</option>
                        <option value="barn">Barn</option>
                        <option value="sick_bay">Sick Bay</option>
                        <option value="transport">In Transit</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Movement Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type *</label>
                    <select name="movement_type" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        <option value="grazing">Grazing</option>
                        <option value="resting">Resting</option>
                        <option value="feeding">Feeding</option>
                        <option value="treatment">Treatment</option>
                        <option value="inspection">Inspection</option>
                        <option value="transport">Transport</option>
                        <option value="birth">Birth/Calving</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Field</label>
                    <select name="field_id" id="fieldSelect" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Field (optional)</option>
                        @foreach($fields as $field)
                        <option value="{{ $field->id }}" 
                            data-lat="{{ $field->gps_latitude ?? '' }}" 
                            data-lng="{{ $field->gps_longitude ?? '' }}"
                            >{{ $field->name }} - {{ $field->farm->name ?? 'No Farm' }}
                            @if($field->gps_latitude && $field->gps_longitude)
                                📍
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="fieldCoords">
                        @if($fields->first()?->gps_latitude && $fields->first()?->gps_longitude)
                            Fields with 📍 have GPS coordinates
                        @endif
                    </p>
                </div>

                <!-- Farm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                    <select name="farm_id" id="farmSelect" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Farm (optional)</option>
                        @foreach(\App\Models\Farm::all() as $farm)
                        <option value="{{ $farm->id }}" 
                            data-lat="{{ $farm->gps_latitude ?? '' }}" 
                            data-lng="{{ $farm->gps_longitude ?? '' }}"
                            {{ $livestock->farm_id == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                            @if($farm->gps_latitude && $farm->gps_longitude) 📍 @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="farmCoords">
                        @if(\App\Models\Farm::first()?->gps_latitude && \App\Models\Farm::first()?->gps_longitude)
                            Farms with 📍 have GPS coordinates
                        @endif
                    </p>
                </div>

                <!-- GPS Coordinates (Auto-detected) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        GPS Coordinates 
                        <button type="button" id="getLocationBtn" class="ml-2 text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                            <i class="fas fa-location-arrow mr-1"></i>Get My Location
                        </button>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" step="any" name="gps_latitude" id="latitude" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Latitude" min="-90" max="90" value="{{ old('gps_latitude', $initialLat ?? '') }}">
                        <input type="number" step="any" name="gps_longitude" id="longitude" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Longitude" min="-180" max="180" value="{{ old('gps_longitude', $initialLng ?? '') }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="locationStatus">Click "Get My Location" or select a field/farm to auto-fill</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Any additional details about this location or movement..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('livestock.show', $livestock) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Record Location
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const getLocationBtn = document.getElementById('getLocationBtn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const locationStatus = document.getElementById('locationStatus');
    const fieldSelect = document.getElementById('fieldSelect');
    const farmSelect = document.getElementById('farmSelect');

    // Pre-fill from initial values (farm GPS) if present
    @if(isset($initialLat) && isset($initialLng) && $initialLat && $initialLng)
        latitudeInput.value = "{{ $initialLat }}";
        longitudeInput.value = "{{ $initialLng }}";
        locationStatus.textContent = 'GPS pre-filled from current farm location';
        locationStatus.className = 'text-xs text-green-500 mt-1';
    @endif

    // Auto-detect location from browser
    getLocationBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            locationStatus.textContent = 'Geolocation is not supported by this browser.';
            locationStatus.className = 'text-xs text-red-500 mt-1';
            return;
        }

        locationStatus.textContent = 'Getting location...';
        locationStatus.className = 'text-xs text-blue-500 mt-1';
        getLocationBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude.toFixed(8);
                const lng = position.coords.longitude.toFixed(8);
                
                latitudeInput.value = lat;
                longitudeInput.value = lng;
                locationStatus.textContent = `Location captured: ${lat}, ${lng} (accuracy: ${Math.round(position.coords.accuracy)}m)`;
                locationStatus.className = 'text-xs text-green-500 mt-1';
                getLocationBtn.disabled = false;
            },
            function(error) {
                let msg = 'Unable to retrieve location.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        msg = 'Location access denied. Please enable location permissions.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = 'Location information unavailable.';
                        break;
                    case error.TIMEOUT:
                        msg = 'Location request timed out.';
                        break;
                }
                locationStatus.textContent = msg;
                locationStatus.className = 'text-xs text-red-500 mt-1';
                getLocationBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // Auto-populate from field selection
    fieldSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const lat = selectedOption.dataset.lat;
        const lng = selectedOption.dataset.lng;
        const fieldCoords = document.getElementById('fieldCoords');
        
        if (lat && lng) {
            latitudeInput.value = lat;
            longitudeInput.value = lng;
            fieldCoords.textContent = `Field coordinates: ${lat}, ${lng}`;
            locationStatus.textContent = 'GPS auto-filled from field data';
            locationStatus.className = 'text-xs text-green-500 mt-1';
        } else {
            fieldCoords.textContent = 'No GPS data for this field';
        }
    });

    // Auto-populate from farm selection
    farmSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const lat = selectedOption.dataset.lat;
        const lng = selectedOption.dataset.lng;
        const farmCoords = document.getElementById('farmCoords');
        
        if (lat && lng) {
            // Only fill if lat/lng not already set from field
            if (!latitudeInput.value && !longitudeInput.value) {
                latitudeInput.value = lat;
                longitudeInput.value = lng;
            }
            farmCoords.textContent = `Farm coordinates: ${lat}, ${lng}`;
            locationStatus.textContent = 'GPS auto-filled from farm data';
            locationStatus.className = 'text-xs text-green-500 mt-1';
        } else {
            farmCoords.textContent = 'No GPS data for this farm';
        }
    });
});
</script>
@endpush
