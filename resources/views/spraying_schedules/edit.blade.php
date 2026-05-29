@extends('layouts.MainLayout')

@section('title', 'Edit Spraying Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Spraying Schedule</h1>
        <a href="{{ route('spraying_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('spraying_schedules.update', $sprayingSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="spray_type" class="block text-sm font-medium text-gray-700 mb-2">Spray Type *</label>
                    <select name="spray_type" id="spray_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        <option value="">Select Type</option>
                        <option value="pesticide" {{ $sprayingSchedule->spray_type == 'pesticide' ? 'selected' : '' }}>Pesticide</option>
                        <option value="herbicide" {{ $sprayingSchedule->spray_type == 'herbicide' ? 'selected' : '' }}>Herbicide</option>
                        <option value="fungicide" {{ $sprayingSchedule->spray_type == 'fungicide' ? 'selected' : '' }}>Fungicide</option>
                        <option value="insecticide" {{ $sprayingSchedule->spray_type == 'insecticide' ? 'selected' : '' }}>Insecticide</option>
                    </select>
                </div>

                <div>
                    <label for="chemical_name" class="block text-sm font-medium text-gray-700 mb-2">Chemical Name *</label>
                    <input type="text" name="chemical_name" id="chemical_name" value="{{ old('chemical_name', $sprayingSchedule->chemical_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Crop</label>
                    <select name="crop_id" id="crop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Crop</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ $sprayingSchedule->crop_id == $crop->id ? 'selected' : '' }}>{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field</label>
                    <select name="field_id" id="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ $sprayingSchedule->field_id == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $sprayingSchedule->quantity) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $sprayingSchedule->unit) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $sprayingSchedule->scheduled_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="applied_date" class="block text-sm font-medium text-gray-700 mb-2">Applied Date</label>
                    <input type="date" name="applied_date" id="applied_date" value="{{ old('applied_date', $sprayingSchedule->applied_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="scheduled" {{ $sprayingSchedule->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ $sprayingSchedule->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $sprayingSchedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $sprayingSchedule->cost) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="mt-6">
                <label for="application_method" class="block text-sm font-medium text-gray-700 mb-2">Application Method</label>
                <input type="text" name="application_method" id="application_method" value="{{ old('application_method', $sprayingSchedule->application_method) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes', $sprayingSchedule->notes) }}</textarea>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection