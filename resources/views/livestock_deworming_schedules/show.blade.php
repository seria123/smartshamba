@extends('layouts.MainLayout')

@section('title', 'Deworming Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Deworming Schedule Details</h1>
        <a href="{{ route('livestock_deworming_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Dewormer Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->dewormer_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Drug Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->dewormer_type ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Animal</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->livestock->name ?? ($livestockDewormingSchedule->livestock->tag_number ?? ($livestockDewormingSchedule->livestockType->name ?? 'N/A')) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Breed</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->breed ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Date of Birth</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->date_of_birth?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Weight (kg)</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->weight ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Group / Herd / Pen</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->group_herd_pen ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Manufacturer / Brand</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->manufacturer_brand ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Quantity</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->quantity }} {{ $livestockDewormingSchedule->unit }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Expiry Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->expiry_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Administered Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->administered_date?->format('M d, Y') ?? 'Not Administered' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                        @if($livestockDewormingSchedule->status === 'administered') bg-green-100 text-green-800
                        @elseif($livestockDewormingSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($livestockDewormingSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->cost ? 'KES ' . number_format($livestockDewormingSchedule->cost, 2) : 'N/A' }}</p>
            </div
            <div>
                <h3 class="text-sm font-medium text-gray-500">Drug Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->dewormer_type ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Animal</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->livestock->name ?? ($livestockDewormingSchedule->livestock->tag_number ?? ($livestockDewormingSchedule->livestockType->name ?? 'N/A')) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Breed</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->breed ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Group / Herd / Pen</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->group_herd_pen ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Manufacturer / Brand</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->manufacturer_brand ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Quantity</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->quantity }} {{ $livestockDewormingSchedule->unit }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Expiry Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->expiry_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Administered Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->administered_date?->format('M d, Y') ?? 'Not Administered' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                        @if($livestockDewormingSchedule->status === 'administered') bg-green-100 text-green-800
                        @elseif($livestockDewormingSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($livestockDewormingSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->cost ? 'KES ' . number_format($livestockDewormingSchedule->cost, 2) : 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Next Due Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->next_due_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Deworming Frequency</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->deworming_frequency ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Reminder</h3>
                <p class="text-lg font-semibold text-gray-900>{{ $livestockDewormingSchedule->reminder_toggle ? 'Yes' : 'No' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Reminder Method</h3>
                <p class="text-lg font-semibold text-gray-900>{{ ucfirst($livestockDewormingSchedule->reminder_method ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Body Condition Score</h3>
                <p class="text-lg font-semibold text-gray-900>{{ $livestockDewormingSchedule->body_condition_score ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Current Weight (kg)</h3>
                <p class="text-lg font-semibold text-gray-900>{{ $livestockDewormingSchedule->current_weight ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Previous Deworming Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->previous_deworming_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Administration Method</h3>
                <p class="text-lg font-semibold text-gray-900>{{ ucfirst($livestockDewormingSchedule->administration_method ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Supervised By</h3>
                <p class="text-lg font-semibold text-gray-900>{{ $livestockDewormingSchedule->supervised_by ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Farm Location / Field</h3>
                <p class="text-lg font-semibold text-gray-900>{{ $livestockDewormingSchedule->farm_location ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Animal Reaction</h3>
                <p class="text-lg font-semibold text-gray-900>{{ ucfirst($livestockDewormingSchedule->animal_reaction ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Effectiveness</h3>
                <p class="text-lg font-semibold text-gray-900>{{ ucfirst($livestockDewormingSchedule->effectiveness ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Next Due Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->next_due_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Deworming Frequency</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->deworming_frequency ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Reminder</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->reminder_toggle ? 'Yes' : 'No' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Reminder Method</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->reminder_method ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Body Condition Score</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->body_condition_score ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Administration Method</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->administration_method ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Supervised By</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockDewormingSchedule->supervised_by ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Animal Reaction</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->animal_reaction ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Effectiveness</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($livestockDewormingSchedule->effectiveness ?? 'N/A') }}</p>
            </div>
        </div>

        @if($livestockDewormingSchedule->administered_by)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Administered By</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->administered_by }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->batch_number)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Batch Number</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->batch_number }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->signs_of_infection && is_array($livestockDewormingSchedule->signs_of_infection) && count($livestockDewormingSchedule->signs_of_infection) > 0)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Signs of Infection</h3>
                <p class="text-gray-900">{{ implode(', ', $livestockDewormingSchedule->signs_of_infection) }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->resistance_history)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Resistance History</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->resistance_history }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->notes }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Additional Notes</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->notes }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->batch_number)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Batch Number</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->batch_number }}</p>
            </div>
        @endif

        @if($livestockDewormingSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <p class="text-gray-900">{{ $livestockDewormingSchedule->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('livestock_deworming_schedules.edit', $livestockDewormingSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('livestock_deworming_schedules.destroy', $livestockDewormingSchedule->id) }}" method="POST" class="inline">
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