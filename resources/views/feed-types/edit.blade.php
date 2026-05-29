@extends('layouts.MainLayout')

@section('title', 'Edit Feed Type - SmartShamba')

@section('content')
<div class="p-4 max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('feed-types.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Feed Types
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold mb-4"><i class="fas fa-edit mr-2"></i>Edit Feed Type</h4>

        <form action="{{ route('feed-types.update', $feedType) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Feed Type Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $feedType->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Default Unit -->
                <div>
                    <label for="default_unit" class="block text-sm font-medium text-gray-700 mb-2">Default Unit</label>
                    <input type="text" name="default_unit" id="default_unit" value="{{ old('default_unit', $feedType->default_unit) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('default_unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Threshold -->
                <div>
                    <label for="min_threshold" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Threshold</label>
                    <input type="number" name="min_threshold" id="min_threshold" value="{{ old('min_threshold', $feedType->min_threshold) }}" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('min_threshold')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $feedType->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('feed-types.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Feed Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
