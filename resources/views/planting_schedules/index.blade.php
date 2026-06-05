@extends('layouts.MainLayout')

@section('title', 'Planting Schedules - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Planting Schedules</h1>
        <a href="{{ route('planting_schedules.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-4 rounded-xl border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
            <i class="fas fa-plus text-lg"></i>
            <span>Add Schedule</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Total Schedules</h3>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $schedules->total() }}</p>
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Upcoming</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $upcoming->count() }}</p>
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Active</h3>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $active->count() }}</p>
        </x-ui.card>
        <x-ui.card>
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Harvested</h3>
            <p class="text-3xl font-bold text-purple-600 mt-2">
                {{ $active->filter(fn($s) => $s->status === 'harvested')->count() }}
            </p>
        </x-ui.card>
    </div>

     <!-- Calendar Quick Link -->
     <x-ui.card>
         <div class="flex items-center justify-between">
             <div>
                 <h3 class="font-bold text-lg text-gray-800">View Calendar</h3>
                 <p class="text-sm text-gray-500 mt-1">Visualize planting schedules across the year</p>
             </div>
             <a href="{{ route('planting_schedules.calendar') }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                 <i class="fas fa-calendar-alt"></i>
                 <span>Open Calendar</span>
             </a>
         </div>
     </x-ui.card>

    <!-- Upcoming Plantings -->
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Upcoming Plantings</h3>
        @if($upcoming->count() > 0)
            <div class="space-y-3">
                @foreach($upcoming as $schedule)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ 
                                $schedule->status === 'planned' ? 'bg-yellow-100 text-yellow-600' : 
                                ($schedule->status === 'planted' ? 'bg-blue-100 text-blue-600' : 
                                ($schedule->status === 'growing' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600'))
                            }}">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $schedule->crop->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">{{ $schedule->field->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ $schedule->planting_date->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($schedule->status) }}</p>
                        </div>
                         <div class="flex items-center space-x-2">
                             <a href="{{ route('planting_schedules.edit', $schedule->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                 Edit
                             </a>
                             <form action="{{ route('planting_schedules.destroy', $schedule->id) }}" method="POST" class="inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium" onclick="return confirm('Delete this schedule?')">
                                     Delete
                                 </button>
                             </form>
                         </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No upcoming plantings scheduled</p>
        @endif
    </x-ui.card>

    <!-- Active Plantings -->
    @if($active->count() > 0)
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Active Plantings</h3>
        <div class="space-y-3">
            @foreach($active as $schedule)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border-l-4 {{ 
                    $schedule->status === 'planted' ? 'border-blue-500' : 
                    ($schedule->status === 'growing' ? 'border-green-500' : 'border-orange-500')
                }}">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-emerald-100 text-emerald-600">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $schedule->crop->name ?? 'N/A' }}</h4>
                            <p class="text-sm text-gray-500">{{ $schedule->field->name ?? 'N/A' }} - {{ ucfirst($schedule->status) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $schedule->planting_date->format('M d, Y') }}</p>
                        <div class="w-32 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $schedule->completion_percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $schedule->completion_percentage }}% complete</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    <!-- All Schedules Table -->
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">All Schedules</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Crop</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Field</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Farm</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Planting Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harvest Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $schedule->crop->name ?? 'N/A' }}</div>
                                @if($schedule->variety)
                                    <div class="text-sm text-gray-500">{{ Str::limit($schedule->variety, 20) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->field->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->farm->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->planting_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->expected_harvest_date ? $schedule->expected_harvest_date->format('M d, Y') : 'TBD' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                    $schedule->status === 'planned' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($schedule->status === 'planted' ? 'bg-blue-100 text-blue-800' : 
                                    ($schedule->status === 'growing' ? 'bg-green-100 text-green-800' : 
                                    ($schedule->status === 'ready_for_harvest' ? 'bg-orange-100 text-orange-800' : 
                                    ($schedule->status === 'harvested' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $schedule->completion_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $schedule->completion_percentage }}%</span>
                                </div>
                            </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                 <div class="flex items-center gap-2">
                                     <a href="{{ route('planting_schedules.show', $schedule->id) }}" class="text-emerald-600 hover:text-emerald-700" title="View">
                                         <i class="fas fa-eye"></i>
                                     </a>
                                     <a href="{{ route('planting_schedules.edit', $schedule->id) }}" class="text-blue-600 hover:text-blue-700" title="Edit">
                                         <i class="fas fa-edit"></i>
                                     </a>
                                     <form action="{{ route('planting_schedules.destroy', $schedule->id) }}" method="POST" class="inline">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="text-red-600 hover:text-red-700" title="Delete" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                             <i class="fas fa-trash"></i>
                                         </button>
                                     </form>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">No planting schedules found. Create one to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $schedules->links() }}
        </div>
    </x-ui.card>

</div>
@endsection
