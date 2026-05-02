<x-layout.app-layout title="Dashboard">

    <h1 class="text-2xl font-bold mb-6">SmartShamba Dashboard 🌿</h1>

    @auth
        @if (!auth()->user()->farm)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Complete Your Farm Setup</h3>
                <p class="text-blue-600 mb-4">Set up your farm profile to get started with SmartShamba.</p>
                <a href="{{ route('farms.onboarding') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Set Up Farm
                </a>
            </div>
        @endif
    @endauth

     <!-- Smart Alerts Widget -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bell mr-2 text-red-500"></i>
                    Smart Alerts & Notifications
                </h2>
                <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                    View All Alerts →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Fertilizer Alert -->
                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-r-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-leaf text-yellow-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-yellow-800">Fertilizer Reminder</h3>
                    </div>
                    <p class="text-sm text-yellow-700">
                        @php
                            $activeCycles = collect();
                            if (auth()->check()) {
                                $activeCycles = \App\Models\CropCycle::whereHas('field', function($q) {
                                    $q->where('user_id', auth()->id());
                                })->whereIn('current_stage', ['vegetative', 'flowering', 'fruiting'])->get();
                            }
                        @endphp
                        @if($activeCycles->count() > 0)
                            {{ $activeCycles->count() }} crop{{ $activeCycles->count() != 1 ? 's' : '' }} in nutrient-demanding stages
                        @else
                            No immediate fertilizer needs
                        @endif
                    </p>
                </div>
                
                <!-- Weather Alert -->
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded-r-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-cloud-rain text-blue-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-blue-800">Weather Update</h3>
                    </div>
                    <p class="text-sm text-blue-700">
                        @php
                            $rainExpected = false;
                            if (auth()->check()) {
                                $recentWeather = \App\Models\WeatherData::where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))->first();
                                if ($recentWeather && $recentWeather->precipitation > 0) {
                                    $rainExpected = true;
                                }
                            }
                        @endphp
                        @if($rainExpected)
                            Rain expected today - adjust irrigation
                        @else
                            Check weather before irrigation
                        @endif
                    </p>
                </div>
                
                <!-- Pest/Disease Alert -->
                <div class="border-l-4 border-rose-400 bg-rose-50 p-4 rounded-r-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-bug text-rose-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-rose-800">Pest Watch</h3>
                    </div>
                    <p class="text-sm text-rose-700">
                        @if($recentAnalyses && $recentAnalyses->where('severity', 'high')->count() > 0)
                            {{ $recentAnalyses->where('severity', 'high')->count() }} high-severity issue{{ $recentAnalyses->where('severity', 'high')->count() != 1 ? 's' : '' }} detected
                        @else
                            No critical pest/disease alerts
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

     <div class="grid md:grid-cols-3 gap-6 mb-6">

          <x-ui.card>
              <h2 class="font-bold">🐄 Livestock</h2>
              <p>Animals tracking module</p>
          </x-ui.card>

          <x-ui.card>
              <h2 class="font-bold">🌱 Crops</h2>
              <p>Crop monitoring system</p>
          </x-ui.card>

          <x-ui.card>
              <h2 class="font-bold">📊 Analytics</h2>
              <p>Farm intelligence dashboard</p>
          </x-ui.card>

      </div>

    <!-- Disease Analysis Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-virus mr-2"></i>
                Disease Analysis
            </h2>
            <a href="{{ route('crop_analyses.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                <i class="fas fa-camera mr-2"></i>
                New Analysis
            </a>
        </div>
        
        @if(isset($recentAnalyses) && $recentAnalyses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recentAnalyses as $analysis)
            @php
                $confidence = $analysis->confidence_score ?? 0;
                $confidenceColor = $confidence > 80 ? 'success' : ($confidence > 50 ? 'warning' : 'danger');
            @endphp
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-2">
                    <span class="badge bg-{{ $analysis->status_color }}">
                        {{ ucfirst($analysis->status) }}
                    </span>
                    @if($analysis->severity)
                    <span class="badge bg-{{ $analysis->severity_color }}">
                        {{ ucfirst($analysis->severity) }}
                    </span>
                    @endif
                </div>
                
                @if($analysis->image_path)
                <div class="mb-3">
                    <img src="{{ Storage::url($analysis->image_path) }}" 
                         class="w-full h-32 object-cover rounded" 
                         alt="Crop image">
                </div>
                @endif
                
                <h4 class="font-semibold text-gray-900 truncate">
                    {{ $analysis->diagnosis ?? 'Unknown Condition' }}
                </h4>
                
                @if($analysis->cropCycle)
                <p class="text-sm text-gray-500 mb-2">
                    {{ $analysis->cropCycle->crop->name ?? $analysis->cropCycle->crop_name ?? 'N/A' }}
                    @if($analysis->cropCycle->field)
                    • {{ $analysis->cropCycle->field->name }}
                    @endif
                </p>
                @endif
                
                @if($analysis->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ \Illuminate\Support\Str::limit($analysis->description, 100) }}
                </p>
                @endif
                
                @if($analysis->recommendation)
                <div class="bg-emerald-50 p-2 rounded text-sm mb-3">
                    <strong class="text-emerald-800">💡</strong>
                    {{ \Illuminate\Support\Str::limit($analysis->recommendation, 80) }}
                </div>
                @endif
                
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        {{ $analysis->created_at->diffForHumans() }}
                    </span>
                    <span class="badge bg-{{ $confidenceColor }}">
                        {{ number_format($confidence, 1) }}%
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('crop_analyses.index') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                View All Analyses →
            </a>
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-leaf text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No disease analyses yet</p>
            <a href="{{ route('crop_analyses.create') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                Create your first analysis →
            </a>
        </div>
        @endif
    </div>

</x-layout.app-layout>