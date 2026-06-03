@extends('layouts.MainLayout')

@section('title', 'Fumigation Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Fumigation Schedule Details</h1>
        <a href="{{ route('livestock_fumigation_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Fumigant Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->fumigant_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Fumigation Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->fumigation_type ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Farm</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->farm->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Quantity</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->quantity }} {{ $livestockFumigationSchedule->unit }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Area Covered</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->area_covered ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Performed Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->performed_date?->format('M d, Y') ?? 'Not Performed' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                    @if($livestockFumigationSchedule->status === 'completed') bg-green-100 text-green-800
                    @elseif($livestockFumigationSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($livestockFumigationSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->cost ? 'KES ' . number_format($livestockFumigationSchedule->cost, 2) : 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Next Due Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $livestockFumigationSchedule->next_due_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>

        @if($livestockFumigationSchedule->performed_by)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Performed By</h3>
                <p class="text-gray-900">{{ $livestockFumigationSchedule->performed_by }}</p>
            </div>
        @endif

        @if($livestockFumigationSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <p class="text-gray-900">{{ $livestockFumigationSchedule->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('livestock_fumigation_schedules.edit', $livestockFumigationSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('livestock_fumigation_schedules.destroy', $livestockFumigationSchedule->id) }}" method="POST" class="inline">
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