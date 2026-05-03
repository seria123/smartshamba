@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Yield Statistics</h1>
                        <p class="text-gray-600 mt-1">Comprehensive analysis of yield estimations and trends</p>
                    </div>
                    <a href="{{ route('yield_estimations.dashboard') }}" 
                       class="btn-secondary flex items-center gap-2">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-input-modern" 
                               value="{{ request('start_date', $startDate) }}">
                    </div>
                    <div>
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-input-modern" 
                               value="{{ request('end_date', $endDate) }}">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary flex items-center gap-2">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Total Estimations</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_estimations'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Total Hectares</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_hectares'], 1) }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Est. Total Yield</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['estimated_total_yield'], 0) }}</div>
                    <div class="text-xs text-gray-500">kg</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Act. Total Yield</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['actual_total_yield'], 0) }}</div>
                    <div class="text-xs text-gray-500">kg</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Est. Income</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_income'], 0) }}</div>
                    <div class="text-xs text-gray-500">KES</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="text-sm text-gray-500 mb-1">Act. Income</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_actual_income'], 0) }}</div>
                    <div class="text-xs text-gray-500">KES</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- By Status -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-gray-600"></i>
                            Distribution by Status
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($byStatus->count() > 0)
                            @foreach($byStatus as $item)
                            @php
                                $statusColors = [
                                    'planned' => ['bg-blue-100', 'text-blue-700', 'bar-blue'],
                                    'in_progress' => ['bg-yellow-100', 'text-yellow-700', 'bar-yellow'],
                                    'harvested' => ['bg-green-100', 'text-green-700', 'bar-green'],
                                    'failed' => ['bg-red-100', 'text-red-700', 'bar-red'],
                                ];
                                $colors = $statusColors[$item['name']] ?? $statusColors['planned'];
                            @endphp
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full {{ $colors[0] }}"></div>
                                    <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900">{{ $item['count'] }}</div>
                                    <div class="text-sm text-gray-500">{{ number_format($item['hectares'], 1) }} ha</div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-center text-gray-500 py-4">No data available</p>
                        @endif
                    </div>
                </div>

                <!-- By Crop -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-gray-600"></i>
                            Top Crops by Yield
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($byCrop->count() > 0)
                            @foreach($byCrop->take(5) as $item)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900">{{ number_format($item['yield'], 0) }}</div>
                                    <div class="text-sm text-gray-500">{{ $item['count'] }} est.</div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-center text-gray-500 py-4">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-6">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-gray-600"></i>
                        Monthly Trend
                    </h2>
                </div>
                <div class="p-6">
                    @if($byMonth->count() > 0)
                        <div class="space-y-4">
                            @foreach($byMonth as $item)
                            <div class="flex items-center gap-4">
                                <div class="w-32 text-sm font-medium text-gray-600">{{ $item['month'] }}</div>
                                <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full flex items-center px-3 transition-all duration-500" 
                                         style="width: {{ max(($item['yield'] / max($byMonth->max('yield') ?: 1, 1)) * 100, 5) }}%">
                                        <span class="text-white text-sm font-medium">
                                            {{ number_format($item['yield'], 0) }} kg
                                        </span>
                                    </div>
                                </div>
                                <div class="w-20 text-sm text-gray-600 text-right">{{ $item['count'] }} est.</div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
