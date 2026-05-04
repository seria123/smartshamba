@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('livestock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Livestock
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Animal Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-emerald-200">
                    <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2">
                        <i class="fas fa-cow"></i> Animal Details
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Tag Number</p>
                                <p class="font-semibold text-lg">{{ $livestock->tag_number ?? 'Untagged' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Name</p>
                                <p class="font-semibold text-lg">{{ $livestock->name ?? 'Unnamed' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Type</p>
                                <p class="font-semibold text-lg">{{ optional($livestock->type)->name ?? 'Unknown' }}</p>
                            </div>
                            @if($livestock->breed)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Breed</p>
                                <p class="font-semibold text-lg">{{ $livestock->breed }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Gender</p>
                                @if($livestock->gender)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $livestock->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ ucfirst($livestock->gender) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Age / Acquired</p>
                                <p class="font-semibold text-lg">
                                    @if($livestock->birth_date)
                                        {{ \Carbon\Carbon::parse($livestock->birth_date)->age }} years old
                                    @elseif($livestock->date_acquired)
                                        Acquired: {{ $livestock->date_acquired->format('M d, Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Weight</p>
                                <p class="font-semibold text-lg">{{ $livestock->weight ? $livestock->weight . ' kg' : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ 
                                    $livestock->status === 'healthy' ? 'bg-green-100 text-green-700' : 
                                    ($livestock->status === 'sick' ? 'bg-red-100 text-red-700' : 
                                    ($livestock->status === 'sold' ? 'bg-gray-100 text-gray-700' : 'bg-slate-100 text-slate-700')) 
                                }}">
                                    {{ ucfirst($livestock->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Farm</p>
                                <p class="font-semibold text-lg">{{ optional($livestock->farm)->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Current Location</p>
                                <p class="font-semibold text-lg">
                                    @if($livestock->currentLocation)
                                        @if(optional($livestock->currentLocation)->field)
                                            Field: {{ $livestock->currentLocation->field->name }}
                                        @elseif(optional($livestock->currentLocation)->farm)
                                            Farm: {{ $livestock->currentLocation->farm->name }}
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3">
                    <a href="{{ route('livestock.edit', $livestock) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                        <i class="fas fa-edit mr-2"></i> Edit Animal
                    </a>
                    <a href="{{ route('livestock.locations.create', $livestock) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition font-medium">
                        <i class="fas fa-map-marker-alt mr-2"></i> Record Location
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Actions & Analysis -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('livestock-analysis.create') }}?livestock_id={{ $livestock->id }}" 
                       class="flex items-center justify-between w-full px-4 py-3 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition group">
                        <span class="font-medium text-rose-700">Analyze Disease</span>
                        <i class="fas fa-virus text-rose-600 group-hover:scale-110 transition"></i>
                    </a>
                    <a href="{{ route('livestock.analysis-history', $livestock) }}" 
                       class="flex items-center justify-between w-full px-4 py-3 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition group">
                        <span class="font-medium text-amber-700">View Analysis History</span>
                        <i class="fas fa-chart-line text-amber-600 group-hover:scale-110 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Analyses -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-virus text-amber-600"></i> Recent Analyses
                    </h3>
                </div>
                <div class="p-6">
                    @if($analyses->isEmpty())
                        <div class="text-center py-6">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-sm">No analyses recorded yet.</p>
                            <a href="{{ route('livestock-analysis.create', ['livestock_id' => $livestock->id]) }}" class="text-rose-600 hover:underline text-sm font-medium mt-2 inline-block">
                                Upload first image →
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($analyses as $analysis)
                            <div class="border rounded-xl p-3 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $analysis->diagnosis ?? 'Unknown' }}</h4>
                                    <span class="badge bg-{{ $analysis->severity_color ?? 'secondary' }} text-xs">
                                        {{ ucfirst($analysis->severity ?? 'N/A') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">
                                    {{ $analysis->created_at->format('M d, Y') }}
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs px-2 py-1 rounded-full {{ $analysis->status === 'reviewed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($analysis->status) }}
                                    </span>
                                    <a href="{{ route('livestock-analysis.show', $analysis) }}" class="text-xs font-medium text-emerald-600 hover:underline">
                                        View →
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('livestock.analysis-history', $livestock) }}" class="text-sm font-medium text-amber-600 hover:underline">
                                View All Analyses <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
