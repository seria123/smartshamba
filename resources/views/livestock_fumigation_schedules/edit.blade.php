@extends('layouts.MainLayout')

@section('title', 'Edit Fumigation Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Fumigation Schedule</h1>
        <a href="{{ route('livestock_fumigation_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock_fumigation_schedules.update', $livestockFumigationSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                    <select name="farm_id" id="farm_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Farm</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ $livestockFumigationSchedule->farm_id == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="fumigant_name" class="block text-sm font-medium text-gray-700 mb-2">Fumigant Name *</label>
                    <input type="text" name="fumigant_name" id="fumigant_name" value="{{ old('fumigant_name', $livestockFumigationSchedule->fumigant_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="fumigation_type" class="block text-sm font-medium text-gray-700 mb-2">Fumigation Type</label>
                    <input type="text" name="fumigation_type" id="fumigation_type" value="{{ old('fumigation_type', $livestockFumigationSchedule->fumigation_type) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $livestockFumigationSchedule->quantity) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $livestockFumigationSchedule->unit) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="area_covered" class="block text-sm font-medium text-gray-700 mb-2">Area Covered</label>
                    <input type="text" name="area_covered" id="area_covered" value="{{ old('area_covered', $livestockFumigationSchedule->area_covered) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $livestockFumigationSchedule->scheduled_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="performed_date" class="block text-sm font-medium text-gray-700 mb-2">Performed Date</label>
                    <input type="date" name="performed_date" id="performed_date" value="{{ old('performed_date', $livestockFumigationSchedule->performed_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="scheduled" {{ $livestockFumigationSchedule->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ $livestockFumigationSchedule->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="missed" {{ $livestockFumigationSchedule->status == 'missed' ? 'selected' : '' }}>Missed</option>
                        <option value="cancelled" {{ $livestockFumigationSchedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $livestockFumigationSchedule->cost) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="next_due_date" class="block text-sm font-medium text-gray-700 mb-2">Next Due Date</label>
                    <input type="date" name="next_due_date" id="next_due_date" value="{{ old('next_due_date', $livestockFumigationSchedule->next_due_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="mt-6">
                <label for="performed_by" class="block text-sm font-medium text-gray-700 mb-2">Performed By</label>
                <input type="text" name="performed_by" id="performed_by" value="{{ old('performed_by', $livestockFumigationSchedule->performed_by) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes', $livestockFumigationSchedule->notes) }}</textarea>
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