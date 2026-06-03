@extends('layouts.MainLayout')

@section('title', 'Edit Vaccination Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit Vaccination Schedule</h1>
        <a href="{{ route('livestock_vaccination_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock_vaccination_schedules.update', $livestockVaccinationSchedule->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
             
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="livestock_id" class="block text-sm font-medium text-gray-700 mb-2">Animal</label>
                    <select name="livestock_id" id="livestock_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Animal</option>
                        @foreach($livestocks as $livestock)
                            <option value="{{ $livestock->id }}" {{ $livestockVaccinationSchedule->livestock_id == $livestock->id ? 'selected' : '' }}>{{ $livestock->name ?? $livestock->tag_number ?? 'Animal #' . $livestock->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="livestock_type_id" class="block text-sm font-medium text-gray-700 mb-2">Animal Type</label>
                    <select name="livestock_type_id" id="livestock_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Type</option>
                        @foreach($livestockTypes as $type)
                            <option value="{{ $type->id }}" {{ $livestockVaccinationSchedule->livestock_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="animal_weight" class="block text-sm font-medium text-gray-700 mb-2">Animal Weight (kg)</label>
                    <input type="number" name="animal_weight" id="animal_weight" value="{{ old('animal_weight', $livestockVaccinationSchedule->animal_weight) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name *</label>
                    <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name', $livestockVaccinationSchedule->vaccine_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type</label>
                    <input type="text" name="vaccine_type" id="vaccine_type" value="{{ old('vaccine_type', $livestockVaccinationSchedule->vaccine_type) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="route" class="block text-sm font-medium text-gray-700 mb-2">Administration Route</label>
                    <select name="route" id="route" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Route</option>
                        <option value="IM" {{ (old('route', $livestockVaccinationSchedule->route)) == 'IM' ? 'selected' : '' }}>Intramuscular (IM)</option>
                        <option value="SC" {{ (old('route', $livestockVaccinationSchedule->route)) == 'SC' ? 'selected' : '' }}>Subcutaneous (SC)</option>
                        <option value="oral" {{ (old('route', $livestockVaccinationSchedule->route)) == 'oral' ? 'selected' : '' }}>Oral</option>
                        <option value="intradermal" {{ (old('route', $livestockVaccinationSchedule->route)) == 'intradermal' ? 'selected' : '' }}>Intradermal</option>
                        <option value="nasal" {{ (old('route', $livestockVaccinationSchedule->route)) == 'nasal' ? 'selected' : '' }}>Nasal</option>
                    </select>
                </div>

                <div>
                    <label for="dose_amount" class="block text-sm font-medium text-gray-700 mb-2">Dose Amount</label>
                    <input type="number" name="dose_amount" id="dose_amount" value="{{ old('dose_amount', $livestockVaccinationSchedule->dose_amount) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="dose_unit" class="block text-sm font-medium text-gray-700 mb-2">Dose Unit</label>
                    <input type="text" name="dose_unit" id="dose_unit" value="{{ old('dose_unit', $livestockVaccinationSchedule->dose_unit) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="e.g., ml, mg">
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $livestockVaccinationSchedule->scheduled_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $livestockVaccinationSchedule->cost) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="next_due_date" class="block text-sm font-medium text-gray-700 mb-2">Next Due Date</label>
                    <input type="date" name="next_due_date" id="next_due_date" value="{{ old('next_due_date', $livestockVaccinationSchedule->next_due_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        <option value="scheduled" {{ (old('status', $livestockVaccinationSchedule->status)) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="administered" {{ (old('status', $livestockVaccinationSchedule->status)) == 'administered' ? 'selected' : '' }}>Administered</option>
                        <option value="missed" {{ (old('status', $livestockVaccinationSchedule->status)) == 'missed' ? 'selected' : '' }}>Missed</option>
                        <option value="cancelled" {{ (old('status', $livestockVaccinationSchedule->status)) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <label for="administered_date" class="block text-sm font-medium text-gray-700 mb-2">Administered Date</label>
                <input type="date" name="administered_date" id="administered_date" value="{{ old('administered_date', $livestockVaccinationSchedule->administered_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                <input type="text" name="administered_by" id="administered_by" value="{{ old('administered_by', $livestockVaccinationSchedule->administered_by) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number', $livestockVaccinationSchedule->batch_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes', $livestockVaccinationSchedule->notes) }}</textarea>
            </div>

            <div class="mt-6">
                <label for="evidence_photo_path" class="block text-sm font-medium text-gray-700 mb-2">Evidence Photo (Vaccine Vial/Receipt)</label>
                <input type="file" name="evidence_photo_path" id="evidence_photo_path" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" accept="image/*">
                @if($livestockVaccinationSchedule->evidence_photo_path)
                    <p class="mt-2 text-sm text-gray-600">Current photo: <a href="{{ asset('storage/' . $livestockVaccinationSchedule->evidence_photo_path) }}" target="_blank">View</a></p>
                @endif
            </div>

            <div class="mt-6">
                <label for="qr_code_data" class="block text-sm font-medium text-gray-700 mb-2">QR/Barcode Data</label>
                <input type="text" name="qr_code_data" id="qr_code_data" value="{{ old('qr_code_data', $livestockVaccinationSchedule->qr_code_data) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="Scan or enter QR/barcode data">
            </div>

            <div class="mt-6">
                <label for="vet_notes" class="block text-sm font-medium text-gray-700 mb-2">Vet Notes</label>
                <textarea name="vet_notes" id="vet_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('vet_notes', $livestockVaccinationSchedule->vet_notes) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location (for Disease Trends)</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $livestockVaccinationSchedule->location) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="e.g., Nairobi, Kisumu">
                </div>

                <div>
                    <label for="season" class="block text-sm font-medium text-gray-700 mb-2">Season</label>
                    <select name="season" id="season" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Season</option>
                        <option value="spring" {{ (old('season', $livestockVaccinationSchedule->season)) == 'spring' ? 'selected' : '' }}>Spring</option>
                        <option value="summer" {{ (old('season', $livestockVaccinationSchedule->season)) == 'summer' ? 'selected' : '' }}>Summer</option>
                        <option value="autumn" {{ (old('season', $livestockVaccinationSchedule->season)) == 'autumn' ? 'selected' : '' }}>Autumn</option>
                        <option value="winter" {{ (old('season', $livestockVaccinationSchedule->season)) == 'winter' ? 'selected' : '' }}>Winter</option>
                        <option value="rainy" {{ (old('season', $livestockVaccinationSchedule->season)) == 'rainy' ? 'selected' : '' }}>Rainy Season</option>
                        <option value="dry" {{ (old('season', $livestockVaccinationSchedule->season)) == 'dry' ? 'selected' : '' }}>Dry Season</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center">
                <div class="flex items-center">
                    <input type="checkbox" name="is_bulk_entry" id="is_bulk_entry" value="1" {{ (old('is_bulk_entry', $livestockVaccinationSchedule->is_bulk_entry)) ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary">
                </div>
                <label for="is_bulk_entry" class="ml-2 block text-sm font-medium text-gray-700">Bulk Entry for Herd</label>
            </div>

            <div class="mt-6">
                <label for="user_role" class="block text-sm font-medium text-gray-700 mb-2">Your Role</label>
                <select name="user_role" id="user_role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="worker" {{ (old('user_role', $livestockVaccinationSchedule->user_role)) == 'worker' ? 'selected' : '' }}>Worker (Data Entry)</option>
                    <option value="vet" {{ (old('user_role', $livestockVaccinationSchedule->user_role)) == 'vet' ? 'selected' : '' }}>Vet (Can Approve)</option>
                    <option value="admin" {{ (old('user_role', $livestockVaccinationSchedule->user_role)) == 'admin' ? 'selected' : '' }}>Admin/Farmer (Full Access)</option>
                </select>
            </div>

            <div class="mt-6">
                <label for="vet_approved_by" class="block text-sm font-medium text-gray-700 mb-2">Approved By Vet</label>
                <select name="vet_approved_by" id="vet_approved_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Vet (Optional)</option>
                    @foreach(App\Models\User::whereIn('role', ['vet', 'admin'])->get() as $vet)
                        <option value="{{ $vet->id }}" {{ (old('vet_approved_by', $livestockVaccinationSchedule->vet_approved_by)) == $vet->id ? 'selected' : '' }}>
                            {{ $vet->name ?? $vet->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($livestockVaccinationSchedule->vet_approved_at)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vet Approved At</label>
                    <p class="text-gray-600">{{ $livestockVaccinationSchedule->vet_approved_at?->format('M d, Y h:i A') }}</p>
                </div>
            @endif

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection