@extends('layouts.MainLayout')

@section('title', 'Livestock by Location - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock_tracking.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Livestock by Location</h1>
        </div>
    </div>

    <!-- Grouped by Field -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Grouped by Field</h2>
        </div>
        @if($byField->count() > 0)
        @foreach($byField as $fieldId => $fieldLivestock)
            @php $field = $fieldLivestock->first()->field @endphp
            <div class="border-b last:border-0">
                <div class="px-6 py-3 bg-blue-50">
                    <h3 class="font-semibold text-gray-800">
                        {{ $field->name ?? 'Unknown Field' }}
                        <span class="text-sm text-gray-500">({{ $fieldLivestock->count() }} animals)</span>
                    </h3>
                    @if($field)
                        <small class="text-gray-600">{{ $field->farm->name ?? 'No farm' }}</small>
                    @endif
                </div>
                <div class="px-6 py-2">
                    @foreach($fieldLivestock as $location)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <a href="{{ route('livestock.show', $location->livestock) }}" class="font-medium text-gray-900 hover:text-primary">
                                {{ $location->livestock->name ?? 'Unnamed' }}
                            </a>
                            <span class="text-sm text-gray-500"> - {{ $location->livestock->tag_number ?? 'No tag' }}</span>
                            <br><small class="text-gray-500">{{ $location->livestock->type->name ?? 'Unknown type' }}</small>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $location->entered_at->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @else
        <div class="p-8 text-center">
            <p class="text-gray-500">No livestock currently assigned to fields.</p>
        </div>
        @endif
    </div>

    <!-- Grouped by Farm -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Grouped by Farm (not in specific field)</h2>
        </div>
        @if($byFarm->count() > 0)
        @foreach($byFarm as $farmId => $farmLivestock)
            @php $farm = $farmLivestock->first()->farm @endphp
            <div class="border-b last:border-0">
                <div class="px-6 py-3 bg-green-50">
                    <h3 class="font-semibold text-gray-800">
                        {{ $farm->name ?? 'Unknown Farm' }}
                        <span class="text-sm text-gray-500">({{ $farmLivestock->count() }} animals)</span>
                    </h3>
                </div>
                <div class="px-6 py-2">
                    @foreach($farmLivestock as $location)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <a href="{{ route('livestock.show', $location->livestock) }}" class="font-medium text-gray-900 hover:text-primary">
                                {{ $location->livestock->name ?? 'Unnamed' }}
                            </a>
                            <span class="text-sm text-gray-500"> - {{ $location->livestock->tag_number ?? 'No tag' }}</span>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $location->entered_at->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @else
        <div class="p-8 text-center">
            <p class="text-gray-500">No livestock currently assigned to farms only.</p>
        </div>
        @endif
    </div>
</div>
@endsection
