@extends('layouts.MainLayout')

@section('title', 'Create Pest Control Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Pest Control Schedule</h1>
        <a href="{{ route('pest_control_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('pest_control_schedules.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pest_name" class="block text-sm font-medium text-gray-700 mb-2">Pest Name *</label>
                    <input type="text" name="pest_name" id="pest_name" value="{{ old('pest_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="threat_level" class="block text-sm font-medium text-gray-700 mb-2">Threat Level *</label>
                    <select name="threat_level" id="threat_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        <option value="medium" {{ old('threat_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ old('threat_level') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="high" {{ old('threat_level') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <div>
                    <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Crop</label>
                    <select name="crop_id" id="crop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Crop</option>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>{{ $crop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field</label>
                    <select name="field_id" id="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Field</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
            </div>

            <div class="mt-6">
                <label for="treatment_method" class="block text-sm font-medium text-gray-700 mb-2">Treatment Method</label>
                <input type="text" name="treatment_method" id="treatment_method" value="{{ old('treatment_method') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection