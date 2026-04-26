@extends('layouts.MainLayout')

@section('title', 'Disease Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $livestockDisease->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('diseases.edit', $livestockDisease->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @if($livestockDisease->status === 'active')
                <form action="{{ route('diseases.treat', $livestockDisease->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>Mark as Treated
                    </button>
                </form>
            @endif
            <a href="{{ route('diseases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Disease Status -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Disease Status</h2>
            @switch($livestockDisease->status)
                @case('active')
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">Active</span>
                    @break
                @case('treated')
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm">Treated</span>
                    @break
                @case('chronic')
                    <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm">Chronic</span>
                    @break
            @endswitch
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Severity</p>
                <p class="text-lg font-medium text-gray-800">
                    @switch($livestockDisease->severity)
                        @case('high')
                            <span class="text-red-600">High</span>
                            @break
                        @case('medium')
                            <span class="text-yellow-600">Medium</span>
                            @break
                        @case('low')
                            <span class="text-blue-600">Low</span>
                            @break
                    @endswitch
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Diagnosed Date</p>
                <p class="text-lg font-medium text-gray-800">{{ $livestockDisease->diagnosed_date ? $livestockDisease->diagnosed_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            @if($livestockDisease->treated_date)
            <div>
                <p class="text-sm text-gray-500">Treated Date</p>
                <p class="text-lg font-medium text-gray-800">{{ $livestockDisease->treated_date->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Affected Livestock -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Affected Livestock</h2>
        @if($livestockDisease->livestock)
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-800">{{ $livestockDisease->livestock->name ?? 'Unnamed' }}</h3>
                <span class="text-sm text-gray-500">Tag: {{ $livestockDisease->livestock->tag_number ?? 'N/A' }}</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Type</p>
                    <p class="font-medium">{{ $livestockDisease->livestock->type->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Gender</p>
                    <p class="font-medium">{{ ucfirst($livestockDisease->livestock->gender ?? 'Unknown') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Age</p>
                    <p class="font-medium">{{ $livestockDisease->livestock->birth_date ? $livestockDisease->livestock->birth_date->age : 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p class="font-medium">
                        @switch($livestockDisease->livestock->status)
                            @case('healthy')
                                <span class="text-green-600">Healthy</span>
                                @break
                            @case('sick')
                                <span class="text-red-600">Sick</span>
                                @break
                            @case('sold')
                                <span class="text-blue-600">Sold</span>
                                @break
                            @case('dead')
                                <span class="text-gray-600">Dead</span>
                                @break
                        @endswitch
                    </p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('livestock.show', $livestockDisease->livestock->id) }}" class="text-primary hover:text-primary-dark font-medium">
                    View Livestock Details <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @else
            <p class="text-gray-500">Livestock information not available.</p>
        @endif
    </div>

    <!-- Disease Information -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Disease Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Species Affected</p>
                <p class="text-gray-800">{{ $livestockDisease->species ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cause</p>
                <p class="text-gray-800">{{ $livestockDisease->cause ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Mortality Rate</p>
                <p class="text-gray-800">{{ $livestockDisease->mortality_rate ? $livestockDisease->mortality_rate . '%' : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Transmission</p>
                <p class="text-gray-800">{{ $livestockDisease->transmission ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Symptoms & Treatment -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Symptoms</h3>
            <p class="text-gray-700">{{ $livestockDisease->symptoms ?? 'No symptoms recorded.' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Treatment</h3>
            <p class="text-gray-700">{{ $livestockDisease->treatment ?? 'No treatment recorded.' }}</p>
        </div>
    </div>

    <!-- Prevention -->
    @if($livestockDisease->prevention)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Prevention</h3>
        <p class="text-gray-700">{{ $livestockDisease->prevention }}</p>
    </div>
    @endif

    <!-- Treatment Information -->
    @if($livestockDisease->treatedBy)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Treatment Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Treated By</p>
                <p class="text-gray-800">{{ $livestockDisease->treatedBy->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Treated Date</p>
                <p class="text-gray-800">{{ $livestockDisease->treated_date ? $livestockDisease->treated_date->format('M d, Y H:i') : 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection