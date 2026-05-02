@extends('layouts.MainLayout')

@section('title', 'Edit Planting Schedule - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Edit Planting Schedule</h1>
        <a href="{{ route('planting-schedules.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to List</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <x-ui.card>
        <form action="{{ route('planting-schedules.update', $plantingSchedule->id) }}" method="POST" id="scheduleEditForm">
            @csrf
            @method('PUT')

            <!-- Crop Selection -->
            <div class="mb-6">
                <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Crop *</label>
                <select name="crop_id" id="crop_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('crop_id') border-red-500 @enderror">
                    <option value="">Select crop...</option>
                    @foreach($crops as $crop)
                        <option value="{{ $crop->id }}" {{ $plantingSchedule->crop_id == $crop->id ? 'selected' : '' }}>
                            {{ $crop->name }}
                        </option>
                    @endforeach
                </select>
                @error('crop_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Field -->
                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field</label>
                    <select name="field_id" id="field_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('field_id') border-red-500 @enderror">
                        <option value="">Select field...</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ $plantingSchedule->field_id == $field->id ? 'selected' : '' }}>
                                {{ $field->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Farm -->
                <div>
                    <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                    <select name="farm_id" id="farm_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('farm_id') border-red-500 @enderror">
                        <option value="">Select farm...</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ $plantingSchedule->farm_id == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Planting Date -->
                <div>
                    <label for="planting_date" class="block text-sm font-medium text-gray-700 mb-2">Planting Date *</label>
                    <input type="date" name="planting_date" id="planting_date" 
                        value="{{ $plantingSchedule->planting_date->format('Y-m-d') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('planting_date') border-red-500 @enderror">
                    @error('planting_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected Harvest Date -->
                <div>
                    <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Harvest Date</label>
                    <input type="date" name="expected_harvest_date" id="expected_harvest_date" 
                        value="{{ $plantingSchedule->expected_harvest_date ? $plantingSchedule->expected_harvest_date->format('Y-m-d') : '' }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('expected_harvest_date') border-red-500 @enderror">
                    @error('expected_harvest_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Season -->
                <div>
                    <label for="season" class="block text-sm font-medium text-gray-700 mb-2">Season *</label>
                    <select name="season" id="season" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('season') border-red-500 @enderror">
                        <option value="">Select season...</option>
                        <option value="spring" {{ $plantingSchedule->season == 'spring' ? 'selected' : '' }}>Spring</option>
                        <option value="summer" {{ $plantingSchedule->season == 'summer' ? 'selected' : '' }}>Summer</option>
                        <option value="fall" {{ $plantingSchedule->season == 'fall' ? 'selected' : '' }}>Fall</option>
                        <option value="winter" {{ $plantingSchedule->season == 'winter' ? 'selected' : '' }}>Winter</option>
                        <option value="year_round" {{ $plantingSchedule->season == 'year_round' ? 'selected' : '' }}>Year Round</option>
                    </select>
                    @error('season')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="">Select status...</option>
                        <option value="planned" {{ $plantingSchedule->status == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="planted" {{ $plantingSchedule->status == 'planted' ? 'selected' : '' }}>Planted</option>
                        <option value="growing" {{ $plantingSchedule->status == 'growing' ? 'selected' : '' }}>Growing</option>
                        <option value="ready_for_harvest" {{ $plantingSchedule->status == 'ready_for_harvest' ? 'selected' : '' }}>Ready for Harvest</option>
                        <option value="harvested" {{ $plantingSchedule->status == 'harvested' ? 'selected' : '' }}>Harvested</option>
                        <option value="cancelled" {{ $plantingSchedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Completion Percentage -->
                <div>
                    <label for="completion_percentage" class="block text-sm font-medium text-gray-700 mb-2">Completion % *</label>
                    <input type="number" name="completion_percentage" id="completion_percentage" 
                        value="{{ old('completion_percentage', $plantingSchedule->completion_percentage) }}" min="0" max="100" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('completion_percentage') border-red-500 @enderror">
                    @error('completion_percentage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Quantity -->
                <div>
                    <label for="estimated_quantity" class="block text-sm font-medium text-gray-700 mb-2">Est. Quantity</label>
                    <input type="number" name="estimated_quantity" id="estimated_quantity" value="{{ old('estimated_quantity', $plantingSchedule->estimated_quantity) }}" 
                        step="0.01" min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('estimated_quantity') border-red-500 @enderror">
                    @error('estimated_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity Unit -->
                <div>
                    <label for="quantity_unit" class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                    <input type="text" name="quantity_unit" id="quantity_unit" value="{{ old('quantity_unit', $plantingSchedule->quantity_unit) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('quantity_unit') border-red-500 @enderror"
                        placeholder="e.g., kg, bags, tons">
                    @error('quantity_unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Variety -->
                <div>
                    <label for="variety" class="block text-sm font-medium text-gray-700 mb-2">Variety</label>
                    <input type="text" name="variety" id="variety" value="{{ old('variety', $plantingSchedule->variety) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('variety') border-red-500 @enderror"
                        placeholder="e.g., Hybrid, Organic, Local">
                    @error('variety')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Stage -->
                <div>
                    <label for="current_stage" class="block text-sm font-medium text-gray-700 mb-2">Current Stage</label>
                    <input type="text" name="current_stage" id="current_stage" value="{{ old('current_stage', $plantingSchedule->current_stage) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('current_stage') border-red-500 @enderror"
                        placeholder="e.g., Germination, Flowering">
                    @error('current_stage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                    placeholder="Additional notes about this planting schedule...">{{ old('notes', $plantingSchedule->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('planting-schedules.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl shadow-md hover:bg-emerald-700 hover:shadow-lg active:scale-95 transition">
                    <i class="fas fa-save mr-2"></i>
                    Update Schedule
                </button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
