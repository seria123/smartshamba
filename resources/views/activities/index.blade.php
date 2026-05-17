@extends('layouts.MainLayout')

@section('title', 'Activities - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">📋 Activities</h1>
        <a href="{{ route('activities.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> New Activity
        </a>
    </div>

    <!-- Activities List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($activities->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Activity Name</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Type</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Field</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Date</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Status</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($activities as $activity)
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6">
                        <p class="font-medium">{{ $activity->activity_name }}</p>
                        <p class="text-sm text-gray-500">{{ Str::limit($activity->description, 50) }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @php
                            $typeColors = [
                                'weeding' => 'success',
                                'fertilizer_application' => 'warning',
                                'spraying' => 'danger',
                                'irrigation' => 'info',
                                'pruning_training' => 'primary',
                                'scouting_inspection' => 'gray',
                                'thinning_gapping' => 'warning',
                                'soil_crop_nutrition' => 'success',
                                'harvesting' => 'success',
                            ];
                            $typeLabels = [
                                'weeding' => 'Weeding',
                                'fertilizer_application' => 'Fertilizer App',
                                'spraying' => 'Spraying',
                                'irrigation' => 'Irrigation',
                                'pruning_training' => 'Pruning/Training',
                                'scouting_inspection' => 'Scouting',
                                'thinning_gapping' => 'Thinning/Gapping',
                                'soil_crop_nutrition' => 'Soil Nutrition',
                                'harvesting' => 'Harvesting',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $typeColors[$activity->activity_type] ?? 'gray' }}-100 text-{{ $typeColors[$activity->activity_type] ?? 'gray' }}-800">
                            {{ $typeLabels[$activity->activity_type] ?? $activity->activity_type }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $activity->field->name ?? 'N/A' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $activity->activity_date?->format('M d, Y') }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @if($activity->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Approved
                            </span>
                        @elseif($activity->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <a href="{{ route('activities.show', $activity) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('activities.edit', $activity) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-6 text-center text-gray-500">
            <p>No activities found. Create your first activity!</p>
        </div>
        @endif
    </div>
</div>
@endsection
