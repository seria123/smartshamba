@extends('layouts.MainLayout')

@section('title', $feedType->name . ' - SmartShamba')

@section('content')
<div class="p-4 max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('feed_types.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Feed Types
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-4">
            <h4 class="text-lg font-semibold"><i class="fas fa-wheat-awn mr-2"></i>{{ $feedType->name }}</h4>
            <span class="bg-emerald-100 text-emerald-800 text-sm px-3 py-1 rounded-full">
                {{ $feedType->default_unit ?? 'unit' }}
            </span>
        </div>

        @if($feedType->description)
            <p class="text-gray-600 mb-4">{{ $feedType->description }}</p>
        @else
            <p class="text-gray-400 italic mb-4">No description provided.</p>
        @endif

        <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-200">
            <div>
                <span class="text-sm text-gray-500">Minimum Threshold</span>
                <p class="font-semibold {{ $feedType->min_threshold ? 'text-orange-600' : 'text-gray-400' }}">
                    {{ $feedType->min_threshold ?? 'Not set' }}
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Created</span>
                <p class="font-semibold text-gray-700">{{ $feedType->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="mt-6 flex space-x-3">
            <a href="{{ route('feed_types.edit', $feedType) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('feed_types.destroy', $feedType) }}" method="POST" onsubmit="return confirm('Delete this feed type?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
