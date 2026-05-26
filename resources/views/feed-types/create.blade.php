@extends('layouts.MainLayout')

@section('title', 'New Feed Type - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌾 New Feed Type</h1>
        <a href="{{ route('feed-types.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <form action="{{ route('feed-types.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Basic Feed Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">1</span>
                Basic Feed Information
            </h2>
            <p class="text-gray-500 text-sm mb-4">Define the feed type details</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required placeholder="e.g., Hay, Maize Bran">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Brief description of the feed type"></textarea>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Unit *</label>
                    <input type="text" name="default_unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required placeholder="e.g., kg, lbs, bags" value="kg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Threshold *</label>
                    <input type="number" name="min_threshold" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required placeholder="e.g., 50" min="0" step="0.01">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="is_active" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" checked>
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Submit -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('feed-types.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancel
        </a>
        <button type="submit" form="feedTypeForm" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
            <i class="fas fa-save mr-2"></i> Create Feed Type
        </button>
    </div>
</div>
@endsection