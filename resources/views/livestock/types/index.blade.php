@extends('layouts.MainLayout')

@section('title', 'Livestock Types - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Livestock Types</h1>
            <p class="text-sm text-gray-500 mt-1">Configure animal types and tracking requirements</p>
        </div>
        <a href="{{ route('livestock-types.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-xl border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
            <i class="fas fa-plus"></i>
            <span>Add Type</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($types as $type)
        <x-ui.card>
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ 
                    $type->name === 'Cattle' ? 'bg-blue-100 text-blue-600' : 
                    ($type->name === 'Goats' ? 'bg-green-100 text-green-600' : 
                    ($type->name === 'Sheep' ? 'bg-purple-100 text-purple-600' : 
                    ($type->name === 'Poultry' ? 'bg-orange-100 text-orange-600' : 'bg-emerald-100 text-emerald-600')))
                }}">
                    @if($type->name === 'Cattle')
                        <i class="fas fa-cow text-xl"></i>
                    @elseif($type->name === 'Goats')
                        <i class="fas fa-hiking text-xl"></i>
                    @elseif($type->name === 'Sheep')
                        <i class="fas fa-sheep text-xl"></i>
                    @elseif($type->name === 'Poultry')
                        <i class="fas fa-dove text-xl"></i>
                    @else
                        <i class="fas fa-paw text-xl"></i>
                    @endif
                </div>
                @if($type->requires_individual_tracking)
                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                        Requires Tag
                    </span>
                @endif
            </div>
            
            <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $type->name }}</h3>
            
            @if($type->description)
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $type->description }}</p>
            @endif
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-2 mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="text-center">
                    <p class="text-lg font-bold text-emerald-600">{{ $type->healthy_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Healthy</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-red-600">{{ $type->sick_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Sick</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-700">{{ $type->total_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-2 pt-2 border-t border-gray-100">
                <a href="{{ route('livestock-types.show', $type) }}" 
                   class="flex-1 text-center px-3 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                    View
                </a>
                <a href="{{ route('livestock-types.edit', $type) }}" 
                   class="flex-1 text-center px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    Edit
                </a>
            </div>
        </x-ui.card>
        @empty
        <div class="col-span-full">
            <x-ui.card>
                <div class="text-center py-12">
                    <i class="fas fa-tags text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">No livestock types yet</h3>
                    <p class="text-gray-400 mb-6">Create your first livestock type to get started</p>
                    <a href="{{ route('livestock-types.create') }}" 
                       class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg border-2 border-emerald-600 hover:border-emerald-700 transition-colors">
                        <i class="fas fa-plus"></i>
                        <span>Create Type</span>
                    </a>
                </div>
            </x-ui.card>
        </div>
        @endforelse
    </div>

</div>
@endsection
