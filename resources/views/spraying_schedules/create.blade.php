@extends('layouts.MainLayout')

@section('title', 'Create Spraying Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Spraying Schedule</h1>
        <a href="{{ route('spraying_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <form action="{{ route('spraying_schedules.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- 🌾 1. Basic Crop & Field Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🌾 1. Basic Crop & Field Information</h2>
            <p class="text-sm text-gray-600 mb-4">These are the foundation inputs. Spraying depends heavily on crop stage.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="crop_cycle_id" class="block text-sm font-medium text-gray-700 mb-2">Crop Cycle (Optional)</label>
                    <select name="crop_cycle_id" id="crop_cycle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Crop Cycle</option>
                        @foreach($cropCycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ old('crop_cycle_id') == $cycle->id ? 'selected' : '' }}>{{ $cycle->crop_name }} ({{ $cycle->field->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="crop_type" class="block text-sm font-medium text-gray-700 mb-2">Crop Type</label>
                    <input type="text" name="crop_type" id="crop_type" value="{{ old('crop_type') }}" placeholder="e.g., maize, tomatoes, beans" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="crop_variety" class="block text-sm font-medium text-gray-700 mb-2">Crop Variety</label>
                    <input type="text" name="crop_variety" id="crop_variety" value="{{ old('crop_variety') }}" placeholder="e.g., Hybrid 513, Roma" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="planting_date" class="block text-sm font-medium text-gray-700 mb-2">Planting Date</label>
                    <input type="date" name="planting_date" id="planting_date" value="{{ old('planting_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="growth_stage" class="block text-sm font-medium text-gray-700 mb-2">Growth Stage</label>
                    <select name="growth_stage" id="growth_stage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Growth Stage</option>
                        <option value="seedling" {{ old('growth_stage') == 'seedling' ? 'selected' : '' }}>Seedling</option>
                        <option value="vegetative" {{ old('growth_stage') == 'vegetative' ? 'selected' : '' }}>Vegetative</option>
                        <option value="flowering" {{ old('growth_stage') == 'flowering' ? 'selected' : '' }}>Flowering</option>
                        <option value="fruiting" {{ old('growth_stage') == 'fruiting' ? 'selected' : '' }}>Fruiting</option>
                    </select>
                </div>

                <div>
                    <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Crop (Optional)</label>
                    <select name="crop_id" id="crop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Crop</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field (Optional)</label>
                    <select name="field_id" id="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 🧪 2. Spray Product Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🧪 2. Spray Product Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="spray_type" class="block text-sm font-medium text-gray-700 mb-2">Spray Type *</label>
                    <select name="spray_type" id="spray_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        <option value="">Select Type</option>
                        <option value="pesticide" {{ old('spray_type') == 'pesticide' ? 'selected' : '' }}>Pesticide</option>
                        <option value="herbicide" {{ old('spray_type') == 'herbicide' ? 'selected' : '' }}>Herbicide</option>
                        <option value="fungicide" {{ old('spray_type') == 'fungicide' ? 'selected' : '' }}>Fungicide</option>
                        <option value="insecticide" {{ old('spray_type') == 'insecticide' ? 'selected' : '' }}>Insecticide</option>
                    </select>
                </div>

                <div>
                    <label for="chemical_name" class="block text-sm font-medium text-gray-700 mb-2">Chemical/Product Name *</label>
                    <input type="text" name="chemical_name" id="chemical_name" value="{{ old('chemical_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="active_ingredient" class="block text-sm font-medium text-gray-700 mb-2">Active Ingredient</label>
                    <input type="text" name="active_ingredient" id="active_ingredient" value="{{ old('active_ingredient') }}" placeholder="e.g., Glyphosate, Copper" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="target_pest_disease" class="block text-sm font-medium text-gray-700 mb-2">Target Pest/Disease</label>
                    <input type="text" name="target_pest_disease" id="target_pest_disease" value="{{ old('target_pest_disease') }}" placeholder="e.g., aphids, blight" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- 📅 3. Scheduling & Timing -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📅 3. Scheduling & Timing</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Planned Spray Date *</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="frequency_days" class="block text-sm font-medium text-gray-700 mb-2">Frequency (Days)</label>
                    <input type="number" name="frequency_days" id="frequency_days" value="{{ old('frequency_days') }}" min="1" placeholder="e.g., 7" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="growth_stage_trigger" class="block text-sm font-medium text-gray-700 mb-2">Growth-Stage Trigger</label>
                    <input type="text" name="growth_stage_trigger" id="growth_stage_trigger" value="{{ old('growth_stage_trigger') }}" placeholder="e.g., at flowering stage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="rei_hours" class="block text-sm font-medium text-gray-700 mb-2">Re-entry Interval (Hours)</label>
                    <input type="number" name="rei_hours" id="rei_hours" value="{{ old('rei_hours') }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="phi_days" class="block text-sm font-medium text-gray-700 mb-2">Pre-harvest Interval (Days)</label>
                    <input type="number" name="phi_days" id="phi_days" value="{{ old('phi_days') }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- 🌦️ 4. Weather Conditions Integration -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🌦️ 4. Weather Conditions Integration</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                    <input type="number" name="temperature" id="temperature" value="{{ old('temperature') }}" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="rain_forecast" class="block text-sm font-medium text-gray-700 mb-2">Rain Forecast</label>
                    <select name="rain_forecast" id="rain_forecast" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select</option>
                        <option value="0" {{ old('rain_forecast') == '0' ? 'selected' : '' }}>No Rain</option>
                        <option value="1" {{ old('rain_forecast') == '1' ? 'selected' : '' }}>Rain Expected</option>
                    </select>
                </div>

                <div>
                    <label for="wind_speed" class="block text-sm font-medium text-gray-700 mb-2">Wind Speed (km/h)</label>
                    <input type="number" name="wind_speed" id="wind_speed" value="{{ old('wind_speed') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- 💧 5. Dosage & Application Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">💧 5. Dosage & Application Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', 'liters') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="mixing_ratio" class="block text-sm font-medium text-gray-700 mb-2">Mixing Ratio</label>
                    <input type="text" name="mixing_ratio" id="mixing_ratio" value="{{ old('mixing_ratio') }}" placeholder="e.g., 20ml per 20L" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="water_volume" class="block text-sm font-medium text-gray-700 mb-2">Water Volume</label>
                    <input type="number" name="water_volume" id="water_volume" value="{{ old('water_volume') }}" step="0.01" min="0" placeholder="per acre/hectare" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="equipment" class="block text-sm font-medium text-gray-700 mb-2">Equipment Used</label>
                    <select name="equipment" id="equipment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Equipment</option>
                        <option value="knapsack" {{ old('equipment') == 'knapsack' ? 'selected' : '' }}>Knapsack Sprayer</option>
                        <option value="tractor" {{ old('equipment') == 'tractor' ? 'selected' : '' }}>Tractor Sprayer</option>
                        <option value="drone" {{ old('equipment') == 'drone' ? 'selected' : '' }}>Drone Sprayer</option>
                    </select>
                </div>

                <div>
                    <label for="area_covered" class="block text-sm font-medium text-gray-700 mb-2">Area Covered (ha)</label>
                    <input type="number" name="area_covered" id="area_covered" value="{{ old('area_covered') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- 👨‍🌾 6. Operator & Safety Tracking -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">👨‍🌾 6. Operator & Safety Tracking</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="operator" class="block text-sm font-medium text-gray-700 mb-2">Person Responsible</label>
                    <input type="text" name="operator" id="operator" value="{{ old('operator') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Protective Gear Checklist</label>
                    <div class="flex items-center">
                        <input type="checkbox" name="gear_gloves" id="gear_gloves" value="1" {{ old('gear_gloves') ? 'checked' : '' }} class="h-4 w-4 text-green-600 border-gray-300 rounded">
                        <label for="gear_gloves" class="ml-2 text-sm text-gray-700">Gloves</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="gear_mask" id="gear_mask" value="1" {{ old('gear_mask') ? 'checked' : '' }} class="h-4 w-4 text-green-600 border-gray-300 rounded">
                        <label for="gear_mask" class="ml-2 text-sm text-gray-700">Mask</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="gear_overalls" id="gear_overalls" value="1" {{ old('gear_overalls') ? 'checked' : '' }} class="h-4 w-4 text-green-600 border-gray-300 rounded">
                        <label for="gear_overalls" class="ml-2 text-sm text-gray-700">Overalls</label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="safety_notes" class="block text-sm font-medium text-gray-700 mb-2">Safety Notes / Precautions</label>
                    <textarea name="safety_notes" id="safety_notes" rows="2" placeholder="Any additional safety notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('safety_notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 📊 7. Spray Logs -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📊 7. Spray Logs</h2>
            <p class="text-sm text-gray-600 mb-4">Record actual spray details (can be updated after spraying).</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="application_method" class="block text-sm font-medium text-gray-700 mb-2">Application Method</label>
                    <input type="text" name="application_method" id="application_method" value="{{ old('application_method') }}" placeholder="e.g., foliar spray" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Observations (Pest reduced? Crop reaction?)</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 transition">
                <i class="fas fa-save mr-2"></i>Create Schedule
            </button>
        </div>
    </form>
</div>
@endsection