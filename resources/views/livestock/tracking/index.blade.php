@extends('layouts.MainLayout')

@section('title', 'Livestock Tracking - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Livestock Tracking</h1>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('livestock_tracking.recent-movements') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-route mr-2"></i>All Movements
            </a>
            <a href="{{ route('livestock_tracking.by-location') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-globe mr-2"></i>By Location
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Tracked</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalTracked }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-cow"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">With Current Location</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $withCurrentLocation }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Transit</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $inTransit }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('livestock_tracking.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Tag number or name...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                <select name="farm_id" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">All Farms</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Livestock Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Livestock Locations</h2>
            <span class="text-sm text-gray-500">{{ $livestock->total() }} animals</span>
        </div>
        @if($livestock->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                     <tr>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Animal</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking ID</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Location</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Since</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                     </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                     @foreach($livestock as $animal)
                     <tr class="hover:bg-gray-50">
                         <td class="px-6 py-4 whitespace-nowrap">
                             <div>
                                 <p class="font-medium text-gray-900">{{ $animal->name ?? 'Unnamed' }}</p>
                                 <p class="text-sm text-gray-500">Tag: {{ $animal->tag_number ?? 'N/A' }}</p>
                             </div>
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                             {{ $animal->tracking_id ?? 'N/A' }}
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                             {{ $animal->type->name ?? 'Unknown' }}
                         </td>
                         <td class="px-6 py-4 text-sm text-gray-900">
                             @if($animal->currentLocation)
                                 <span class="font-medium">{{ $animal->currentLocation->field->name ?? $animal->currentLocation->farm->name ?? 'Unknown' }}</span>
                                 <br><small class="text-gray-500">{{ ucfirst($animal->currentLocation->location_type) }}</small>
                             @else
                                 <span class="text-gray-400">Not tracked</span>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                             @if($animal->currentLocation)
                                 {{ $animal->currentLocation->entered_at->diffForHumans() }}
                             @else
                                 -
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap">
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                 {{ $animal->status == 'healthy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                 {{ ucfirst($animal->status) }}
                             </span>
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                             <div class="flex space-x-2">
                                 <a href="{{ route('livestock.show', $animal) }}" class="text-primary hover:text-primary-dark" title="View">
                                     <i class="fas fa-eye"></i>
                                 </a>
                                 <a href="{{ route('livestock.locations.create', $animal) }}" class="text-green-600 hover:text-green-800" title="Record Location">
                                     <i class="fas fa-map-pin"></i>
                                 </a>
                                 <a href="{{ route('livestock.locations.index', $animal) }}" class="text-blue-600 hover:text-blue-800" title="History">
                                     <i class="fas fa-history"></i>
                                 </a>
                             </div>
                         </td>
                     </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $livestock->links() }}
        </div>
        @else
        <div class="p-8 text-center">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">No livestock found matching your criteria.</p>
        </div>
        @endif
    </div>
</div>
@endsection
