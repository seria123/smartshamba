@extends('layouts.MainLayout')

@section('title', 'Spraying Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Spraying Schedule Details</h1>
        <a href="{{ route('spraying_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Spray Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($sprayingSchedule->spray_type) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Chemical Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->chemical_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Crop/Field</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->crop->name ?? ($sprayingSchedule->field->name ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Quantity</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->quantity }} {{ $sprayingSchedule->unit }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Applied Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->applied_date?->format('M d, Y') ?? 'Not Applied' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                    @if($sprayingSchedule->status === 'completed') bg-green-100 text-green-800
                    @elseif($sprayingSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($sprayingSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $sprayingSchedule->cost ? 'KES ' . number_format($sprayingSchedule->cost, 2) : 'N/A' }}</p>
            </div>
        </div>

        @if($sprayingSchedule->application_method)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Application Method</h3>
                <p class="text-gray-900">{{ $sprayingSchedule->application_method }}</p>
            </div>
        @endif

        @if($sprayingSchedule->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <p class="text-gray-900">{{ $sprayingSchedule->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('spraying_schedules.edit', $sprayingSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('spraying_schedules.destroy', $sprayingSchedule->id) }}" method="POST" class="inline">
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