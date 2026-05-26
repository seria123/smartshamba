@extends('layouts.MainLayout')

@section('title', 'Feed Type Details - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold mb-0">{{ $feedType->name }}</h1>
                <small class="text-gray-500">{{ $feedType->description ?? 'Feed type details' }}</small>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('feed-types.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                    ← Back to Feed Types
                </a>
                 @if(auth()->user()->hasRole(['admin', 'manager']))
                     <a href="{{ route('feed-types.edit', $feedType) }}" class="btn btn-primary whitespace-nowrap">
                         <i class="fas fa-edit me-2"></i>Edit Feed Type
                     </a>
                 @endif
            </div>
        </div>

        {{-- Feed Type Details Card --}}
        <div class="card-modern p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Default Unit</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $feedType->default_unit }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Minimum Threshold</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $feedType->min_threshold }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full @if($feedType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        {{ $feedType->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Current Stock Level</p>
                    <p class="text-xl font-semibold text-gray-900">
                        {{ $feedType->totalQuantity() }} {{ $feedType->default_unit }}
                        @if($feedType->isBelowThreshold())
                            <span class="ml-2 px-2 py-0.5 text-sm font-semibold bg-red-100 text-red-800 rounded">LOW STOCK</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection