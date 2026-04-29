@extends('layouts.MainLayout')

@section('title', 'Livestock - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Livestock</h1>
            <p class="text-sm text-gray-500 mt-2">Monitor and manage all your livestock animals</p>
        </div>
        <a href="{{ route('livestock.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Livestock</span>
        </a>
    </div>

    <!-- Statistics Cards -->
    @php
    $types = \App\Models\LivestockType::withCount([
        'livestock as total' => function ($q) { $q->whereIn('status', ['healthy', 'sick']); },
        'livestock as healthy' => function ($q) { $q->where('status', 'healthy'); },
        'livestock as sick' => function ($q) { $q->where('status', 'sick'); },
    ])->get();
    @endphp
    
    @if($types->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($types as $type)
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-600 tracking-wide">{{ $type->name }}</p>
                <i class="fas fa-cow text-xl text-emerald-600 opacity-40"></i>
            </div>
            <div class="text-3xl font-bold text-gray-800 mb-3">{{ $type->total }}</div>
            <div class="flex items-center space-x-4 text-xs font-medium">
                <div class="flex items-center space-x-1.5">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-gray-700">{{ $type->healthy }} Healthy</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <span class="text-gray-700">{{ $type->sick }} Sick</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Livestock Table -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800 tracking-wide">All Animals</h2>
        </div>
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tag ID</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Weight</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($livestock ?? [] as $animal)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-100 text-gray-800 text-xs font-bold tracking-wider">
                            {{ $animal->tag_number ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $animal->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $animal->type->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @switch($animal->status)
                            @case('healthy')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                                    <i class="fas fa-check-circle mr-1.5"></i>Healthy
                                </span>
                                @break
                            @case('sick')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                    <i class="fas fa-exclamation-circle mr-1.5"></i>Sick
                                </span>
                                @break
                            @case('sold')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-300">
                                    <i class="fas fa-handshake mr-1.5"></i>Sold
                                </span>
                                @break
                            @case('dead')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-800 text-white border border-slate-900">
                                    <i class="fas fa-times-circle mr-1.5"></i>Dead
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $animal->gender ? ucfirst($animal->gender) : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $animal->weight ? $animal->weight . ' kg' : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('livestock.show', $animal->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('livestock.edit', $animal->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-cow text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium mb-4">No livestock found. Add your first animal to get started.</p>
                            <a href="{{ route('livestock.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 font-semibold text-sm">
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
</div>
@endsection