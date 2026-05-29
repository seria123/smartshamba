@extends('layouts.MainLayout')

@section('title', 'Create Vaccination Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Vaccination Schedule</h1>
        <a href="{{ route('livestock_vaccination_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock_vaccination_schedules.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="livestock_id" class="block text-sm font-medium text-gray-700 mb-2">Animal</label>
                    <select name="livestock_id" id="livestock_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Animal</option>
                        @foreach($livestocks as $livestock)
                            <option value="{{ $livestock->id }}" {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>{{ $livestock->name ?? $livestock->tag_number ?? 'Animal #' . $livestock->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="livestock_type_id" class="block text-sm font-medium text-gray-700 mb-2">Animal Type</label>
                    <select name="livestock_type_id" id="livestock_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Type</option>
                        @foreach($livestockTypes as $type)
                            <option value="{{ $type->id }}" {{ old('livestock_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name *</label>
                    <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type</label>
                    <input type="text" name="vaccine_type" id="vaccine_type" value="{{ old('vaccine_type') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
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
                <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                <input type="text" name="administered_by" id="administered_by" value="{{ old('administered_by') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes') }}</textarea>
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