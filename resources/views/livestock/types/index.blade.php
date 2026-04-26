@extends('layouts.MainLayout')

@section('title', 'Livestock Types - SmartShamba')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-semibold"><i class="fas fa-tags mr-2"></i>Livestock Types</h4>
        <a href="{{ route('livestock-types.create') }}" class="bg-primary text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($types as $type)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-bold text-gray-800">{{ $type->name }}</h5>
                @if($type->requires_individual_tracking)
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Tag</span>
                @endif
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ $type->description ?? 'No description' }}</p>
            <div class="flex justify-between text-sm">
                <span class="text-green-600"><i class="fas fa-check-circle"></i> {{ $type->healthy_count ?? 0 }}</span>
                <span class="text-red-600"><i class="fas fa-heartbeat"></i> {{ $type->sick_count ?? 0 }}</span>
                <span class="text-gray-700 font-semibold">Total: {{ $type->total_count ?? 0 }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            No livestock types. <a href="{{ route('livestock-types.create') }}" class="text-primary">Add one</a>
        </div>
        @endforelse
    </div>
</div>
@endsection