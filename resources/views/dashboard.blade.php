@extends('layouts.MainLayout')

@section('title', 'Dashboard - SmartShamba')

@section('content')


   

     <!-- Page Header -->
     <div class="flex items-center justify-between">
         <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Dashboard</h1>
         <a href="{{ route('farms.create') }}" class="inline-flex items-center space-x-3 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-4 rounded-xl border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
             <i class="fas fa-plus text-lg"></i>
             <span>Add Farm</span>
         </a>
     </div>

     <!-- Statistics Cards -->
     <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
         <!-- Farms Card -->
         <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-6">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-semibold text-gray-500 tracking-wide">Total Farms</p>
                     <p class="text-4xl font-bold text-gray-800 mt-2 tracking-tight">{{ $totalFarms }}</p>
                 </div>
                 <div class="w-14 h-14 bg-blue-100 rounded-2xl border-2 border-blue-300 flex items-center justify-center text-blue-600">
                     <i class="fas fa-tractor text-2xl"></i>
                 </div>
             </div>
         </div>

         <!-- Fields Card -->
         <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-green-300 transition-all duration-200 p-6">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-semibold text-gray-500 tracking-wide">Total Fields</p>
                     <p class="text-4xl font-bold text-gray-800 mt-2 tracking-tight">{{ $totalFields }}</p>
                 </div>
                 <div class="w-14 h-14 bg-green-100 rounded-2xl border-2 border-green-300 flex items-center justify-center text-green-600">
                     <i class="fas fa-seedling text-2xl"></i>
                 </div>
             </div>
         </div>

         <!-- Sensors Card -->
         <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-purple-300 transition-all duration-200 p-6">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-semibold text-gray-500 tracking-wide">Active Sensors</p>
                     <p class="text-4xl font-bold text-gray-800 mt-2 tracking-tight">{{ $activeSensors }} / {{ $totalSensors }}</p>
                 </div>
                 <div class="w-14 h-14 bg-purple-100 rounded-2xl border-2 border-purple-300 flex items-center justify-center text-purple-600">
                     <i class="fas fa-satellite-dish text-2xl"></i>
                 </div>
             </div>
         </div>

         <!-- Alerts Card -->
         <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-200 p-6">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-semibold text-gray-500 tracking-wide">Unread Alerts</p>
                     <p class="text-4xl font-bold text-gray-800 mt-2 tracking-tight">{{ $unreadAlertsCount }}</p>
                 </div>
                 <div class="w-14 h-14 bg-red-100 rounded-2xl border-2 border-red-300 flex items-center justify-center text-red-600">
                     <i class="fas fa-bell text-2xl"></i>
                 </div>
             </div>
         </div>
     </div>

     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
         <!-- Recent Alerts -->
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden hover:border-emerald-300 transition-all duration-200">
            <div class="px-6 py-4 border-b-2 border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 tracking-wide">Recent Alerts</h2>
                <a href="{{ route('alerts.index') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200">
                    <span>View All</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="p-4">
                @forelse($recentAlerts as $alert)
                    <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0">
                        <div class="mt-1.5">
                            @if($alert->severity == 'high')
                                <div class="w-3 h-3 bg-red-500 rounded-full border-2 border-red-200"></div>
                            @elseif($alert->severity == 'medium')
                                <div class="w-3 h-3 bg-yellow-500 rounded-full border-2 border-yellow-200"></div>
                            @else
                                <div class="w-3 h-3 bg-blue-500 rounded-full border-2 border-blue-200"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 leading-relaxed">{{ $alert->message }}</p>
                            <p class="text-xs text-gray-500 mt-1.5">{{ $alert->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">No recent alerts</p>
                @endforelse
            </div>
        </div>

          <!-- Sensor Status -->
         <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden hover:border-emerald-300 transition-all duration-200">
             <div class="px-6 py-4 border-b-2 border-gray-200 bg-gray-50 flex justify-between items-center">
                 <h2 class="text-lg font-bold text-gray-800 tracking-wide">Sensor Status</h2>
                 <a href="{{ route('sensors.index') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200">
                     <span>View All</span>
                     <i class="fas fa-arrow-right"></i>
                 </a>
             </div>
             <div class="p-4">
                 @forelse($sensors as $sensor)
                     <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                         <div>
                             <p class="text-sm font-semibold text-gray-800 tracking-wide">{{ $sensor->name }}</p>
                             <p class="text-xs text-gray-500 mt-1">{{ $sensor->field->name ?? 'N/A' }} • {{ $sensor->type }}</p>
                         </div>
                         <span class="px-4 py-2 text-xs font-semibold rounded-lg border-2 {{ $sensor->status == 'active' ? 'bg-green-50 border-green-300 text-green-700' : 'bg-gray-50 border-gray-300 text-gray-700' }}">
                             {{ ucfirst($sensor->status) }}
                         </span>
                     </div>
                 @empty
                     <p class="text-sm text-gray-500 text-center py-8">No sensors found</p>
                 @endforelse
             </div>
         </div>
    </div>

     <!-- Farms Overview -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden hover:border-emerald-300 transition-all duration-200">
        <div class="px-6 py-4 border-b-2 border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800 tracking-wide">Your Farms</h2>
            <a href="{{ route('farms.index') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200">
                <span>View All</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($farms as $farm)
                    <div class="border-2 border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-emerald-300 transition-all duration-200 bg-white group">
                        <h3 class="font-bold text-gray-800 tracking-wide group-hover:text-emerald-700 transition text-lg">{{ $farm->name }}</h3>
                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $farm->location }}</p>
                        <div class="mt-4 pt-4 border-t-2 border-gray-100 flex justify-between text-sm">
                            <span class="text-gray-500 font-medium">Size:</span>
                            <span class="font-bold text-gray-800">{{ $farm->size_hectares }} ha</span>
                        </div>
                        <div class="flex justify-between text-sm pt-3">
                            <span class="text-gray-500 font-medium">Fields:</span>
                            <span class="font-bold text-gray-800">{{ $farm->fields_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8 col-span-3">No farms yet. <a href="{{ route('farms.create') }}" class="text-emerald-700 hover:text-emerald-800 font-bold underline underline-offset-2 decoration-2">Create your first farm</a>.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
