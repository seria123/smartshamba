@extends('layouts.MainLayout')

@section('title', 'Livestock Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $livestock->name ?? 'Unnamed Livestock' }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('livestock.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Add Another
            </a>
            <a href="{{ route('livestock.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Livestock Status -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Livestock Status</h2>
            @switch($livestock->status)
                @case('healthy')
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm">Healthy</span>
                    @break
                @case('sick')
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">Sick</span>
                    @break
                @case('sold')
                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">Sold</span>
                    @break
                @case('dead')
                    <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-sm">Dead</span>
                    @break
            @endswitch
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="text-lg font-medium text-gray-800">{{ $livestock->type->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tag Number</p>
                <p class="text-lg font-medium text-gray-800">{{ $livestock->tag_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Weight</p>
                <p class="text-lg font-medium text-gray-800">{{ $livestock->weight ? $livestock->weight . ' kg' : 'N/A' }}</p>
            </div>
        </div>
    </div>

     <!-- Basic Information -->
     <div class="bg-white rounded-lg shadow-md p-6">
         <h2 class="text-xl font-bold text-gray-800 mb-4">Basic Information</h2>
         <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             <div>
                 <p class="text-sm text-gray-500">Name</p>
                 <p class="text-gray-800">{{ $livestock->name ?? 'Unnamed' }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Gender</p>
                 <p class="text-gray-800">{{ ucfirst($livestock->gender ?? 'Unknown') }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Date Acquired</p>
                 <p class="text-gray-800">{{ $livestock->date_acquired ? $livestock->date_acquired->format('M d, Y') : 'N/A' }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Birth Date</p>
                 <p class="text-gray-800">{{ $livestock->birth_date ? $livestock->birth_date->format('M d, Y') : 'N/A' }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Tracking ID</p>
                 <p class="text-lg font-medium text-gray-800">{{ $livestock->tracking_id ?? 'N/A' }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Tag Number</p>
                 <p class="text-lg font-medium text-gray-800">{{ $livestock->tag_number ?? 'N/A' }}</p>
             </div>
             <div>
                 <p class="text-sm text-gray-500">Weight</p>
                 <p class="text-lg font-medium text-gray-800">{{ $livestock->weight ? $livestock->weight . ' kg' : 'N/A' }}</p>
             </div>
             @if($livestock->purchase_price)
             <div>
                 <p class="text-sm text-gray-500">Purchase Price</p>
                 <p class="text-gray-800">${{ number_format($livestock->purchase_price, 2) }}</p>
             </div>
             @endif
             @if($livestock->sale_price)
             <div>
                 <p class="text-sm text-gray-500">Sale Price</p>
                 <p class="text-gray-800">${{ number_format($livestock->sale_price, 2) }}</p>
             </div>
         @endif
     </div>

    <!-- Tracking Information -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Location & Movement Tracking</h2>

        @if($livestock->currentLocation)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-2">Current Location</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Field</p>
                    <p class="font-medium">{{ $livestock->currentLocation->field->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Farm</p>
                    <p class="font-medium">{{ $livestock->currentLocation->farm->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Since</p>
                    <p class="font-medium">{{ $livestock->currentLocation->entered_at->diffForHumans() }}</p>
                </div>
            </div>
            @if($livestock->currentLocation->gps_latitude && $livestock->currentLocation->gps_longitude)
            <div class="mt-2 text-sm">
                <p class="text-gray-500">GPS: {{ $livestock->currentLocation->gps_latitude }}, {{ $livestock->currentLocation->gps_longitude }}</p>
            </div>
            @endif
        </div>
        @else
        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-gray-500">No current location recorded. <a href="{{ route('livestock.locations.create', $livestock) }}" class="text-primary hover:underline">Add location</a></p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('livestock.locations.index', $livestock) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Location History</h3>
                        <p class="text-sm text-gray-500">{{ $livestock->locations->count() }} records</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('livestock.movements.index', $livestock) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-route"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Movement History</h3>
                        <p class="text-sm text-gray-500">{{ $livestock->movements->count() }} movements</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('livestock.grazing.patterns', $livestock) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-chart-area"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Grazing Patterns</h3>
                        <p class="text-sm text-gray-500">Analytics & insights</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-4">
            <a href="{{ route('livestock.locations.create', $livestock) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Record Location/Movement
            </a>
        </div>
    </div>
</div>
@endsection