@extends('layouts.MainLayout')

@section('title', 'Livestock Type Details - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">{{ $livestockType->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Livestock type configuration</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('livestock-types.edit', $livestockType) }}" 
               class="inline-flex items-center space-x-2 bg-yellow-600 hover:bg-yellow-700 text-white px-5 py-3 rounded-xl border-2 border-yellow-700 hover:border-yellow-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('livestock-types.index') }}" 
               class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Type Info Card -->
    <x-ui.card>
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center {{ 
                    $livestockType->name === 'Cattle' ? 'bg-blue-100 text-blue-600' : 
                    ($livestockType->name === 'Goats' ? 'bg-green-100 text-green-600' : 
                    ($livestockType->name === 'Sheep' ? 'bg-purple-100 text-purple-600' : 
                    ($livestockType->name === 'Poultry' ? 'bg-orange-100 text-orange-600' : 'bg-emerald-100 text-emerald-600')))
                }}">
                    @if($livestockType->name === 'Cattle')
                        <i class="fas fa-cow text-2xl"></i>
                    @elseif($livestockType->name === 'Goats')
                        <i class="fas fa-hiking text-2xl"></i>
                    @elseif($livestockType->name === 'Sheep')
                        <i class="fas fa-sheep text-2xl"></i>
                    @elseif($livestockType->name === 'Poultry')
                        <i class="fas fa-dove text-2xl"></i>
                    @else
                        <i class="fas fa-paw text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $livestockType->name }}</h2>
                    @if($livestockType->requires_individual_tracking)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                            <i class="fas fa-tag mr-1"></i>
                            Requires Individual Tags
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($livestockType->description)
            <div class="mb-6">
                <h3 class="font-bold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600 leading-relaxed">{{ $livestockType->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-emerald-50 rounded-lg">
                <p class="text-2xl font-bold text-emerald-600">{{ $livestockType->healthy_count ?? 0 }}</p>
                <p class="text-xs text-emerald-700 font-medium">Healthy</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-2xl font-bold text-red-600">{{ $livestockType->sick_count ?? 0 }}</p>
                <p class="text-xs text-red-700 font-medium">Sick</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-gray-700">{{ $livestockType->total_count ?? 0 }}</p>
                <p class="text-xs text-gray-700 font-medium">Total</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm font-medium text-blue-700">{{ strtoupper($livestockType->slug) }}</p>
                <p class="text-xs text-blue-500">Slug</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Associated Livestock -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-800">Associated Livestock ({{ $livestockType->livestock->count() }})</h3>
            @if($livestockType->requires_individual_tracking)
                <a href="{{ route('livestock.create') }}?type={{ $livestockType->id }}" 
                   class="inline-flex items-center space-x-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    <i class="fas fa-plus"></i>
                    <span>Add Animal</span>
                </a>
            @endif
        </div>

        @if($livestockType->livestock->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tag/Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Farm</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Weight</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($livestockType->livestock as $livestock)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $livestock->name ?? 'Unnamed' }}</div>
                                    @if($livestock->tag_number)
                                        <div class="text-xs text-gray-500">Tag: {{ $livestock->tag_number }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $livestock->farm->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ 
                                        $livestock->status === 'healthy' ? 'bg-green-100 text-green-800' : 
                                        ($livestock->status === 'sick' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')
                                    }}">
                                        {{ ucfirst($livestock->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($livestock->gender ?? 'N/A') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $livestock->weight ? $livestock->weight . ' kg' : 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('livestock.show', $livestock) }}" class="text-emerald-600 hover:text-emerald-700">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-cow text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No livestock of this type yet.</p>
                @if($livestockType->requires_individual_tracking)
                    <a href="{{ route('livestock.create') }}?type={{ $livestockType->id }}" 
                       class="inline-flex items-center space-x-2 text-emerald-600 hover:text-emerald-700 font-medium mt-2">
                        <i class="fas fa-plus"></i>
                        <span>Add First Animal</span>
                    </a>
                @endif
            </div>
        @endif
    </x-ui.card>

</div>
@endsection
