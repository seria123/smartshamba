@extends('layouts.MainLayout')

@section('title', 'Record Location - SmartShamba')

@section('header')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('livestock.show', $livestock) }}"
                   class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Animal</span>
                </a>
                <h1 class="text-2xl font-bold text-gray-800 tracking-wide">Record Location</h1>
                <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $livestock->name ?? 'Unnamed' }}
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        <!-- Quick Info Bar -->
        <x-ui.card class="bg-gradient-to-r from-blue-50 to-purple-50 border-blue-200">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-700 font-medium">Current Location</p>
                        @if($livestock->currentLocation)
                            <p class="text-lg font-semibold text-blue-900">
                                {{ $livestock->currentLocation->field->name ?? $livestock->currentLocation->farm->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-blue-600">
                                Since {{ $livestock->currentLocation->entered_at->diffForHumans() }}
                            </p>
                        @else
                            <p class="text-sm text-blue-600">No location recorded</p>
                        @endif
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm text-blue-700 font-medium">Total Location Records</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $livestock->locations_count ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Location Recording Form -->
        <x-ui.card>
            <form action="{{ route('livestock.locations.store', $livestock) }}" method="POST" id="locationForm">
                @csrf

                <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-emerald-600"></i>
                    Movement Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Location Type -->
                    <div>
                        <label for="location_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Location Type *
                        </label>
                        <select name="location_type" id="location_type" required
                            class="form-input-modern">
                            <option value="">Select location type...</option>
                            <option value="field">📍 Field</option>
                            <option value="farm">🏢 General Farm Area</option>
                            <option value="pasture">🌾 Pasture</option>
                            <option value="barn">🏠 Barn</option>
                            <option value="sick_bay">🏥 Sick Bay</option>
                            <option value="transport">🚚 In Transit</option>
                            <option value="other">📋 Other</option>
                        </select>
                    </div>

                    <!-- Movement Type -->
                    <div>
                        <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Movement Type *
                        </label>
                        <select name="movement_type" id="movement_type" required
                            class="form-input-modern">
                            <option value="">Select movement type...</option>
                            <option value="grazing">🌿 Grazing</option>
                            <option value="resting">😴 Resting</option>
                            <option value="feeding">🍽️ Feeding</option>
                            <option value="treatment">💊 Treatment</option>
                            <option value="inspection">🔍 Inspection</option>
                            <option value="transport">🚚 Transport</option>
                            <option value="birth">🐄 Birth/Calving</option>
                            <option value="other">📋 Other</option>
                        </select>
                    </div>

                    <!-- Field -->
                    <div>
                        <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Field (optional)
                        </label>
                        <select name="field_id" id="fieldSelect"
                            class="form-input-modern">
                            <option value="">Select field...</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}"
                                    data-lat="{{ $field->gps_latitude ?? '' }}"
                                    data-lng="{{ $field->gps_longitude ?? '' }}"
                                    {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                    {{ $field->name }} - {{ $field->farm->name ?? 'No Farm' }}
                                    @if($field->gps_latitude && $field->gps_longitude)
                                        📍
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="fieldCoords">
                            Fields with 📍 have saved GPS coordinates
                        </p>
                    </div>

                    <!-- Farm -->
                    <div>
                        <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Farm (optional)
                        </label>
                        <select name="farm_id" id="farmSelect"
                            class="form-input-modern">
                            <option value="">Select farm...</option>
                            @foreach(\App\Models\Farm::all() as $farm)
                                <option value="{{ $farm->id }}"
                                    data-lat="{{ $farm->gps_latitude ?? '' }}"
                                    data-lng="{{ $farm->gps_longitude ?? '' }}"
                                    {{ $livestock->farm_id == $farm->id || old('farm_id') == $farm->id ? 'selected' : '' }}>
                                    {{ $farm->name }}
                                    @if($farm->gps_latitude && $farm->gps_longitude)
                                        📍
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="farmCoords">
                            Farms with 📍 have saved GPS coordinates
                        </p>
                    </div>

                    <!-- GPS Coordinates Section -->
                    <div class="md:col-span-2 border-2 border-dashed border-gray-200 rounded-lg p-6 bg-gray-50">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-satellite-dish text-emerald-600 mr-2"></i>
                                GPS Coordinates
                            </label>
                            <button type="button" id="getLocationBtn"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-location-arrow mr-2"></i>
                                Get My Location
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-600 mb-1">
                                    Latitude
                                </label>
                                <input type="number" name="gps_latitude" id="latitude"
                                    class="form-input-modern font-mono text-lg"
                                    step="any" placeholder="-90.00000000" min="-90" max="90"
                                    value="{{ old('gps_latitude', $initialLat ?? '') }}">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-600 mb-1">
                                    Longitude
                                </label>
                                <input type="number" name="gps_longitude" id="longitude"
                                    class="form-input-modern font-mono text-lg"
                                    step="any" placeholder="-180.00000000" min="-180" max="180"
                                    value="{{ old('gps_longitude', $initialLng ?? '') }}">
                            </div>
                        </div>

                        <div id="locationStatus" class="mt-3 text-sm">
                            @if(isset($initialLat) && isset($initialLng) && $initialLat && $initialLng)
                                <span class="text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    GPS pre-filled from current farm location
                                </span>
                            @else
                                <span class="text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Click "Get My Location" or select a field/farm to auto-fill GPS coordinates
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Movement Notes (optional)
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="form-textarea-modern"
                            placeholder="Add any additional details about this movement, animal behavior, weather conditions, etc...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i>
                            <span class="text-sm text-gray-500">
                                All required fields marked with *
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('livestock.show', $livestock) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium whitespace-nowrap">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-map-marker-check mr-2"></i>
                                Create & Record Location
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </x-ui.card>

        <!-- Tips Card -->
        <x-ui.card>
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 mt-0.5">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Quick Tips for Location Recording</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• GPS coordinates help track animal movement patterns over time</li>
                        <li>• Select a field or farm to auto-fill saved coordinates</li>
                        <li>• Use "Get My Location" when you're at the actual field</li>
                        <li>• Movement type helps categorize animal activities</li>
                        <li>• Treatment movements can be linked to health records</li>
                    </ul>
                </div>
            </div>
        </x-ui.card>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('getLocationBtn');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const status = document.getElementById('locationStatus');

    if (!btn || !latInput || !lngInput || !status) return;

    function setStatus(html, color = 'gray') {
        status.innerHTML = `<span class="text-${color}-600 flex items-center">${html}</span>`;
    }

    setStatus(
        `<i class="fas fa-info-circle mr-1"></i>
        Click "Get My Location" or select a field/farm to auto-fill GPS coordinates`,
        'blue'
    );

    btn.addEventListener('click', function () {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Getting...';
        btn.disabled = true;

        if (!navigator.geolocation) {
            setStatus('<i class="fas fa-exclamation-circle mr-1"></i>Geolocation not supported by this browser', 'red');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                latInput.value = pos.coords.latitude.toFixed(8);
                lngInput.value = pos.coords.longitude.toFixed(8);
                setStatus(
                    `<i class="fas fa-check-circle mr-1"></i>
                    Location captured: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)} (±${Math.round(pos.coords.accuracy)}m)`,
                    'green'
                );
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            },
            function (err) {
                let message = 'GPS error';
                if (err.code === 1) {
                    message = 'Location permission denied. Please allow location access in your browser settings.';
                } else if (err.code === 2) {
                    message = 'Location unavailable. Ensure GPS/location services are enabled on your device.';
                } else if (err.code === 3) {
                    message = 'Location request timed out. Move to an open area with clear sky view, or select a field/farm manually.';
                }
                setStatus(`<i class="fas fa-exclamation-circle mr-1"></i>${message}`, 'red');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
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