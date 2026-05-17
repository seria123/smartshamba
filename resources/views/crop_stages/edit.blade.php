@extends('layouts.MainLayout')

@section('title', 'Edit Crop Stage - SmartShamba')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">✏️ Edit Crop Stage</h1>
        <a href="{{ route('crop_stages.show', $cropStage) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Details
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('crop_stages.update', $cropStage) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="crop_cycle_id" class="block text-sm font-medium text-gray-700 mb-2">Crop Cycle *</label>
                    <select name="crop_cycle_id" id="crop_cycle_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('crop_cycle_id') border-red-500 @enderror">
                        @foreach($cropCycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ old('crop_cycle_id', $cropStage->crop_cycle_id) == $cycle->id ? 'selected' : '' }}>
                                {{ $cycle->crop_name ?? $cycle->crop->name ?? 'Crop' }} - {{ $cycle->code ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_cycle_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stage_name" class="block text-sm font-medium text-gray-700 mb-2">Stage Name *</label>
                    <input type="text" name="stage_name" id="stage_name" value="{{ old('stage_name', $cropStage->stage_name) }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('stage_name') border-red-500 @enderror">
                    @error('stage_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $cropStage->start_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $cropStage->end_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('crop_stages.show', $cropStage) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Update Stage
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
