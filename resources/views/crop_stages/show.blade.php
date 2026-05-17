@extends('layouts.MainLayout')

@section('title', 'Crop Stage Details - SmartShamba')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌱 {{ $cropStage->stage_name }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('crop_stages.edit', $cropStage) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('crop_stages.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <!-- Stage Header -->
        <div class="border-b pb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $cropStage->stage_name }}</h2>
                    <p class="text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-2"></i>
                        {{ $cropStage->start_date?->format('M d, Y') ?? 'Start: Not set' }}
                        @if($cropStage->end_date)
                            - {{ $cropStage->end_date?->format('M d, Y') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Stage Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Crop Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Crop Cycle</dt>
                        <dd class="text-gray-900">
                            {{ $cropStage->cropCycle->crop_name ?? $cropStage->cropCycle->crop->name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Crop Code</dt>
                        <dd class="text-gray-900">{{ $cropStage->cropCycle->code ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Variety</dt>
                        <dd class="text-gray-900">{{ $cropStage->cropCycle->variety ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Field</dt>
                        <dd class="text-gray-900">{{ $cropStage->cropCycle->field->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Farm</dt>
                        <dd class="text-gray-900">{{ $cropStage->cropCycle->field->farm->name ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Timeline</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500">Start Date</dt>
                        <dd class="text-gray-900">{{ $cropStage->start_date?->format('M d, Y') ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">End Date</dt>
                        <dd class="text-gray-900">{{ $cropStage->end_date?->format('M d, Y') ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Duration</dt>
                        <dd class="text-gray-900">
                            @if($cropStage->start_date && $cropStage->end_date)
                                {{ $cropStage->start_date->diffInDays($cropStage->end_date) }} days
                            @else
                                Not determined
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($cropStage->cropCycle && $cropStage->cropCycle->activities->count() > 0)
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Activities in this Stage</h3>
            <div class="space-y-2">
                @foreach($cropStage->cropCycle->activities->where('crop_stage_id', $cropStage->id) as $activity)
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $activity->activity_name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $activity->activity_date?->format('M d, Y') }} • {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($activity->status === 'approved') bg-green-100 text-green-800
                            @elseif($activity->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($activity->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Delete Action -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-red-600 mb-2">Danger Zone</h3>
        <p class="text-gray-600 mb-4">Deleting this stage will not delete associated activities, but they may become orphaned.</p>
        <form action="{{ route('crop_stages.destroy', $cropStage) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this crop stage?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i> Delete Stage
            </button>
        </form>
    </div>
</div>
@endsection
