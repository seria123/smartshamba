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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        @if($livestock->notes)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-gray-800">{{ $livestock->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Relationships -->
    @if($livestock->parent || $livestock->offspring->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Relationships</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($livestock->parent)
            <div>
                <p class="text-sm text-gray-500">Parent</p>
                <a href="{{ route('livestock.show', $livestock->parent->id) }}" class="text-primary hover:text-primary-dark">
                    {{ $livestock->parent->name ?? 'Unnamed' }} ({{ $livestock->parent->tag_number ?? 'No tag' }})
                </a>
            </div>
            @endif
            @if($livestock->offspring->count() > 0)
            <div>
                <p class="text-sm text-gray-500">Offspring ({{ $livestock->offspring->count() }})</p>
                <div class="space-y-1">
                    @foreach($livestock->offspring->take(3) as $offspring)
                    <a href="{{ route('livestock.show', $offspring->id) }}" class="block text-primary hover:text-primary-dark text-sm">
                        {{ $offspring->name ?? 'Unnamed' }} ({{ $offspring->tag_number ?? 'No tag' }})
                    </a>
                    @endforeach
                    @if($livestock->offspring->count() > 3)
                    <p class="text-sm text-gray-500">... and {{ $livestock->offspring->count() - 3 }} more</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Disease History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Disease History</h2>
        @if($livestock->diseases->count() > 0)
        <div class="space-y-3">
            @foreach($livestock->diseases as $disease)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-800">{{ $disease->name }}</h3>
                    <span class="text-sm text-gray-500">{{ $disease->diagnosed_date ? $disease->diagnosed_date->format('M d, Y') : 'Unknown date' }}</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-medium">
                            @switch($disease->status)
                                @case('active')
                                    <span class="text-red-600">Active</span>
                                    @break
                                @case('treated')
                                    <span class="text-green-600">Treated</span>
                                    @break
                                @case('chronic')
                                    <span class="text-orange-600">Chronic</span>
                                    @break
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Severity</p>
                        <p class="font-medium">{{ ucfirst($disease->severity) }}</p>
                    </div>
                    @if($disease->treated_date)
                    <div>
                        <p class="text-gray-500">Treated</p>
                        <p class="font-medium">{{ $disease->treated_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($disease->treatedBy)
                    <div>
                        <p class="text-gray-500">Treated By</p>
                        <p class="font-medium">{{ $disease->treatedBy->name }}</p>
                    </div>
                    @endif
                </div>
                @if($disease->symptoms)
                <div class="mt-3">
                    <p class="text-gray-500 text-sm">Symptoms</p>
                    <p class="text-sm">{{ Str::limit($disease->symptoms, 100) }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500">No disease history recorded.</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('diseases.diagnose') }}?livestock_id={{ $livestock->id }}" class="text-primary hover:text-primary-dark font-medium">
                <i class="fas fa-plus mr-2"></i>Diagnose New Disease
            </a>
        </div>
    </div>
</div>
@endsection