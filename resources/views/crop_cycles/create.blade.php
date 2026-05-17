@extends('layouts.MainLayout')

@section('title', 'New Crop Cycle - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌾 New Crop Cycle</h1>
        <a href="{{ route('crop_cycles.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <form action="{{ route('crop_cycles.store') }}" method="POST" class="space-y-8" id="cropCycleForm">
        @csrf

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
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Field *</label>
                    <select name="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select field...</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}">{{ $field->name }} ({{ $field->farm->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Crop *</label>
                    <select name="crop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select crop...</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}">{{ $crop->name }} ({{ $crop->category ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Variety</label>
                    <input type="text" name="variety" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., hybrid, local, improved">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select category...</option>
                        <option value="cereal">Cereal</option>
                        <option value="vegetable">Vegetable</option>
                        <option value="fruit">Fruit</option>
                        <option value="legume">Legume</option>
                        <option value="tubers">Tubers</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Season Type</label>
                    <select name="season" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select season...</option>
                        <option value="rain-fed">Rain-fed</option>
                        <option value="irrigated">Irrigated</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Date *</label>
                    <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Harvest Date</label>
                    <input type="date" name="expected_harvest_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Land & Soil Requirements -->
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
                        <option value="clay">Clay</option>
                        <option value="sandy">Sandy</option>
                        <option value="loamy">Loamy</option>
                        <option value="silty">Silty</option>
                        <option value="peaty">Peaty</option>
                        <option value="chalky">Chalky</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Soil pH Level</label>
                    <input type="number" name="ph_level" step="0.1" min="0" max="14" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 6.5">
                </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Previous Crop (Rotation)</label>
                        <select name="previous_crop_cycle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select previous crop...</option>
                            @foreach($cropCycles as $prev)
                                <option value="{{ $prev->id }}">{{ $prev->crop->name ?? $prev->crop_name }} ({{ $prev->start_date->format('Y') }})</option>
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
                        <option value="rain-fed">Rain-fed</option>
                        <option value="drip">Drip Irrigation</option>
                        <option value="sprinkler">Sprinkler</option>
                        <option value="flood">Flood/Furrow</option>
                        <option value="micro">Micro-sprinkler</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Water Source</label>
                    <input type="text" name="water_source_override" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., borehole, river, rain">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Drainage Condition</label>
                    <select name="drainage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select condition...</option>
                        <option value="good">Good</option>
                        <option value="moderate">Moderate</option>
                        <option value="poor">Poor</option>
                        <option value="waterlogged">Waterlogged</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Watering Schedule</label>
                <textarea name="irrigation_schedule" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Every 3 days, 2 hours in morning"></textarea>
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
                        <input type="text" name="input_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., DAP, Maize H1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="input_quantity[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Date</label>
                        <input type="date" name="input_date[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                        <input type="number" name="input_cost[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
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
                    <input type="text" name="seed_batch_number" id="seed_batch_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Seed lot number or nursery batch">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seed Quantity</label>
                    <input type="number" name="seed_quantity" id="seed_quantity" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 2 kg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seedling Quantity</label>
                    <input type="number" name="seedling_quantity" id="seedling_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area Planted (m²)</label>
                    <input type="number" name="area_planted" id="area" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row Spacing (cm)</label>
                    <input type="number" name="spacing_row" id="spacing_row" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plant Spacing (cm)</label>
                    <input type="number" name="spacing_plant" id="spacing_plant" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 60">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Plant Population</label>
                    <input type="number" name="plant_population" id="plant_population" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Germination Rate (%)</label>
                    <input type="number" name="germination_rate" id="germination_rate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Survival Rate (%)</label>
                    <input type="number" name="survival_rate" id="survival_rate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Survivors</label>
                    <input type="number" name="survival_count" id="survival_count" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor (Workers)</label>
                    <input type="number" name="planting_labor_workers" id="planting_labor_workers" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Number of workers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor Cost (KES)</label>
                    <input type="number" name="planting_labor_cost" id="planting_labor_cost" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Total cost">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seed Batch Number</label>
                    <input type="text" name="seed_batch_number" id="seed_batch_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Seed lot number or nursery batch">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seedling Quantity</label>
                    <input type="number" name="seedling_quantity" id="seedling_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area Planted (m²)</label>
                    <input type="number" name="area_planted" id="area" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row Spacing (cm)</label>
                    <input type="number" name="spacing_row" id="spacing_row" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 30">
                </div>
               
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Germination Rate (%)</label>
                    <input type="number" name="germination_rate" id="germination_rate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Survival Rate (%)</label>
                    <input type="number" name="survival_rate" id="survival_rate" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 85">
                </div>
            </div>
               
            </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plant Spacing (cm)</label>
                    <input type="number" name="spacing_plant" id="spacing_plant" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 60">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expected Plant Population</label>
                    <input type="number" name="plant_population" id="plant_population" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Survivors</label>
                    <input type="number" name="survival_count" id="survival_count" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Calculated automatically">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor (Workers)</label>
                    <input type="number" name="planting_labor_workers" id="planting_labor_workers" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Number of workers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planting Labor Cost (KES)</label>
                    <input type="number" name="planting_labor_cost" id="planting_labor_cost" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Total cost">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Planting Notes</label>
                <textarea name="planting_notes" id="planting_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Poor rainfall, delayed transplant"></textarea>
            </div>
        </div>

        <script>
                function addInputRow() {
                const container = document.getElementById('inputsContainer');
                const row = document.createElement('div');
                row.className = 'input-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 bg-gray-50 rounded-lg';
                row.innerHTML = `
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
                        <input type="text" name="input_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., DAP, Maize H1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="input_quantity[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Date</label>
                        <input type="date" name="input_date[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                        <input type="number" name="input_cost[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., 5000">
                    </div>
                `;
                container.appendChild(row);
            }
        </script>
    </div>

    <!-- Submit -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('crop_cycles.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancel
        </a>
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
            <i class="fas fa-save mr-2"></i> Create Crop Cycle
        </button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
  
function calculatePlantPopulation() {
    // Get values safely
    const spacingRow = parseFloat(document.getElementById('spacing_row')?.value);
    const spacingPlant = parseFloat(document.getElementById('spacing_plant')?.value);
    const area = parseFloat(document.getElementById('area')?.value);

    const seedQuantity = parseFloat(document.getElementById('seed_quantity')?.value) || 0;
    const seedlingQuantity = parseInt(document.getElementById('seedling_quantity')?.value) || 0;

    const germinationRate = parseFloat(document.getElementById('germination_rate')?.value) || 0;
    const survivalRate = parseFloat(document.getElementById('survival_rate')?.value) || 0;

    // =========================
    // 🌱 Estimated Population
    // =========================
    let estimatedPopulation = '';

    if (!isNaN(spacingRow) && !isNaN(spacingPlant) && !isNaN(area) &&
        spacingRow > 0 && spacingPlant > 0 && area > 0) {

        const plantsPerSqM = 10000 / (spacingRow * spacingPlant);
        estimatedPopulation = Math.round(plantsPerSqM * area);
    }

    document.getElementById('plant_population').value = estimatedPopulation;

    // =========================
    // 🌾 Actual Population
    // =========================
    const actualPlanted = seedlingQuantity > 0
        ? seedlingQuantity
        : (seedQuantity > 0 ? seedQuantity * 1000 : 0);

    // =========================
    // 🌿 Survival Count
    // =========================
    let survivalCount = '';

    if (actualPlanted > 0) {
        if (germinationRate > 0) {
            survivalCount = Math.round(actualPlanted * (germinationRate / 100));
        } else if (survivalRate > 0) {
            survivalCount = Math.round(actualPlanted * (survivalRate / 100));
        }
    }

    document.getElementById('survival_count').value = survivalCount;
}

// =========================
// ⚡ Event Binding
// =========================
function initPlantCalculator() {
    const inputs = [
        'spacing_row', 'spacing_plant', 'area',
        'seed_quantity', 'seedling_quantity',
        'germination_rate', 'survival_rate'
    ];

    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', calculatePlantPopulation);
        }
    });

    // Run once on load
    calculatePlantPopulation();
}

// Ensure it always runs
window.addEventListener('load', initPlantCalculator);

</script>
</body>
</html>
