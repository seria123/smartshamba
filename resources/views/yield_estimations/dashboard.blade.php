@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Yield Analysis Dashboard</h1>
                        <p class="text-gray-600 mt-1">Overview of yield estimations and production forecasts</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select class="form-select-modern" id="yearSelect" onchange="window.location.href='?year='+this.value">
                            @for($y = 2024; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <a href="{{ route('yield_estimations.create') }}" class="btn-primary flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            New Estimation
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Total Estimations</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_estimations'] }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Total Hectares</span>
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <i class="fas fa-tree text-green-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_planned_hectares'], 1) }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Total Est. Yield</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-weight-hanging text-purple-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['estimated_total_yield'], 0) }}</p>
                    <p class="text-xs text-gray-500">kg (estimated)</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">In Progress</span>
                        <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-spinner text-yellow-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_in_progress'] }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Harvested</span>
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_harvested'] }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm">Failed</span>
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['total_failed'] }}</p>
                </div>
            </div>

            @if($estimations->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Recent Estimations -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-history text-gray-600"></i>
                            Recent Estimations ({{ $year }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($estimations->take(5) as $est)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                        <i class="fas fa-crop text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $est->crop?->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $est->year }} • {{ ucwords(str_replace('_', ' ', $est->season)) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium">{{ number_format($est->estimated_yield, 1) }} kg</p>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $est->status == 'harvested' ? 'bg-green-100 text-green-700' : 
                                           ($est->status == 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $est->status)) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('yield_estimations.index') }}" 
                           class="mt-4 w-full inline-flex items-center justify-center gap-2 text-amber-600 hover:text-amber-700">
                            <i class="fas fa-arrow-right"></i>
                            View All Estimations
                        </a>
                    </div>
                </div>

                <!-- Monthly Distribution -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-gray-600"></i>
                            Monthly Distribution ({{ $year }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                            @php
                                $count = $monthlyEstimations->get($month, collect())->count();
                                $barWidth = $count > 0 ? min(($count / max($monthlyEstimations->max(function($m) { return $m->count(); }) ?: 1, 1)) * 100, 100) : 0;
                            @endphp
                            @if($count > 0)
                            <div class="flex items-center gap-3">
                                <span class="w-24 text-sm text-gray-600">{{ $month }}</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full transition-all duration-500" 
                                         style="width: {{ $barWidth }}%">
                                    </div>
                                </div>
                                <span class="w-8 text-sm font-medium text-gray-700">{{ $count }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Start Card if no estimations -->
            @if($estimations->count() == 0)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg border border-amber-200 p-8 text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-4xl text-amber-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Yield Estimations Yet</h2>
                <p class="text-gray-600 mb-6">Create your first yield estimation to start tracking production forecasts</p>
                <a href="{{ route('yield_estimations.create') }}" 
                   class="btn-primary inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Create First Estimation
                </a>
            </div>
            @endif

            <!-- Crop Comparison -->
            @if($estimations->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-gray-600"></i>
                        Crop Analysis ({{ $year }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($estimations->groupBy('crop_id') as $cropId => $cropEsts)
                        @php
                            $crop = $cropEsts->first()->crop;
                            $totalYield = $cropEsts->sum('estimated_yield');
                            $totalHectares = $cropEsts->sum('hectares');
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $crop?->name ?? 'Unknown' }}</h4>
                                <span class="text-sm font-medium text-gray-500">{{ $cropEsts->count() }} est.</span>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Estimated Yield:</span>
                                    <span class="font-medium">{{ number_format($totalYield, 0) }} kg</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Area:</span>
                                    <span class="font-medium">{{ number_format($totalHectares, 1) }} ha</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Avg Yield/Ha:</span>
                                    <span class="font-medium">{{ $totalHectares > 0 ? number_format($totalYield / $totalHectares, 0) : 0 }} kg</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
