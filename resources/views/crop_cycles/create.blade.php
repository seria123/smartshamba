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
                            <option value="fertilizer">Fertilizer</option>
                            <option value="pesticide">Pesticide</option>
                            <option value="herbicide">Herbicide</option>
                            <option value="fungicide">Fungicide</option>
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
                            <option value="fertilizer">Fertilizer</option>
                            <option value="pesticide">Pesticide</option>
                            <option value="herbicide">Herbicide</option>
                            <option value="fungicide">Fungicide</option>
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
