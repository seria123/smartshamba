@extends('layouts.MainLayout')

@section('title', 'Feed Types - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Feed Types</h1>
            <p class="text-sm text-gray-500 mt-2">Manage feed types and track inventory thresholds</p>
        </div>
        <a href="{{ route('feed_types.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Feed Type</span>
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($feedTypes as $feed)
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 overflow-hidden">
            <!-- Card Header with Color Accent -->
            <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>
            
            <div class="p-6">
                <!-- Title and Unit Badge -->
                <div class="flex items-start justify-between mb-3">
                    <h5 class="text-lg font-bold text-gray-900">{{ $feed->name }}</h5>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold tracking-wide">
                        {{ $feed->default_unit ?? 'unit' }}
                    </span>
                </div>
                
                <!-- Description -->
                @if($feed->description)
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $feed->description }}</p>
                @else
                    <p class="text-sm text-gray-400 italic mb-4">No description</p>
                @endif
                
                <!-- Min Threshold Section -->
                <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Min Threshold</span>
                        <span class="text-lg font-bold {{ $feed->min_threshold ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ $feed->min_threshold ?? 'Not set' }}
                        </span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 pt-2 border-t border-gray-200">
                    <a href="{{ route('feed_types.edit', $feed->id) }}" class="flex-1 inline-flex items-center justify-center space-x-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2.5 rounded-lg border-2 border-yellow-300 transition-all duration-200 font-semibold text-xs">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <form action="{{ route('feed_types.destroy', $feed->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center space-x-1.5 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2.5 rounded-lg border-2 border-red-300 transition-all duration-200 font-semibold text-xs" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-12 text-center">
                <i class="fas fa-sack text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-700 mb-2">No Feed Types Yet</h3>
                <p class="text-gray-500 mb-6">Create your first feed type to start tracking livestock nutrition.</p>
                <a href="{{ route('feed_types.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 font-semibold">
                    <i class="fas fa-plus"></i>
                    <span>Add First Feed Type</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
