@extends('layouts.MainLayout')

@section('title', 'Create Crop - SmartShamba')

@section('content')
<x-ui.card style="border-left: 4px solid #43A047;">
    <form action="{{ route('crops.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Field -->
                <div>
                    <label for="field_id" class="form-label">Field *</label>
                    <select name="field_id" id="field_id" 
                        class="form-select-modern @error('field_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }} ({{ $field->farm->name }})</option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="form-label">Crop Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="form-input-modern @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Variety -->
                <div>
                    <label for="variety" class="form-label">Variety</label>
                    <input type="text" name="variety" id="variety" value="{{ old('variety') }}" 
                        class="form-input-modern">
                </div>

                <!-- Planting Date -->
                <div>
                    <label for="planting_date" class="form-label">Planting Date</label>
                    <input type="date" name="planting_date" id="planting_date" value="{{ old('planting_date') }}" 
                        class="form-input-modern">
                </div>

                <!-- Expected Harvest Date -->
                <div>
                    <label for="expected_harvest_date" class="form-label">Expected Harvest Date</label>
                    <input type="date" name="expected_harvest_date" id="expected_harvest_date" value="{{ old('expected_harvest_date') }}" 
                        class="form-input-modern">
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" rows="4" 
                    class="form-textarea-modern">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('crops.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Create Crop
                </button>
            </div>
        </form>
</x-ui.card>
@endsection
