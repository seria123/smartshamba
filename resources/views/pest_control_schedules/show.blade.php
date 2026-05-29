@extends('layouts.MainLayout')

@section('title', 'Pest Control Schedule Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Pest Control Schedule Details</h1>
        <a href="{{ route('pest_control_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Pest Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->pest_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Threat Level</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                    @if($pestControlSchedule->threat_level === 'high') bg-red-100 text-red-800
                    @elseif($pestControlSchedule->threat_level === 'medium') bg-yellow-100 text-yellow-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ ucfirst($pestControlSchedule->threat_level) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Crop/Field</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->crop->name ?? ($pestControlSchedule->field->name ?? 'N/A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Scheduled Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Inspected Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->inspected_date?->format('M d, Y') ?? 'Not Inspected' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Treatment Date</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->treatment_date?->format('M d, Y') ?? 'Not Treated' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                    @if($pestControlSchedule->status === 'treated') bg-green-100 text-green-800
                    @elseif($pestControlSchedule->status === 'inspected') bg-blue-100 text-blue-800
                    @elseif($pestControlSchedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($pestControlSchedule->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Cost</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $pestControlSchedule->cost ? 'KES ' . number_format($pestControlSchedule->cost, 2) : 'N/A' }}</p>
            </div>
        </div>

        @if($pestControlSchedule->description)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                <p class="text-gray-900">{{ $pestControlSchedule->description }}</p>
            </div>
        @endif

        @if($pestControlSchedule->treatment_method)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Treatment Method</h3>
                <p class="text-gray-900">{{ $pestControlSchedule->treatment_method }}</p>
            </div>
        @endif

        @if($pestControlSchedule->treatment_notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Treatment Notes</h3>
                <p class="text-gray-900">{{ $pestControlSchedule->treatment_notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('pest_control_schedules.edit', $pestControlSchedule->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('pest_control_schedules.destroy', $pestControlSchedule->id) }}" method="POST" class="inline">
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