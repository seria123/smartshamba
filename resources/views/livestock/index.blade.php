@extends('layouts.MainLayout')

@section('title', 'Livestock Management - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Livestock Management</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor and manage all your livestock animals</p>
        </div>
        <a href="{{ route('livestock.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-xl border-2 border-emerald-700 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
            <i class="fas fa-plus"></i>
            <span>Add Animal</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Statistics Overview -->
    @if($types->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($types as $type)
        <x-ui.card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">{{ $type->name }}</h3>
                <i class="fas fa-cow text-xl text-emerald-600 opacity-40"></i>
            </div>
            <p class="text-3xl font-bold text-emerald-600">{{ $type->total }}</p>
            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    {{ $type->healthy }} healthy
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    {{ $type->sick }} sick
                </span>
            </div>
        </x-ui.card>
        @endforeach
    </div>
    @endif

    <!-- Quick Stats Bar -->
    <x-ui.card>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 rounded-lg bg-emerald-50">
                <p class="text-2xl font-bold text-emerald-600">{{ $livestock->whereIn('status', ['healthy', 'sick'])->count() }}</p>
                <p class="text-xs text-emerald-700 font-medium">Active Animals</p>
            </div>
            <div class="text-center p-3 rounded-lg bg-blue-50">
                <p class="text-2xl font-bold text-blue-600">{{ $livestock->where('status', 'healthy')->count() }}</p>
                <p class="text-xs text-blue-700 font-medium">Healthy</p>
            </div>
            <div class="text-center p-3 rounded-lg bg-orange-50">
                <p class="text-2xl font-bold text-orange-600">{{ $livestock->where('status', 'sick')->count() }}</p>
                <p class="text-xs text-orange-700 font-medium">Need Attention</p>
            </div>
            <div class="text-center p-3 rounded-lg bg-purple-50">
                <p class="text-2xl font-bold text-purple-600">{{ $livestock->where('status', 'sold')->count() }}</p>
                <p class="text-xs text-purple-700 font-medium">Sold</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Livestock Table -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-800">All Animals</h3>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Total: {{ $livestock->count() }}</span>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span>Active: {{ $livestock->whereIn('status', ['healthy', 'sick'])->count() }}</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tag ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Farm</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Weight</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($livestock ?? [] as $animal)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ 
                                        $animal->status === 'healthy' ? 'bg-green-100 text-green-700' : 
                                        ($animal->status === 'sick' ? 'bg-red-100 text-red-700' : 
                                        ($animal->status === 'sold' ? 'bg-gray-100 text-gray-700' : 'bg-slate-100 text-slate-700'))
                                    }}">
                                        {{ $animal->tag_number ?? 'UNASSIGNED' }}
                                    </span>
                                    @if($animal->tracking_id)
                                        <span class="text-xs text-gray-400">{{ $animal->tracking_id }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $animal->name ?? 'Unnamed' }}</div>
                                @if($animal->acquisition_type)
                                    <div class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $animal->acquisition_type) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $animal->type->name ?? 'N/A' }}</div>
                                @if($animal->breed)
                                    <div class="text-xs text-gray-500">{{ $animal->breed }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $animal->farm->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($animal->gender)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ 
                                        $animal->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'
                                    }}">
                                        {{ ucfirst($animal->gender) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($animal->birth_date)
                                    {{ \Carbon\Carbon::parse($animal->birth_date)->diffForHumans() }}
                                @else
                                    {{ $animal->date_acquired ? 'Since ' . $animal->date_acquired->format('M d, Y') : '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($animal->weight)
                                    <div class="flex items-center gap-1">
                                        <span class="font-medium">{{ $animal->weight }}</span>
                                        <span class="text-gray-400">kg</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                    $animal->status === 'healthy' ? 'bg-green-100 text-green-800' : 
                                    ($animal->status === 'sick' ? 'bg-red-100 text-red-800' : 
                                    ($animal->status === 'sold' ? 'bg-gray-100 text-gray-800' : 'bg-slate-100 text-slate-800'))
                                }}">
                                    {{ ucfirst($animal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('livestock.show', $animal->id) }}" 
                                       class="text-emerald-600 hover:text-emerald-700" 
                                       title="View details">
                                         <i class="fas fa-eye"></i>
                                     </a>
                                    <a href="{{ route('livestock.edit', $animal->id) }}" 
                                       class="text-blue-600 hover:text-blue-700" 
                                       title="Edit">
                                         <i class="fas fa-edit"></i>
                                     </a>
                                      <a href="{{ route('livestock-analysis.create') }}?livestock_id={{ $animal->id }}"
                                       class="text-rose-600 hover:text-rose-700" 
                                       title="New disease analysis">
                                         <i class="fas fa-virus"></i>
                                     </a>
                                     <a href="{{ route('livestock.analysis-history', $animal) }}" 
                                       class="text-amber-600 hover:text-amber-700" 
                                       title="View analysis history">
                                         <i class="fas fa-chart-line"></i>
                                     </a>
                                     <a href="{{ route('livestock.locations.create', $animal->id) }}" 
                                       class="text-purple-600 hover:text-purple-700" 
                                       title="Record location">
                                         <i class="fas fa-map-marker-alt"></i>
                                     </a>
                                 </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-cow text-4xl text-gray-300 mb-3"></i>
                                    <h3 class="text-lg font-medium text-gray-500 mb-2">No livestock found</h3>
                                    <p class="text-gray-400 mb-6">Add your first animal to get started with tracking</p>
                                    <a href="{{ route('livestock.create') }}" 
                                       class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg border-2 border-emerald-700 hover:border-emerald-600 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        <span>Add First Animal</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $livestock->links() }}
        </div>
    </x-ui.card>

    <!-- Recent Activity -->
    <x-ui.card>
        <h3 class="font-bold text-lg text-gray-800 mb-4">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($livestock->whereIn('status', ['healthy', 'sick'])->take(5) as $animal)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ 
                            $animal->status === 'healthy' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
                        }}">
                            <i class="fas fa-cow"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $animal->name ?? 'Unnamed' }}</p>
                            <p class="text-sm text-gray-500">{{ $animal->type->name ?? 'Unknown' }} - {{ $animal->tag_number ?? 'No tag' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ $animal->status === 'healthy' ? 'Healthy' : 'Needs attention' }}</p>
                        <a href="{{ route('livestock.show', $animal->id) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                            View →
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No active livestock to display</p>
            @endforelse
        </div>
    </x-ui.card>

</div>
@endsection