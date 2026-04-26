@extends('layouts.MainLayout')

@section('title', 'Dashboard - SmartShamba')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <a href="{{ route('farms.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add Farm
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Farms Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Farms</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalFarms }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-tractor"></i>
                </div>
            </div>
        </div>

        <!-- Fields Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Fields</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalFields }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-seedling"></i>
                </div>
            </div>
        </div>

        <!-- Sensors Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Sensors</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeSensors }} / {{ $totalSensors }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-satellite-dish"></i>
                </div>
            </div>
        </div>

        <!-- Alerts Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Unread Alerts</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $unreadAlertsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Alerts -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Recent Alerts</h2>
                <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="p-4">
                @forelse($recentAlerts as $alert)
                    <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0">
                        <div class="mt-1">
                            @if($alert->severity == 'high')
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            @elseif($alert->severity == 'medium')
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $alert->message }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No recent alerts</p>
                @endforelse
            </div>
        </div>

        <!-- Sensor Status -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Sensor Status</h2>
                <a href="{{ route('sensors.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="p-4">
                @forelse($sensors as $sensor)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $sensor->name }}</p>
                            <p class="text-xs text-gray-500">{{ $sensor->field->name ?? 'N/A' }} • {{ $sensor->type }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $sensor->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($sensor->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No sensors found</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Farms Overview -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Your Farms</h2>
            <a href="{{ route('farms.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($farms as $farm)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <h3 class="font-semibold text-gray-800">{{ $farm->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $farm->location }}</p>
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-500">Size:</span>
                            <span class="font-medium">{{ $farm->size_hectares }} ha</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Fields:</span>
                            <span class="font-medium">{{ $farm->fields_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 col-span-3">No farms yet. <a href="{{ route('farms.create') }}" class="text-blue-600 hover:underline">Create your first farm</a>.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
