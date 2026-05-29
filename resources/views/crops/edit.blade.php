@extends('layouts.MainLayout')

@section('title', 'Edit Crop - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Crop</h1>
        <a href="{{ route('crops.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Crops
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-6" style="border-left: 4px solid #43A047;">
        <form action="{{ route('crops.update', $crop->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Field -->
                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field *</label>
                    <select name="field_id" id="field_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('field_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id', $crop->field_id) == $field->id ? 'selected' : '' }}>{{ $field->name }} ({{ $field->farm->name }})</option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Crop Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $crop->name) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Variety -->
                <div>
                    <label for="variety" class="block text-sm font-medium text-gray-700 mb-2">Variety</label>
                    <input type="text" name="variety" id="variety" value="{{ old('variety', $crop->variety) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Planting Date -->
                <div>
                    <label for="planting_date" class="block text-sm font-medium text-gray-700 mb-2">Planting Date</label>
                    <input type="date" name="planting_date" id="planting_date" value="{{ old('planting_date', $crop->planting_date?->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Expected Harvest Date -->
                <div>
                    <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Harvest Date</label>
                    <input type="date" name="expected_harvest_date" id="expected_harvest_date" value="{{ old('expected_harvest_date', $crop->expected_harvest_date?->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('notes', $crop->notes) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('crops.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Crop
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
