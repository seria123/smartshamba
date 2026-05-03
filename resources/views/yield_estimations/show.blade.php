@extends('layouts.MainLayout')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Yield Estimation Details</h1>
                    <p class="text-gray-600 mt-1">{{ $yieldEstimation->crop?->name ?? 'Crop' }} - {{ $yieldEstimation->year }} @if($yieldEstimation->season){{ ucwords(str_replace('_', ' ', $yieldEstimation->season)) }} @endif</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('yield_estimations.edit', $yieldEstimation) }}" 
                   class="btn-secondary flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('yield_estimations.index') }}" 
                   class="btn-secondary flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold 
                            {{ 
                                $yieldEstimation->status == 'planned' ? 'bg-blue-100 text-blue-800' :
                                ($yieldEstimation->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                                ($yieldEstimation->status == 'harvested' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
                            }}">
                            <i class="fas fa-circle-notch mr-1"></i>
                            {{ ucwords(str_replace('_', ' ', $yieldEstimation->status)) }}
                        </span>
                        @if($yieldEstimation->field)
                        <span class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $yieldEstimation->field->name }}
                            @if($yieldEstimation->field->location)
                            • {{ $yieldEstimation->field->location }}
                            @endif
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-weight-hanging text-amber-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Area</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($yieldEstimation->hectares, 2) }}</p>
            <p class="text-xs text-gray-500">hectares</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Est. Yield</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($yieldEstimation->estimated_yield, 2) }}</p>
            <p class="text-xs text-gray-500">{{ $yieldEstimation->yield_unit }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Yield/Hectare</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($yieldEstimation->yield_per_hectare, 2) }}</p>
            <p class="text-xs text-gray-500">{{ $yieldEstimation->yield_unit }}/ha</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-blue-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Est. Income</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                @if($yieldEstimation->estimated_income)
                    {{ number_format($yieldEstimation->estimated_income, 0) }}
                @else
                    ---
                @endif
            </p>
            <p class="text-xs text-gray-500">KES</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Details Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-600"></i>
                    Details
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Crop</span>
                        <span class="font-medium">{{ $yieldEstimation->crop?->name ?? 'N/A' }}</span>
                    </div>
                    @if($yieldEstimation->field)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Field</span>
                        <span class="font-medium">{{ $yieldEstimation->field->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Season</span>
                        <span class="font-medium">{{ ucwords(str_replace('_', ' ', $yieldEstimation->season)) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Year</span>
                        <span class="font-medium">{{ $yieldEstimation->year }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Status</span>
                        <span class="font-medium px-3 py-1 rounded-full text-sm
                            {{ 
                                $yieldEstimation->status == 'planned' ? 'bg-blue-100 text-blue-800' :
                                ($yieldEstimation->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                                ($yieldEstimation->status == 'harvested' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
                            }}">
                            {{ ucwords(str_replace('_', ' ', $yieldEstimation->status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Created</span>
                        <span class="font-medium">{{ $yieldEstimation->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($yieldEstimation->updated_at->gt($yieldEstimation->created_at))
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Last Updated</span>
                        <span class="font-medium">{{ $yieldEstimation->updated_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actual Results Card (Show if harvested) -->
        @if($yieldEstimation->status == 'harvested' || $yieldEstimation->actual_yield)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-600"></i>
                    Actual Results
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if($yieldEstimation->actual_yield)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Actual Yield</span>
                        <span class="font-medium text-lg">{{ number_format($yieldEstimation->actual_yield, 2) }} {{ $yieldEstimation->yield_unit }}</span>
                    </div>
                    @endif
                    
                    @if($yieldEstimation->actual_yield && $yieldEstimation->estimated_yield)
                    @php
                        $diff = $yieldEstimation->actual_yield - $yieldEstimation->estimated_yield;
                        $diffPercent = $yieldEstimation->estimated_yield > 0 ? ($diff / $yieldEstimation->estimated_yield) * 100 : 0;
                    @endphp
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Yield Difference</span>
                        <span class="font-medium text-lg {{ $diff >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }} ({{ number_format($diffPercent, 1) }}%)
                        </span>
                    </div>
                    @endif

                    @if($yieldEstimation->actual_income)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Actual Income</span>
                        <span class="font-medium text-lg">KES {{ number_format($yieldEstimation->actual_income, 0) }}</span>
                    </div>
                    @endif

                    @if($yieldEstimation->actual_income && $yieldEstimation->estimated_income)
                    @php
                        $incomeDiff = $yieldEstimation->actual_income - $yieldEstimation->estimated_income;
                    @endphp
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Income Difference</span>
                        <span class="font-medium text-lg {{ $incomeDiff >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $incomeDiff >= 0 ? '+' : '' }}KES {{ number_format($incomeDiff, 0) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Notes Card -->
        @if($yieldEstimation->notes)
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-gray-600"></i>
                    Notes
                </h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $yieldEstimation->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
