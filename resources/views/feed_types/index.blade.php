@extends('layouts.MainLayout')

@section('title', 'Feed Types - SmartShamba')

@section('content')
<div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-semibold"><i class="fas fa-sack mr-2"></i>Feed Types</h4>
        <a href="{{ route('feed_types.create') }}" class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-plus mr-1"></i>Add Feed Type
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($feedTypes as $feed)
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-primary">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-bold text-gray-800">{{ $feed->name }}</h5>
                <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded">
                    {{ $feed->default_unit ?? 'unit' }}
                </span>
            </div>
            @if($feed->description)
                <p class="text-sm text-gray-600 mb-3">{{ $feed->description }}</p>
            @else
                <p class="text-sm text-gray-400 mb-3 italic">No description</p>
            @endif
            
            <div class="flex justify-between text-sm pt-2 border-t border-gray-100">
                <span class="text-gray-500">Min Threshold:</span>
                <span class="font-semibold {{ $feed->min_threshold ? 'text-orange-600' : 'text-gray-400' }}">
                    {{ $feed->min_threshold ?? 'Not set' }}
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            <i class="fas fa-wheat-awn text-4xl mb-3 opacity-30"></i>
            <p>No feed types defined.</p>
            <a href="{{ route('feed_types.create') }}" class="text-primary hover:underline mt-2 inline-block">Add your first feed type</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
