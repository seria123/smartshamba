@extends('layouts.MainLayout')

@section('title', 'Activity Details - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">📋 Activity Details</h1>
        <div class="flex space-x-4">
            <a href="{{ route('activities.edit', $activity) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('activities.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <!-- Activity Header -->
        <div class="border-b pb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $activity->activity_name }}</h2>
                    <p class="text-gray-500 mt-1">
                        <i class="fas fa-seedling mr-2"></i>{{ $activity->cropCycle->crop_name ?? $activity->cropCycle->crop->name ?? 'N/A' }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $activity->field->name ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pending' => 'yellow',
                            'approved' => 'green',
                            'rejected' => 'red',
                        ];
                        $statusLabels = [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColors[$activity->status] ?? 'gray' }}-100 text-{{ $statusColors[$activity->status] ?? 'gray' }}-800">
                        {{ $statusLabels[$activity->status] ?? $activity->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Activity Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Activity Information</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Type</dt>
                            <dd class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date</dt>
                            <dd class="text-gray-900">{{ $activity->activity_date?->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Quantity</dt>
                            <dd class="text-gray-900">{{ $activity->quantity ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Cost</dt>
                            <dd class="text-gray-900">{{ $activity->cost ? '$' . number_format($activity->cost, 2) : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Labor Type</dt>
                            <dd class="text-gray-900">{{ $activity->labor_type ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Personnel</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Lead Staff</dt>
                            <dd class="text-gray-900">{{ $activity->staff->full_name ?? $activity->staff->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Supervisor</dt>
                            <dd class="text-gray-900">{{ $activity->supervisor->full_name ?? $activity->supervisor->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Crop & Field Details</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Crop Cycle</dt>
                            <dd class="text-gray-900">{{ $activity->cropCycle->crop_name ?? $activity->cropCycle->crop->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Stage</dt>
                            <dd class="text-gray-900">{{ $activity->cropStage->stage_name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Field</dt>
                            <dd class="text-gray-900">{{ $activity->field->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Farm</dt>
                            <dd class="text-gray-900">{{ $activity->field->farm->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                @if($activity->description)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $activity->description }}</p>
                </div>
                @endif

                @if($activity->notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $activity->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Associated Data -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Associated Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Equipment -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Equipment Used</h4>
                    @if($activity->equipment->count() > 0)
                        <ul class="space-y-1">
                            @foreach($activity->equipment as $eq)
                                <li class="text-sm text-gray-600">
                                    <i class="fas fa-wrench mr-2"></i>{{ $eq->name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No equipment recorded</p>
                    @endif
                </div>

                <!-- Assigned Staff -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Assigned Staff</h4>
                    @if($activity->assignedStaff->count() > 0)
                        <ul class="space-y-1">
                            @foreach($activity->assignedStaff as $staff)
                                <li class="text-sm text-gray-600">
                                    <i class="fas fa-user mr-2"></i>{{ $staff->full_name ?? $staff->name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No additional staff assigned</p>
                    @endif
                </div>

                <!-- Images -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Photos</h4>
                    @if($activity->images->count() > 0)
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($activity->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Activity photo"
                                     class="w-full h-20 object-cover rounded cursor-pointer"
                                     onclick="window.open('{{ asset('storage/' . $image->image_path) }}', '_blank')">
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No photos uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Action -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-red-600 mb-2">Danger Zone</h3>
        <p class="text-gray-600 mb-4">Once you delete this activity, there is no going back. Please be certain.</p>
        <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i> Delete Activity
            </button>
        </form>
    </div>
</div>
@endsection
