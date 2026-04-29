@extends('layouts.MainLayout')

@section('title', 'Location History - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock.show', $livestock) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Location History</h1>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $livestock->name ?? 'Unnamed' }}</span>
        </div>
        <a href="{{ route('livestock.locations.create', $livestock) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Record New Location
        </a>
    </div>

    <!-- Location History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Location Records</h2>
        </div>
        @if($locations->count() > 0)
        <div class="p-4">
            <div class="space-y-4">
                @foreach($locations as $location)
                <div class="border rounded-lg p-4 hover:shadow-md transition {{ $location->left_at ? 'bg-gray-50' : 'bg-blue-50 border-blue-200' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                @if($location->left_at)
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded">Inactive</span>
                                @else
                                    <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded">Active</span>
                                @endif
                                <span class="font-semibold text-gray-800">{{ ucfirst($location->location_type) }}</span>
                                <span class="text-gray-500">•</span>
                                <span class="text-gray-600">{{ ucfirst($location->movement_type) }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Field</p>
                                    <p>{{ $location->field->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Farm</p>
                                    <p>{{ $location->farm->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Entered</p>
                                    <p>{{ $location->entered_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Duration</p>
                                    <p>
                                        @if($location->left_at)
                                            {{ $location->entered_at->diffForHumans($location->left_at, true) }}
                                        @else
                                            {{ $location->entered_at->diffForHumans() }} (ongoing)
                                        @endif
                                    </p>
                                </div>
                                @if($location->gps_latitude && $location->gps_longitude)
                                <div>
                                    <p class="text-gray-500">GPS Coordinates</p>
                                    <p>{{ $location->gps_latitude }}, {{ $location->gps_longitude }}</p>
                                </div>
                                @endif
                            </div>

                            @if($location->notes)
                            <div class="mt-3 text-sm">
                                <p class="text-gray-500">Notes</p>
                                <p>{{ $location->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $locations->links() }}
            </div>
        </div>
        @else
        <div class="p-8 text-center">
            <i class="fas fa-map-marked-alt text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">No location records yet.</p>
            <a href="{{ route('livestock.locations.create', $livestock) }}" class="text-primary hover:underline mt-2 inline-block">Add first location</a>
        </div>
        @endif
    </div>
</div>
@endsection
