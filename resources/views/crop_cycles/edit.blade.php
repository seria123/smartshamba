@extends('layouts.MainLayout')

@section('title', 'Edit Crop Cycle - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌾 Edit Crop Cycle</h1>
        <a href="{{ route('crop_cycles.show', $cropCycle) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Cycle
        </a>
    </div>

    <form action="{{ route('crop_cycles.update', $cropCycle) }}" method="POST" class="space-y-8" id="cropCycleForm">
        @csrf
        @method('PUT')

        <!-- Basic Crop Identity -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">1</span>
                Basic Crop Identity
            </h2>
            <p class="text-gray-500 text-sm mb-4">The "passport" of the crop</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Farm *</label>
                    <select name="farm_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select farm...</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id', $cropCycle->farm_id) == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Field *</label>
                    <select name="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select field...</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id', $cropCycle->field_id) == $field->id ? 'selected' : '' }}>{{ $field->name }} ({{ $field->farm->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Crop *</label>
                    <select name="crop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select crop...</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ old('crop_id', $cropCycle->crop_id) == $crop->id ? 'selected' : '' }}>{{ $crop->name }} ({{ $crop->category ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Variety</label>
                    <input type="text" name="variety" value="{{ old('variety', $cropCycle->variety) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., hybrid, local, improved">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select category...</option>
                        <option value="cereal" {{ old('category', $cropCycle->category) == 'cereal' ? 'selected' : '' }}>Cereal</option>
                        <option value="vegetable" {{ old('category', $cropCycle->category) == 'vegetable' ? 'selected' : '' }}>Vegetable</option>
                        <option value="fruit" {{ old('category', $cropCycle->category) == 'fruit' ? 'selected' : '' }}>Fruit</option>
                        <option value="legume" {{ old('category', $cropCycle->category) == 'legume' ? 'selected' : '' }}>Legume</option>
                        <option value="tubers" {{ old('category', $cropCycle->category) == 'tubers' ? 'selected' : '' }}>Tubers</option>
                        <option value="other" {{ old('category', $cropCycle->category) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Season Type</label>
                    <select name="season" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select season...</option>
                        <option value="rain-fed" {{ old('season', $cropCycle->season) == 'rain-fed' ? 'selected' : '' }}>Rain-fed</option>
                        <option value="irrigated" {{ old('season', $cropCycle->season) == 'irrigated' ? 'selected' : '' }}>Irrigated</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $cropCycle->start_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Harvest Date</label>
                    <input type="date" name="expected_harvest_date" value="{{ old('expected_harvest_date', $cropCycle->expected_harvest_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Land & Soil -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">2</span>
                Land & Soil Requirements
            </h2>
            <p class="text-gray-500 text-sm mb-4">Crops are picky tenants</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Soil Type</label>
                    <select name="soil_type_override" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select soil type...</option>
                        <option value="clay" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'clay' ? 'selected' : '' }}>Clay</option>
                        <option value="sandy" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'sandy' ? 'selected' : '' }}>Sandy</option>
                        <option value="loamy" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'loamy' ? 'selected' : '' }}>Loamy</option>
                        <option value="silty" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'silty' ? 'selected' : '' }}>Silty</option>
                        <option value="peaty" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'peaty' ? 'selected' : '' }}>Peaty</option>
                        <option value="chalky" {{ old('soil_type_override', $cropCycle->soil_type_override) == 'chalky' ? 'selected' : '' }}>Chalky</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Soil pH Level</label>
                    <input type="number" name="ph_level" value="{{ old('ph_level', $cropCycle->ph_level) }}" step="0.1" min="0" max="14" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 6.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Previous Crop (Rotation)</label>
                    <select name="previous_crop_cycle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select previous crop...</option>
                        @foreach($cropCycles as $prev)
                            @if($prev->id != $cropCycle->id && $prev->harvests->count() > 0)
                                <option value="{{ $prev->id }}" {{ old('previous_crop_cycle_id', $cropCycle->previous_crop_cycle_id) == $prev->id ? 'selected' : '' }}>{{ $prev->crop->name ?? $prev->crop_name }} ({{ $prev->start_date->format('Y') }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Water & Irrigation -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">3</span>
                Water & Irrigation
            </h2>
            <p class="text-gray-500 text-sm mb-4">Water is the crop's bloodstream</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Irrigation Type</label>
                    <select name="irrigation_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select type...</option>
                        <option value="rain-fed" {{ old('irrigation_type', $cropCycle->irrigation_type) == 'rain-fed' ? 'selected' : '' }}>Rain-fed</option>
                        <option value="drip" {{ old('irrigation_type', $cropCycle->irrigation_type) == 'drip' ? 'selected' : '' }}>Drip Irrigation</option>
                        <option value="sprinkler" {{ old('irrigation_type', $cropCycle->irrigation_type) == 'sprinkler' ? 'selected' : '' }}>Sprinkler</option>
                        <option value="flood" {{ old('irrigation_type', $cropCycle->irrigation_type) == 'flood' ? 'selected' : '' }}>Flood/Furrow</option>
                        <option value="micro" {{ old('irrigation_type', $cropCycle->irrigation_type) == 'micro' ? 'selected' : '' }}>Micro-sprinkler</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Water Source</label>
                    <input type="text" name="water_source_override" value="{{ old('water_source_override', $cropCycle->water_source_override) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., borehole, river, rain">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Drainage Condition</label>
                    <select name="drainage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select condition...</option>
                        <option value="good" {{ old('drainage', $cropCycle->drainage) == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="moderate" {{ old('drainage', $cropCycle->drainage) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="poor" {{ old('drainage', $cropCycle->drainage) == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="waterlogged" {{ old('drainage', $cropCycle->drainage) == 'waterlogged' ? 'selected' : '' }}>Waterlogged</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Watering Schedule</label>
                <textarea name="irrigation_schedule" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Every 3 days, 2 hours in morning">{{ old('irrigation_schedule', $cropCycle->irrigation_schedule) }}</textarea>
            </div>
        </div>

        <!-- Inputs (Seeds, Fertilizers, Agrochemicals) -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">4</span>
                Inputs
            </h2>
            <p class="text-gray-500 text-sm mb-4">This is where money quietly disappears if not tracked well</p>

            <div id="inputsContainer">
                <div class="input-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Input Type</label>
                        <select name="input_type[]" class="input-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select type...</option>
                            <option value="seed">Seed</option>
                            <option value="seedling">Seedling</option>
                            <option value="fertilizer">Fertilizer</option>
                            <option value="pesticide">Pesticide</option>
                            <option value="herbicide">Herbicide</option>
                            <option value="fungicide">Fungicide</option>
                            <option value="water">Water</option>
                            <option value="fuel">Fuel</option>
                            <option value="packaging">Packaging</option>
                            <option value="other">Other Supplies</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" name="input_name[]" value="{{ old('input_name.0') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., DAP, Maize H1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="input_quantity[]" step="0.01" value="{{ old('input_quantity.0') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Date</label>
                        <input type="date" name="input_date[]" value="{{ old('input_date.0') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                        <input type="number" name="input_cost[]" step="0.01" value="{{ old('input_cost.0') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                    </div>
                </div>
            </div>

            <button type="button" onclick="addInputRow()" class="mt-2 text-green-600 hover:text-green-700 text-sm font-medium flex items-center">
                <i class="fas fa-plus mr-1"></i> Add Another Input
            </button>
        </div>

        <!-- Planting Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">5</span>
                Planting Details
            </h2>
            <p class="text-gray-500 text-sm mb-4">Specifics about how the crop was planted</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seed Batch Number</label>
                    <input type="text" name="seed_batch_number" id="seed_batch_number" value="{{ old('seed_batch_number', $cropCycle->seed_batch_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Seed lot number or nursery batch">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seed Quantity</label>
                    <input type="number" name="seed_quantity" id="seed_quantity" step="0.01" value="{{ old('seed_quantity', $cropCycle->seed_quantity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 2 kg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seedling Quantity</label>
                    <input type="number" name="seedling_quantity" id="seedling_quantity" value="{{ old('seedling_quantity', $cropCycle->seedling_quantity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area Planted (m²)</label>
                    <input type="number" name="area_planted" id="area" step="0.01" value="{{ old('area_planted', $cropCycle->area_planted) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row Spacing (cm)</label>
                    <input type="number" name="spacing_row" id="spacing_row" value="{{ old('spacing_row', $cropCycle->spacing_row) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plant Spacing (cm)</label>
                    <input type="number" name="spacing_plant" id="spacing_plant" value="{{ old('spacing_plant', $cropCycle->spacing_plant) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 60">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Plant Population</label>
                    <input type="number" name="plant_population" id="plant_population" value="{{ old('plant_population', $cropCycle->plant_population) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Germination Rate (%)</label>
                    <input type="number" name="germination_rate" id="germination_rate" step="0.01" min="0" max="100" value="{{ old('germination_rate', $cropCycle->germination_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Survival Rate (%)</label>
                    <input type="number" name="survival_rate" id="survival_rate" step="0.01" min="0" max="100" value="{{ old('survival_rate', $cropCycle->survival_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Survivors</label>
                    <input type="number" name="survival_count" id="survival_count" value="{{ old('survival_count', $cropCycle->survival_count) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor (Workers)</label>
                    <input type="number" name="planting_labor_workers" id="planting_labor_workers" value="{{ old('planting_labor_workers', $cropCycle->planting_labor_workers) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Number of workers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor Cost (KES)</label>
                    <input type="number" name="planting_labor_cost" id="planting_labor_cost" step="0.01" value="{{ old('planting_labor_cost', $cropCycle->planting_labor_cost) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Total cost">
                </div>
            </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seed Quantity</label>
                    <input type="number" name="seed_quantity" id="seed_quantity" step="0.01" value="{{ old('seed_quantity', $cropCycle->seed_quantity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 2 kg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seedling Quantity</label>
                    <input type="number" name="seedling_quantity" id="seedling_quantity" value="{{ old('seedling_quantity', $cropCycle->seedling_quantity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area Planted (m²)</label>
                    <input type="number" name="area_planted" id="area" step="0.01" value="{{ old('area_planted', $cropCycle->area_planted) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row Spacing (cm)</label>
                    <input type="number" name="spacing_row" id="spacing_row" value="{{ old('spacing_row', $cropCycle->spacing_row) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plant Spacing (cm)</label>
                    <input type="number" name="spacing_plant" id="spacing_plant" value="{{ old('spacing_plant', $cropCycle->spacing_plant) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 60">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Plant Population</label>
                    <input type="number" name="plant_population" id="plant_population" value="{{ old('plant_population', $cropCycle->plant_population) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Germination Rate (%)</label>
                    <input type="number" name="germination_rate" id="germination_rate" step="0.01" min="0" max="100" value="{{ old('germination_rate', $cropCycle->germination_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Survival Rate (%)</label>
                    <input type="number" name="survival_rate" id="survival_rate" step="0.01" min="0" max="100" value="{{ old('survival_rate', $cropCycle->survival_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Survivors</label>
                    <input type="number" name="survival_count" id="survival_count" value="{{ old('survival_count', $cropCycle->survival_count) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor (Workers)</label>
                    <input type="number" name="planting_labor_workers" id="planting_labor_workers" value="{{ old('planting_labor_workers', $cropCycle->planting_labor_workers) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Number of workers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor Cost (KES)</label>
                    <input type="number" name="planting_labor_cost" id="planting_labor_cost" step="0.01" value="{{ old('planting_labor_cost', $cropCycle->planting_labor_cost) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Total cost">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Planting Notes</label>
                <textarea name="planting_notes" id="planting_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Poor rainfall, delayed transplant">{{ old('planting_notes', $cropCycle->planting_notes) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row Spacing (cm)</label>
                    <input type="number" name="spacing_row" value="{{ old('spacing_row', $cropCycle->spacing_row) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plant Spacing (cm)</label>
                    <input type="number" name="spacing_plant" value="{{ old('spacing_plant', $cropCycle->spacing_plant) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 60">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Plant Population</label>
                    <input type="number" name="plant_population" value="{{ old('plant_population', $cropCycle->plant_population) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Germination Rate (%)</label>
                    <input type="number" name="germination_rate" step="0.01" min="0" max="100" value="{{ old('germination_rate', $cropCycle->germination_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Survival Rate (%)</label>
                    <input type="number" name="survival_rate" step="0.01" min="0" max="100" value="{{ old('survival_rate', $cropCycle->survival_rate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Survivors</label>
                    <input type="number" name="survival_count" value="{{ old('survival_count', $cropCycle->survival_count) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor (Workers)</label>
                    <input type="number" name="planting_labor_workers" value="{{ old('planting_labor_workers', $cropCycle->planting_labor_workers) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Number of workers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor Cost (KES)</label>
                    <input type="number" name="planting_labor_cost" step="0.01" value="{{ old('planting_labor_cost', $cropCycle->planting_labor_cost) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Total cost">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Planting Notes</label>
                <textarea name="planting_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Poor rainfall, delayed transplant">{{ old('planting_notes', $cropCycle->planting_notes) }}</textarea>
            </div>
        </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('crop_cycles.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</a>
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
            <i class="fas fa-save mr-2"></i> Update Crop Cycle
        </button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
    // Auto-calculate plant population and survival count
    function calculatePlantPopulation() {
        const spacingRow = parseFloat(document.getElementById('spacing_row')?.value) || 0;
        const spacingPlant = parseFloat(document.getElementById('spacing_plant')?.value) || 0;
        const seedQuantity = parseFloat(document.getElementById('seed_quantity')?.value) || 0;
        const seedlingQuantity = parseInt(document.getElementById('seedling_quantity')?.value) || 0;
        
        // If we have spacing values, calculate theoretical plant population per unit area
        // Assuming spacing is in cm, we calculate plants per square meter: 10000 / (row_spacing * plant_spacing)
        let plantPopulation = 0;
        if (spacingRow > 0 && spacingPlant > 0) {
            // Plants per square meter
            const plantsPerSqM = 10000 / (spacingRow * spacingPlant);
            // For now, we'll just store the spacing-based calculation as a reference
            // In a real app, you'd multiply by area planted
            plantPopulation = plantsPerSqM;
        }
        
        // If seedling quantity is provided, use that as the actual planted amount
        const actualPlanted = seedlingQuantity > 0 ? seedlingQuantity : seedQuantity * 1000; // Rough conversion: 1kg seed ≈ 1000 seeds
        
        document.getElementById('plant_population')?.value = isFinite(plantPopulation) ? Math.round(plantPopulation) : '';
        
        // Calculate survival count if germination rate is provided
        const germinationRate = parseFloat(document.getElementById('germination_rate')?.value) || 0;
        const survivalRate = parseFloat(document.getElementById('survival_rate')?.value) || 0;
        
        if (germinationRate > 0 && actualPlanted > 0) {
            const expectedSurvivors = actualPlanted * (germinationRate / 100);
            document.getElementById('survival_count')?.value = Math.round(expectedSurvivors);
        } else if (survivalRate > 0 && actualPlanted > 0) {
            const expectedSurvivors = actualPlanted * (survivalRate / 100);
            document.getElementById('survival_count')?.value = Math.round(expectedSurvivors);
        }
    }
    
    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = [
            'spacing_row', 'spacing_plant', 'seed_quantity', 'seedling_quantity',
            'germination_rate', 'survival_rate'
        ];
        
        inputs.forEach(inputId => {
            const element = document.getElementById(inputId);
            if (element) {
                element.addEventListener('change', calculatePlantPopulation);
                element.addEventListener('keyup', calculatePlantPopulation);
            }
        });
    });
</script>
</body>
</html>
