@extends('layouts.MainLayout')

@section('title', 'Crop Cycle Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🌾 {{ $cropCycle->crop->name ?? $cropCycle->crop_name }}</h1>
            <p class="text-gray-500">Planted: {{ $cropCycle->start_date->format('M d, Y') }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('crop_cycles.edit', $cropCycle) }}" class="px-4 py-2 border border-yellow-500 text-yellow-600 rounded-lg hover:bg-yellow-50 transition">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('crop_cycles.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="bg-white rounded-lg shadow-md p-4 flex items-center justify-between">
        <div class="flex items-center">
            @if($cropCycle->expected_harvest_date)
                @if($cropCycle->expected_harvest_date->isPast())
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>Harvest Overdue
                    </span>
                @else
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i>Expected: {{ $cropCycle->expected_harvest_date->format('M d, Y') }}
                    </span>
                @endif
            @endif
        </div>
        <span class="text-sm text-gray-500">Stage: {{ ucfirst($cropCycle->current_stage) }}</span>
    </div>

    <!-- Basic Crop Identity -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">1</span>
            Basic Crop Identity
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">Crop</p>
                <p class="font-medium">{{ $cropCycle->crop->name ?? $cropCycle->crop_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Category</p>
                <p class="font-medium">{{ $cropCycle->category ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Variety</p>
                <p class="font-medium">{{ $cropCycle->variety ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Season</p>
                <p class="font-medium">{{ $cropCycle->season ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Land & Soil -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">2</span>
            Land & Soil Requirements
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">Field</p>
                <p class="font-medium">{{ $cropCycle->field->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Soil Type</p>
                <p class="font-medium">{{ $cropCycle->soil_type_override ?? $cropCycle->field->soil_type ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Soil pH</p>
                <p class="font-medium">{{ $cropCycle->ph_level ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Farm</p>
                <p class="font-medium">{{ $cropCycle->farm->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Water & Irrigation -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">3</span>
            Water & Irrigation
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Irrigation Type</p>
                <p class="font-medium">{{ $cropCycle->irrigation_type ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Water Source</p>
                <p class="font-medium">{{ $cropCycle->water_source_override ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Drainage</p>
                <p class="font-medium">{{ $cropCycle->drainage ?? 'N/A' }}</p>
            </div>
        </div>
        @if($cropCycle->irrigation_schedule)
        <div class="mt-4">
            <p class="text-sm text-gray-500">Watering Schedule</p>
            <p class="font-medium">{{ $cropCycle->irrigation_schedule }}</p>
        </div>
        @endif
    </div>

    <!-- Inputs -->
    @if($cropCycle->inputs->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">4</span>
            Inputs
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 text-sm text-gray-500">Type</th>
                        <th class="text-left py-3 text-sm text-gray-500">Name</th>
                        <th class="text-left py-3 text-sm text-gray-500">Quantity</th>
                        <th class="text-left py-3 text-sm text-gray-500">Date</th>
                        <th class="text-left py-3 text-sm text-gray-500">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cropCycle->inputs as $input)
                    <tr class="border-b">
                        <td class="py-3">{{ ucfirst($input->input_type) }}</td>
                        <td class="py-3">{{ $input->name }}</td>
                        <td class="py-3">{{ $input->quantity }} {{ $input->unit ?? '' }}</td>
                        <td class="py-3">{{ $input->application_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="py-3">KES {{ number_format($input->cost ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Disease Analyses -->
    @if($cropCycle->analyses->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">5</span>
                Disease Analyses
            </h2>
            <a href="{{ route('crop_analyses.create', ['crop_cycle_id' => $cropCycle->id]) }}" 
               class="btn btn-primary btn-sm">
                <i class="fas fa-camera"></i> New Analysis
            </a>
        </div>
        
        <div class="row g-3">
            @foreach($cropCycle->analyses->sortBy('created_at')->reverse()->take(5) as $analysis)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-{{ $analysis->severity_color ?? 'secondary' }}">
                    <img src="{{ Storage::url($analysis->image_path) }}" 
                         class="card-img-top" 
                         style="height: 150px; object-fit: cover;"
                         data-bs-toggle="modal"
                         data-bs-target="#analysisModal{{ $analysis->id }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">{{ $analysis->diagnosis ?? 'Unknown' }}</h6>
                            @if($analysis->severity)
                                <span class="badge bg-{{ $analysis->severity_color }}">
                                    {{ ucfirst($analysis->severity) }}
                                </span>
                            @endif
                        </div>
                        <small class="text-muted d-block mb-2">
                            {{ $analysis->created_at->format('M d, Y') }} • 
                            Confidence: {{ number_format($analysis->confidence_score ?? 0, 1) }}%
                        </small>
                        @if($analysis->recommendation)
                            <p class="small mb-0">{{ Str::limit($analysis->recommendation, 100) }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('crop_analyses.show', $analysis) }}" class="btn btn-sm btn-outline-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($cropCycle->analyses->count() > 5)
            <div class="text-center mt-3">
                <a href="{{ route('crop_analyses.index', ['crop_cycle_id' => $cropCycle->id]) }}" 
                   class="btn btn-outline-secondary">
                    View All {{ $cropCycle->analyses->count() }} Analyses
                </a>
            </div>
        @endif
    </div>
    @endif

    <!-- Growth Tracking -->
    @if($cropCycle->stages->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">{{ $cropCycle->analyses->count() > 0 ? '6' : '5' }}</span>
            Growth Tracking
        </h2>
        <div class="space-y-4">
            @foreach($cropCycle->stages as $stage)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-800">{{ $stage->stage_name }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full {{ $stage->health_status == 'excellent' ? 'bg-green-100 text-green-700' : ($stage->health_status == 'good' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($stage->health_status ?? 'unknown') }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 text-sm text-gray-600">
                    <div>Period: {{ $stage->start_date->format('M d') }} - {{ $stage->end_date?->format('M d') ?? 'Present' }}</div>
                    <div>Germination Rate: {{ $stage->germination_rate ?? 'N/A' }}%</div>
                    <div>Health: {{ ucfirst($stage->health_status) ?? 'N/A' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Harvests -->
    @if($cropCycle->harvests->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">{{ $cropCycle->analyses->count() > 0 ? '7' : '6' }}</span>
            Harvests
        </h2>
        <div class="space-y-4">
            @foreach($cropCycle->harvests as $harvest)
            <div class="border rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium">{{ $harvest->harvest_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Quantity</p>
                        <p class="font-medium">{{ $harvest->quantity_harvested }} {{ $harvest->unit }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Grade</p>
                        <p class="font-medium">{{ $harvest->getQualityLabelAttribute() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Net Quantity</p>
                        <p class="font-medium">{{ $harvest->getNetQuantity() }} {{ $harvest->unit }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Revenues -->
    @if($cropCycle->revenues->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">{{ $cropCycle->analyses->count() > 0 ? ($cropCycle->harvests->count() > 0 ? '8' : '7') : ($cropCycle->harvests->count() > 0 ? '7' : '6') }}</span>
            Sales & Profitability
        </h2>
        <div class="space-y-4">
            @foreach($cropCycle->revenues as $revenue)
            <div class="border rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Sale Date</p>
                        <p class="font-medium">{{ $revenue->sale_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="font-medium">KES {{ number_format($revenue->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Quantity Sold</p>
                        <p class="font-medium">{{ $revenue->quantity_sold }} {{ $revenue->unit }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Status</p>
                        <span class="px-2 py-1 text-xs rounded-full {{ $revenue->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($revenue->payment_status == 'partial' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($revenue->payment_status) }}
                        </span>
                    </div>
                </div>
                @if($revenue->price_per_unit)
                <div class="mt-3 pt-3 border-t text-sm">
                    <p>Price per unit: KES {{ number_format($revenue->price_per_unit, 2) }}</p>
                    @if($analysis['profit'] ?? false)
                    <p class="font-medium text-green-600">Net Profit: KES {{ number_format($analysis['profit'], 2) }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Smart Alerts & Recommendations -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-blue-100 text-blue-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">{{ $cropCycle->analyses->count() > 0 ? ($cropCycle->harvests->count() > 0 ? ($cropCycle->revenues->count() > 0 ? '9' : '8') : ($cropCycle->revenues->count() > 0 ? '8' : '7')) : ($cropCycle->harvests->count() > 0 ? ($cropCycle->revenues->count() > 0 ? '8' : '7') : ($cropCycle->revenues->count() > 0 ? '7' : '6')) }}</span>
            Smart Farming Alerts & Recommendations
        </h2>
        
        <!-- Alerts -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-bell text-red-500 mr-2"></i>
                Active Alerts
            </h3>
            <div class="space-y-3">
                @php
                    // Time to apply fertilizer alert
                    $needsFertilizer = false;
                    $fertilizerStage = '';
                    if (in_array($cropCycle->current_stage, ['vegetative', 'flowering', 'fruiting']) && 
                        $cropCycle->inputs()->where('input_type', 'fertilizer')->count() == 0) {
                        $needsFertilizer = true;
                        $fertilizerStage = ucfirst($cropCycle->current_stage);
                    }
                    
                    // Rain expected alert
                    $rainExpected = false;
                    $tomorrowWeather = null;
                    if ($cropCycle->field && $cropCycle->field->rainfall_zone) {
                        $recentWeather = $cropCycle->weather()->whereDate('recorded_at', '>=', \Carbon\Carbon::now()->subDays(7))->orderBy('recorded_at', 'desc')->first();
                        if ($recentWeather && $recentWeather->precipitation > 0) {
                            $rainExpected = true;
                            $tomorrowWeather = $recentWeather;
                        }
                    }
                @endphp
                
                @if($needsFertilizer)
                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-leaf text-yellow-600 mt-1"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-yellow-800">Time to Apply Fertilizer</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Your crop is in the <strong>{{ $fertilizerStage }}</strong> stage, which typically requires additional nutrients. 
                                Consider applying fertilizer to support optimal growth.
                            </p>
                            @if($cropCycle->crop->category)
                            <p class="text-xs text-yellow-600 mt-1">
                                Tip: {{ $cropCycle->crop->category == 'fruit' ? 'Use potassium-rich fertilizer for fruit development.' : ($cropCycle->crop->category == 'leafy' ? 'Apply nitrogen-based fertilizer for leaf growth.' : 'Balanced NPK fertilizer recommended.') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                @if($rainExpected)
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-cloud-rain text-blue-600 mt-1"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-blue-800">Rain Expected</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Rain is expected based on recent weather patterns ({{ $tomorrowWeather->precipitation }}mm precipitation recorded).
                                {{ $tomorrowWeather->precipitation > 10 ? 'Significant rainfall expected - irrigation may not be needed.' : 'Light rain expected - adjust irrigation accordingly.' }}
                            </p>
                            @if($cropCycle->irrigation_type)
                            <p class="text-xs text-blue-600 mt-1">
                                Consider postponing scheduled irrigation to conserve water.
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                @if(!$needsFertilizer && !$rainExpected)
                <div class="border-l-4 border-green-400 bg-green-50 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-green-800">All Clear</h4>
                            <p class="text-sm text-green-700 mt-1">
                                No critical alerts at this time. Continue regular monitoring and maintenance.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Weather-based alerts -->
                @if($cropCycle->weather()->exists())
                    @php
                        $latestWeather = $cropCycle->weather()->latest()->first();
                    @endphp
                    @if($latestWeather->temperature > 35)
                    <div class="border-l-4 border-red-400 bg-red-50 p-4 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-temperature-high text-red-600 mt-1"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-red-800">High Temperature Alert</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Current temperature is {{ $latestWeather->temperature }}°C. 
                                    Ensure adequate irrigation and consider shade protection during peak hours.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($latestWeather->humidity < 30)
                    <div class="border-l-4 border-orange-400 bg-orange-50 p-4 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-tint-slash text-orange-600 mt-1"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-orange-800">Low Humidity Alert</h4>
                                <p class="text-sm text-orange-700 mt-1">
                                    Humidity is at {{ $latestWeather->humidity }}%. Consider increasing irrigation frequency.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        
        <!-- Recommendations -->
        <div>
            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-amber-500 mr-2"></i>
                Smart Recommendations
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Planting Time Recommendation -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="bg-emerald-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-calendar-alt text-emerald-600"></i>
                        </div>
                        <h4 class="font-medium text-emerald-800">Best Planting Time</h4>
                    </div>
                    <div class="text-sm text-emerald-700 space-y-2">
                        @php
                            $currentMonth = now()->month;
                            $cropSeasons = $cropCycle->crop->seasons ?? [];
                            $optimalPlanting = null;
                            foreach ($cropSeasons as $season) {
                                $plantStart = \Carbon\Carbon::parse($season['planting_start_date']);
                                $plantEnd = \Carbon\Carbon::parse($season['planting_end_date']);
                                if ($currentMonth >= $plantStart->month && $currentMonth <= $plantEnd->month) {
                                    $optimalPlanting = $season;
                                    break;
                                }
                            }
                        @endphp
                        
                        @if($optimalPlanting)
                            <p>✓ <strong>Current season is optimal</strong> for {{ $cropCycle->crop->name }} planting.</p>
                            <p class="text-xs">{{ $optimalPlanting['name'] }} season ({{ \Carbon\Carbon::parse($optimalPlanting['planting_start_date'])->format('M d') }} - {{ \Carbon\Carbon::parse($optimalPlanting['planting_end_date'])->format('M d') }})</p>
                        @else
                            @php
                                $nextSeason = collect($cropSeasons)->sortBy('planting_start_date')->firstWhere('planting_start_date', '>', now()->toDateString());
                            @endphp
                            @if($nextSeason)
                                <p>⏳ <strong>Next optimal planting window:</strong></p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($nextSeason['planting_start_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($nextSeason['planting_end_date'])->format('M d, Y') }}</p>
                                <p class="text-xs mt-1">{{ $nextSeason['name'] }} season</p>
                            @endif
                        @endif
                        
                        @if($cropCycle->crop->soil_requirements['ph_range'] ?? false)
                            <p class="text-xs mt-2">
                                <i class="fas fa-flask mr-1"></i>
                                Optimal pH: {{ $cropCycle->crop->soil_requirements['ph_range'] }}
                            </p>
                        @endif
                        @if($cropCycle->crop->min_temperature && $cropCycle->crop->max_temperature)
                            <p class="text-xs">
                                <i class="fas fa-thermometer-half mr-1"></i>
                                Temperature: {{ $cropCycle->crop->min_temperature }}°C - {{ $cropCycle->crop->max_temperature }}°C
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Pest Control Suggestions -->
                <div class="bg-gradient-to-r from-rose-50 to-pink-50 border border-rose-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="bg-rose-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-bug text-rose-600"></i>
                        </div>
                        <h4 class="font-medium text-rose-800">Pest Control Suggestions</h4>
                    </div>
                    <div class="text-sm text-rose-700 space-y-3">
                        @php
                            $pestVulnerabilities = $cropCycle->crop->pest_vulnerabilities ?? [];
                            $commonDiseases = \App\Models\CropAnalysis::getCommonDiseases();
                            
                            // Filter diseases for this crop category
                            $relevantDiseases = [
                                'Maize' => ['Leaf Blight', 'Armyworms', 'Cutworms', 'Aphid Infestation'],
                                'Rice' => ['Bacterial Wilt', 'Fusarium Wilt'],
                                'Beans' => ['Bacterial Spot', 'Aphid Infestation', 'Whiteflies'],
                                'Tomatoes' => ['Early Blight', 'Bacterial Spot', 'Spider Mites'],
                                'Cassava' => ['Mosaic Disease', 'Bacterial Blight'],
                                'Mangoes' => ['Powdery Mildew', 'Anthracnose'],
                                'Bananas' => ['Sigatoka', 'Fusarium Wilt'],
                            ];
                            
                            $cropDiseases = $relevantDiseases[$cropCycle->crop->name] ?? [];
                        @endphp
                        
                        @if(!empty($cropDiseases))
                            <div>
                                <p class="font-medium text-sm mb-2">Common Threats for {{ $cropCycle->crop->name }}:</p>
                                <ul class="text-xs space-y-1 ml-3">
                                    @foreach($cropDiseases as $diseaseName)
                                        @php
                                            $disease = collect($commonDiseases)->firstWhere('name', $diseaseName);
                                        @endphp
                                        @if($disease)
                                            <li class="flex items-start">
                                                <span class="text-rose-400 mt-0.5 mr-2">•</span>
                                                <div>
                                                    <strong>{{ $disease['name'] }}</strong>
                                                    <p class="text-rose-600">{{ \Illuminate\Support\Str::limit($disease['description'], 80) }}</p>
                                                    <p class="text-rose-600 italic text-xs">{{ $disease['recommendation'] }}</p>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(!empty($pestVulnerabilities))
                            <div class="pt-2 border-t border-rose-100">
                                <p class="font-medium text-sm mb-1">Known Vulnerabilities:</p>
                                <ul class="text-xs space-y-1">
                                    @foreach($pestVulnerabilities as $vulnerability)
                                        <li class="flex items-center">
                                            <span class="text-rose-400 mr-2">⚠</span>
                                            {{ ucfirst($vulnerability) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <p class="text-xs mt-2 font-medium">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Prevention: Regular scouting, crop rotation, and maintaining field sanitation can reduce pest pressure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics: Cost vs Yield & Profitability -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-purple-100 text-purple-700 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">{{ $cropCycle->analyses->count() > 0 ? ($cropCycle->harvests->count() > 0 ? ($cropCycle->revenues->count() > 0 ? '10' : '9') : ($cropCycle->revenues->count() > 0 ? '9' : '8')) : ($cropCycle->harvests->count() > 0 ? ($cropCycle->revenues->count() > 0 ? '9' : '8') : ($cropCycle->revenues->count() > 0 ? '8' : '7')) }}</span>
            Profitability Analytics
        </h2>
        
        @php
            // Calculate total costs
            $totalInputsCost = $cropCycle->inputs->sum('cost');
            $totalExpenses = $cropCycle->inputs->sum('cost'); // Can add other expenses if available
            
            // Calculate total revenue
            $totalRevenue = $cropCycle->revenues->sum('amount');
            $totalReceived = $cropCycle->revenues->where('payment_status', 'paid')->sum('amount') + 
                             $cropCycle->revenues->where('payment_status', 'partial')->sum('amount');
            
            // Calculate profit
            $grossProfit = $totalRevenue - $totalInputsCost;
            $netProfit = $totalReceived - $totalExpenses;
            
            // Calculate total yield
            $totalYield = $cropCycle->harvests->sum('quantity_harvested');
            $netYield = $cropCycle->harvests->sum(function($h) { return $h->getNetQuantity(); });
            
            // Calculate efficiency metrics
            $costPerKg = $totalInputsCost > 0 && $netYield > 0 ? $totalInputsCost / $netYield : 0;
            $profitPerKg = $netYield > 0 ? $netProfit / $netYield : 0;
            $roi = $totalExpenses > 0 ? (($netProfit - $totalExpenses) / $totalExpenses) * 100 : 0;
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4 border border-emerald-100">
                <p class="text-xs text-emerald-600 uppercase font-medium mb-1">Total Costs</p>
                <p class="text-2xl font-bold text-emerald-700">KES {{ number_format($totalInputsCost, 0) }}</p>
                <p class="text-xs text-emerald-500 mt-1">Inputs & Supplies</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-xs text-blue-600 uppercase font-medium mb-1">Total Revenue</p>
                <p class="text-2xl font-bold text-blue-700">KES {{ number_format($totalRevenue, 0) }}</p>
                <p class="text-xs text-blue-500 mt-1">{{ $cropCycle->revenues->count() }} sale{{ $cropCycle->revenues->count() != 1 ? 's' : '' }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-50 rounded-lg p-4 border border-purple-100">
                <p class="text-xs text-purple-600 uppercase font-medium mb-1">Total Yield</p>
                <p class="text-2xl font-bold text-purple-700">{{ number_format($totalYield, 2) }} {{ $cropCycle->harvests->first()->unit ?? 'kg' }}</p>
                <p class="text-xs text-purple-500 mt-1">Net: {{ number_format($netYield, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-50 rounded-lg p-4 border border-amber-100">
                <p class="text-xs text-amber-600 uppercase font-medium mb-1">Net Profit</p>
                <p class="text-2xl font-bold text-amber-700">KES {{ number_format($netProfit, 0) }}</p>
                <p class="text-xs text-amber-500 mt-1">ROI: {{ number_format($roi, 1) }}%</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Cost vs Yield Chart Placeholder -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-chart-bar text-emerald-600 mr-2"></i>
                    Cost vs Yield Analysis
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Investment</span>
                        <span class="text-sm font-medium text-gray-800">KES {{ number_format($totalInputsCost, 0) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-emerald-600 h-3 rounded-full" style="width: {{ min(100, ($totalInputsCost / max($totalRevenue, 1)) * 100) }}%;"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Revenue Generated</span>
                        <span class="text-sm font-medium text-gray-800">KES {{ number_format($totalRevenue, 0) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: 100%;"></div>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm text-gray-600">Profit Margin</span>
                        <span class="text-sm font-medium text-gray-800">
                            @if($totalRevenue > 0)
                                {{ number_format(($grossProfit / $totalRevenue) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="@if($grossProfit > 0) bg-green-600 @else bg-red-600 @endif h-3 rounded-full" style="width: {{ min(100, abs($grossProfit) / max($totalInputsCost, 1) * 100) }}%;"></div>
                    </div>
                </div>
                
                @if($costPerKg > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-2">Efficiency Metrics</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white rounded p-2 text-center">
                            <p class="text-gray-500">Cost per {{ $cropCycle->harvests->first()->unit ?? 'kg' }}</p>
                            <p class="font-bold text-emerald-600">KES {{ number_format($costPerKg, 2) }}</p>
                        </div>
                        <div class="bg-white rounded p-2 text-center">
                            <p class="text-gray-500">Profit per {{ $cropCycle->harvests->first()->unit ?? 'kg' }}</p>
                            <p class="font-bold @if($profitPerKg > 0) text-green-600 @else text-red-600 @endif">
                                @if($profitPerKg > 0) +
                                @endif
                                KES {{ number_format($profitPerKg, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Comparison with Previous Cycles -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Performance vs Previous Cycles
                </h3>
                @if($cropCycle->field && $cropCycle->field->cropCycles()->where('start_date', '<', $cropCycle->start_date)->count() > 0)
                    @php
                        $previousCycles = $cropCycle->field->cropCycles()
                            ->where('start_date', '<', $cropCycle->start_date)
                            ->with(['harvests', 'inputs', 'revenues'])
                            ->orderBy('start_date', 'desc')
                            ->take(3)
                            ->get();
                    @endphp
                    <div class="space-y-3">
                        @foreach($previousCycles as $index => $prevCycle)
                            @php
                                $prevTotalCost = $prevCycle->inputs->sum('cost');
                                $prevTotalRevenue = $prevCycle->revenues->sum('amount');
                                $prevTotalYield = $prevCycle->harvests->sum('quantity_harvested');
                                $prevNetYield = $prevCycle->harvests->sum(function($h) { return $h->getNetQuantity(); });
                                $prevProfit = $prevTotalRevenue - $prevTotalCost;
                            @endphp
                            <div class="bg-white rounded p-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ \Carbon\Carbon::parse($prevCycle->start_date)->format('M Y') }}
                                        ({{ $prevCycle->crop_name ?? $prevCycle->crop->name ?? 'N/A' }})
                                    </span>
                                    @if($prevProfit > $netProfit)
                                        <span class="text-xs text-red-500">▼ {{ number_format((($prevProfit - $netProfit) / max($prevProfit, 1)) * 100, 1) }}%</span>
                                    @elseif($prevProfit < $netProfit && $prevProfit > 0)
                                        <span class="text-xs text-green-500">▲ {{ number_format((($netProfit - $prevProfit) / $prevProfit) * 100, 1) }}%</span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span>Yield: {{ number_format($prevNetYield, 2) }} {{ $prevCycle->harvests->first()->unit ?? 'kg' }}</span>
                                    <span>Profit: KES {{ number_format($prevProfit, 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">
                        No previous cycles for comparison<br>
                        <span class="text-xs">This is the first crop cycle for this field.</span>
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Key Insights -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-amber-500 mr-2"></i>
                Key Insights & Recommendations
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($profitPerKg < 50 && $profitPerKg > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-amber-100 p-2 rounded-lg mr-3 mt-0.5">
                            <i class="fas fa-info-circle text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Optimization Opportunity</p>
                            <p class="text-xs text-amber-700 mt-1">
                                Profit per kg (KES {{ number_format($profitPerKg, 2) }}) is below optimal. Consider:
                                <ul class="list-disc list-inside mt-1 space-y-0.5">
                                    <li>Negotiating better input prices</li>
                                    <li>Improving yield through better practices</li>
                                    <li>Finding higher-value markets</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($roi < 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-red-100 p-2 rounded-lg mr-3 mt-0.5">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-800">Loss Detected</p>
                            <p class="text-xs text-red-700 mt-1">
                                ROI is negative ({{ number_format($roi, 1) }}%). Review costs and pricing strategy.
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($roi > 50)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-green-100 p-2 rounded-lg mr-3 mt-0.5">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">Excellent ROI</p>
                            <p class="text-xs text-green-700 mt-1">
                                ROI of {{ number_format($roi, 1) }}% indicates highly profitable operation. Consider expanding.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3 mt-0.5">
                            <i class="fas fa-calculator text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Break-even Analysis</p>
                            <p class="text-xs text-blue-700 mt-1">
                                @if($costPerKg > 0 && $totalRevenue > 0)
                                    Break-even price: KES {{ number_format($costPerKg, 2) }}/{{ $cropCycle->harvests->first()->unit ?? 'kg' }}<br>
                                    <span class="font-medium">Actual: KES {{ number_format($totalRevenue / max($netYield, 1), 2) }}/{{ $cropCycle->harvests->first()->unit ?? 'kg' }}</span>
                                @else
                                    Insufficient data for analysis
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Harvest/Revenue Buttons -->
    <div class="flex space-x-3 mt-4">
        <a href="{{ route('harvests.create') }}?crop_cycle={{ $cropCycle->id }}&field={{ $cropCycle->field_id }}&farm={{ $cropCycle->farm_id }}&crop={{ $cropCycle->crop_id }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
            <i class="fas fa-plus mr-1"></i> Record Harvest
        </a>
        <a href="{{ route('revenues.create') }}?crop_cycle={{ $cropCycle->id }}&farm={{ $cropCycle->farm_id }}&crop={{ $cropCycle->crop_id }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
            <i class="fas fa-cash-register mr-1"></i> Record Sale
        </a>
    </div>
</div>
@endsection
