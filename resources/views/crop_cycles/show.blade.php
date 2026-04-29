@extends('layouts.MainLayout')
@section('title', 'Crop Cycle Details - SmartShamba')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $cropCycle->crop->name }} Cycle
                        </h1>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('crop_cycles.edit', $cropCycle) }}" class="btn btn-outline btn-secondary">
                                Edit
                            </a>
                            <a href="{{ route('crop_cycles.index') }}" class="btn btn-outline">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Field Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Field Information</h3>
                            <p class="text-gray-600"><strong>Field:</strong> {{ $cropCycle->field->name }}</p>
                            <p class="text-gray-600"><strong>Farm:</strong> {{ $cropCycle->field->farm->name }}</p>
                            <p class="text-gray-600"><strong>Size:</strong> {{ $cropCycle->field->size_hectares }} hectares</p>
                        </div>

                        <!-- Crop Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Crop Information</h3>
                            <p class="text-gray-600"><strong>Crop:</strong> {{ $cropCycle->crop->name }}</p>
                            <p class="text-gray-600"><strong>Variety:</strong> {{ $cropCycle->crop->variety ?? 'Not specified' }}</p>
                            <p class="text-gray-600"><strong>Days to Maturity:</strong> {{ $cropCycle->crop->days_to_maturity ?? 'Not specified' }} days</p>
                        </div>

                        <!-- Cycle Dates -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Cycle Dates</h3>
                            <p class="text-gray-600"><strong>Start Date:</strong> {{ $cropCycle->start_date->format('M d, Y') }}</p>
                            <p class="text-gray-600"><strong>Expected Harvest:</strong> {{ $cropCycle->expected_harvest_date ? $cropCycle->expected_harvest_date->format('M d, Y') : 'Not set' }}</p>
                            <p class="text-gray-600"><strong>Days Since Planting:</strong> {{ $analysis['days_since_planting'] }}</p>
                        </div>
                    </div>

                    <!-- Analysis Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Crop Analysis</h3>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Current Stage -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Current Stage</p>
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ ucfirst($analysis['current_stage']) }}
                                    </p>
                                </div>

                                <!-- Progress -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Growth Progress</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" 
                                             style="width: {{ $analysis['progress_percentage'] }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $analysis['progress_percentage'] }}% complete
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Activities -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recommended Activities</h3>
                        @if (!empty($analysis['recommended_activities']))
                            <div class="space-y-3">
                                @foreach ($analysis['recommended_activities'] as $activity)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-3 w-3 bg-indigo-600 rounded-full mt-1 mr-3"></div>
                                        <div class="flex-1">
                                            <p class="text-gray-700">{{ $activity }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No specific recommendations available for this stage.</p>
                        @endif
                     </div>
                 </div>
             </div>
         </div>
     </div>
@endsection