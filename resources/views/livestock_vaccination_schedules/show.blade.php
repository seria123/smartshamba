@extends('layouts.MainLayout')

@section('title', 'Vaccination Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Vaccination Schedule Details</h1>
        <a href="{{ route('livestock_vaccination_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Vaccine Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->vaccine_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Vaccine Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->vaccine_type ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Animal</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->livestock->name ?? ($livestockVaccinationSchedule->livestock->tag_number ?? ($livestockVaccinationSchedule->livestockType->name ?? 'N/A')) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Administered Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->administered_date?->format('M d, Y') ?? 'Not Administered' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                    @if($livestockVaccinationSchedule->status === 'administered') bg-green-100 text-green-800
                    @elseif($livestockVaccinationSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($livestockVaccinationSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->cost ? 'KES ' . number_format($livestockVaccinationSchedule->cost, 2) : 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Next Due Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockVaccinationSchedule->next_due_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>

        @if($livestockVaccinationSchedule->administered_by)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Administered By</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->administered_by }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->batch_number)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Batch Number</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->batch_number }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->notes }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->animal_weight)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Animal Weight</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->animal_weight }} kg</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->route)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Administration Route</h3>
                <p class="text-gray-900">{{ strtoupper($livestockVaccinationSchedule->route) }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->dose_amount)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Dose Amount</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->dose_amount }} {{ $livestockVaccinationSchedule->dose_unit ?? '' }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->evidence_photo_path)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Evidence Photo</h3>
                <a href="{{ asset('storage/' . $livestockVaccinationSchedule->evidence_photo_path) }}" target="_blank">
                    <img src="{{ asset('storage/' . $livestockVaccinationSchedule->evidence_photo_path) }}" alt="Evidence" class="mt-2 max-w-xs rounded-lg">
                </a>
            </div>
        @endif

        @if($livestockVaccinationSchedule->vet_notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Vet Notes</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->vet_notes }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->location)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Location</h3>
                <p class="text-gray-900">{{ $livestockVaccinationSchedule->location }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->season)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Season</h3>
                <p class="text-gray-900">{{ ucfirst($livestockVaccinationSchedule->season) }}</p>
            </div>
        @endif

        @if($livestockVaccinationSchedule->is_bulk_entry)
            <div class="mt-6">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-800">
                    Bulk Entry
                </span>
            </div>
        @endif

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('livestock_vaccination_schedules.edit', $livestockVaccinationSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('livestock_vaccination_schedules.destroy', $livestockVaccinationSchedule->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection