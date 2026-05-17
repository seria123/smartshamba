@extends('layouts.MainLayout')

@section('title', 'Edit Activity - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">✏️ Edit Activity</h1>
        <a href="{{ route('activities.show', $activity) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Details
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('activities.update', $activity) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Basic Information</h3>
                </div>

                <div>
                    <label for="activity_name" class="block text-sm font-medium text-gray-700 mb-2">Activity Name *</label>
                    <input type="text" name="activity_name" id="activity_name" value="{{ old('activity_name', $activity->activity_name) }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('activity_name') border-red-500 @enderror">
                    @error('activity_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                    <select name="activity_type" id="activity_type" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('activity_type') border-red-500 @enderror">
                        @php
                            $types = [
                                'weeding' => 'Weeding',
                                'fertilizer_application' => 'Fertilizer Application',
                                'spraying' => 'Spraying',
                                'irrigation' => 'Irrigation',
                                'pruning_training' => 'Pruning/Training',
                                'scouting_inspection' => 'Scouting/Inspection',
                                'thinning_gapping' => 'Thinning/Gapping',
                                'soil_crop_nutrition' => 'Soil/Crop Nutrition',
                                'harvesting' => 'Harvesting',
                            ];
                        @endphp
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('activity_type', $activity->activity_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('activity_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field *</label>
                    <select name="field_id" id="field_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('field_id') border-red-500 @enderror">
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ old('field_id', $activity->field_id) == $field->id ? 'selected' : '' }}>
                                {{ $field->name }} ({{ $field->size_hectares ?? 'N/A' }} ha)
                            </option>
                        @endforeach
                    </select>
                    @error('field_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="crop_cycle_id" class="block text-sm font-medium text-gray-700 mb-2">Crop Cycle *</label>
                    <select name="crop_cycle_id" id="crop_cycle_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('crop_cycle_id') border-red-500 @enderror">
                        @foreach($cropCycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ old('crop_cycle_id', $activity->crop_cycle_id) == $cycle->id ? 'selected' : '' }}>
                                {{ $cycle->crop_name ?? $cycle->crop->name ?? 'Crop' }} - {{ $cycle->code ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_cycle_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="crop_stage_id" class="block text-sm font-medium text-gray-700 mb-2">Crop Stage *</label>
                    <select name="crop_stage_id" id="crop_stage_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('crop_stage_id') border-red-500 @enderror">
                        @foreach($cropStages as $stage)
                            <option value="{{ $stage->id }}" {{ old('crop_stage_id', $activity->crop_stage_id) == $stage->id ? 'selected' : '' }}>
                                {{ $stage->stage_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('crop_stage_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="activity_date" class="block text-sm font-medium text-gray-700 mb-2">Activity Date *</label>
                    <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date', $activity->activity_date?->format('Y-m-d')) }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('activity_date') border-red-500 @enderror">
                    @error('activity_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">Lead Staff</label>
                    <select name="staff_id" id="staff_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('staff_id') border-red-500 @enderror">
                        <option value="">None</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" {{ old('staff_id', $activity->staff_id) == $staffMember->id ? 'selected' : '' }}>
                                {{ $staffMember->full_name ?? $staffMember->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('staff_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Additional Fields -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700 mt-4">Additional Details</h3>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('description') border-red-500 @enderror">{{ old('description', $activity->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" step="0.01" name="quantity" id="quantity" value="{{ old('quantity', $activity->quantity) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost ($)</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost', $activity->cost) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('cost') border-red-500 @enderror">
                    @error('cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status', $activity->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $activity->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $activity->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">Supervisor</label>
                    <select name="supervisor_id" id="supervisor_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('supervisor_id') border-red-500 @enderror">
                        <option value="">None</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" {{ old('supervisor_id', $activity->supervisor_id) == $staffMember->id ? 'selected' : '' }}>
                                {{ $staffMember->full_name ?? $staffMember->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervisor_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="labor_type" class="block text-sm font-medium text-gray-700 mb-2">Labor Type</label>
                    <input type="text" name="labor_type" id="labor_type" value="{{ old('labor_type', $activity->labor_type) }}"
                           placeholder="e.g., manual, mechanized, contract"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('labor_type') border-red-500 @enderror">
                    @error('labor_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('notes') border-red-500 @enderror">{{ old('notes', $activity->notes) }}</textarea>
                    @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('activities.show', $activity) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Update Activity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
