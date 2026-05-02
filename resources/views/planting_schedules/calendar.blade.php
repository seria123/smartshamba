@extends('layouts.MainLayout')

@section('title', 'Crop Calendar - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Crop Calendar</h1>
        <a href="{{ route('planting-schedules.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-4 rounded-xl border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
            <i class="fas fa-plus text-lg"></i>
            <span>Add Schedule</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Calendar Navigation -->
    <x-ui.card>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <button onclick="window.location.href='{{ route('planting-schedules.index') }}'" class="flex items-center space-x-2 text-gray-600 hover:text-emerald-600 transition">
                    <i class="fas fa-chevron-left"></i>
                    <span>Back to List</span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">{{ $schedules->count() }} scheduled planting{{ $schedules->count() !== 1 ? 's' : '' }}</span>
            </div>
        </div>
    </x-ui.card>

    <!-- Monthly Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($monthlyStats as $month => $stats)
            <x-ui.card>
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</h3>
                <div class="mt-2 flex items-baseline space-x-2">
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['count'] }}</p>
                    <span class="text-sm text-gray-500">planting{{ $stats['count'] !== 1 ? 's' : '' }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['crops'] }} different crop{{ $stats['crops'] !== 1 ? 's' : '' }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <!-- Calendar Grid -->
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Planting Schedule Calendar</h3>
        
        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mb-6 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <span class="text-sm text-gray-600">Planned</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <span class="text-sm text-gray-600">Planted</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-sm text-gray-600">Growing</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                <span class="text-sm text-gray-600">Ready for Harvest</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                <span class="text-sm text-gray-600">Harvested</span>
            </div>
        </div>

        <!-- Group by month -->
        <div class="space-y-8">
            @foreach($schedules->groupBy(fn($s) => $s->planting_date->format('Y-m')) as $month => $monthSchedules)
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar mr-2 text-emerald-600"></i>
                        {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        <span class="ml-2 text-sm font-normal text-gray-500">{{ $monthSchedules->count() }} planting{{ $monthSchedules->count() !== 1 ? 's' : '' }}</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($monthSchedules as $schedule)
                            <div class="border-2 rounded-lg p-4 hover:shadow-md transition {{ 
                                $schedule->status === 'planned' ? 'border-yellow-200 bg-yellow-50' : 
                                ($schedule->status === 'planted' ? 'border-blue-200 bg-blue-50' : 
                                ($schedule->status === 'growing' ? 'border-green-200 bg-green-50' : 
                                ($schedule->status === 'ready_for_harvest' ? 'border-orange-200 bg-orange-50' : 'border-purple-200 bg-purple-50')))
                            }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-bold text-gray-900 text-lg">{{ $schedule->crop->name ?? 'N/A' }}</h5>
                                        <p class="text-sm text-gray-600">{{ $schedule->field->name ?? 'N/A' }}</p>
                                        @if($schedule->variety)
                                            <p class="text-xs text-gray-500 mt-1">Variety: {{ $schedule->variety }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ 
                                        $schedule->status === 'planned' ? 'bg-yellow-200 text-yellow-800' : 
                                        ($schedule->status === 'planted' ? 'bg-blue-200 text-blue-800' : 
                                        ($schedule->status === 'growing' ? 'bg-green-200 text-green-800' : 
                                        ($schedule->status === 'ready_for_harvest' ? 'bg-orange-200 text-orange-800' : 'bg-purple-200 text-purple-800')))
                                    }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Planted:</span>
                                        <span class="font-medium">{{ $schedule->planting_date->format('M d') }}</span>
                                    </div>
                                    @if($schedule->expected_harvest_date)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Expected Harvest:</span>
                                        <span class="font-medium">{{ $schedule->expected_harvest_date->format('M d') }}</span>
                                    </div>
                                    @endif
                                    @if($schedule->actual_harvest_date)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Actual Harvest:</span>
                                        <span class="font-medium">{{ $schedule->actual_harvest_date->format('M d') }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-3 pt-3 border-t {{ 
                                    $schedule->status === 'planned' ? 'border-yellow-200' : 
                                    ($schedule->status === 'planted' ? 'border-blue-200' : 
                                    ($schedule->status === 'growing' ? 'border-green-200' : 
                                    ($schedule->status === 'ready_for_harvest' ? 'border-orange-200' : 'border-purple-200')))
                                }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Progress</span>
                                        <span class="text-xs font-bold {{ 
                                            $schedule->status === 'planned' ? 'text-yellow-700' : 
                                            ($schedule->status === 'planted' ? 'text-blue-700' : 
                                            ($schedule->status === 'growing' ? 'text-green-700' : 
                                            ($schedule->status === 'ready_for_harvest' ? 'text-orange-700' : 'text-purple-700')))
                                        }}">{{ $schedule->completion_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="h-1.5 rounded-full {{ 
                                            $schedule->status === 'planned' ? 'bg-yellow-500' : 
                                            ($schedule->status === 'planted' ? 'bg-blue-500' : 
                                            ($schedule->status === 'growing' ? 'bg-green-500' : 
                                            ($schedule->status === 'ready_for_harvest' ? 'bg-orange-500' : 'bg-purple-500')))
                                        }}" style="width: {{ $schedule->completion_percentage }}%"></div>
                                    </div>
                                </div>

                                @if($schedule->notes)
                                    <div class="mt-3 text-xs text-gray-500 italic line-clamp-2">
                                        "{{ Str::limit($schedule->notes, 60) }}"
                                    </div>
                                @endif

                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('planting-schedules.show', $schedule->id) }}" class="flex-1 text-center py-2 text-sm font-medium rounded-lg bg-white bg-opacity-50 hover:bg-opacity-70 transition">
                                        View
                                    </a>
                                    <a href="{{ route('planting-schedules.edit', $schedule->id) }}" class="flex-1 text-center py-2 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @if($schedules->count() === 0)
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-500 mb-2">No planting schedules yet</h3>
                <p class="text-gray-400 mb-6">Get started by creating your first planting schedule</p>
                <a href="{{ route('planting-schedules.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-plus"></i>
                    <span>Create Schedule</span>
                </a>
            </div>
        @endif
    </x-ui.card>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-ui.card>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h4 class="font-semibold text-gray-800">New Schedule</h4>
                <p class="text-sm text-gray-500 mt-1">Plan new planting</p>
                <a href="{{ route('planting-schedules.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-2 inline-block">
                    Create →
                </a>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-list"></i>
                </div>
                <h4 class="font-semibold text-gray-800">Full List</h4>
                <p class="text-sm text-gray-500 mt-1">View all schedules</p>
                <a href="{{ route('planting-schedules.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium mt-2 inline-block">
                    View All →
                </a>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="font-semibold text-gray-800">Upcoming</h4>
                <p class="text-sm text-gray-500 mt-1">{{ $schedules->filter(fn($s) => $s->status === 'planned' || $s->status === 'planted')->count() }} pending</p>
                <span class="text-orange-600 text-sm font-medium mt-2 inline-block">Check schedules</span>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h4 class="font-semibold text-gray-800">Progress</h4>
                <p class="text-sm text-gray-500 mt-1">Track completion</p>
                <span class="text-purple-600 text-sm font-medium mt-2 inline-block">View progress</span>
            </div>
        </x-ui.card>
    </div>

</div>
@endsection
