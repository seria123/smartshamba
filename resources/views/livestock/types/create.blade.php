@extends('layouts.MainLayout')

@section('title', 'Add Livestock Type - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Add Livestock Type</h1>
            <p class="text-sm text-gray-500 mt-1">Define a new animal type for tracking</p>
        </div>
        <a href="{{ route('livestock-types.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to List</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <x-ui.card>
        <form action="{{ route('livestock-types.store') }}" method="POST" id="typeCreateForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Type Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="e.g., Cattle, Goats, Poultry">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                        placeholder="e.g., cattle, goats, poultry">
                    <p class="text-xs text-gray-500 mt-1">URL-friendly identifier (lowercase, no spaces)</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('description') border-red-500 @enderror"
                    placeholder="Describe this livestock type...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Options -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="requires_individual_tracking" id="requires_individual_tracking" value="1"
                        {{ old('requires_individual_tracking') ? 'checked' : '' }}
                        class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <label for="requires_individual_tracking" class="text-sm font-medium text-gray-700">
                        Requires Individual Tracking
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2 ml-8">
                    Enable this for livestock types that need individual identification (e.g., cattle, goats, sheep). 
                    Poultry and similar group animals can be tracked collectively.
                </p>
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('livestock-types.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl shadow-md hover:bg-emerald-700 hover:shadow-lg active:scale-95 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Create Type
                </button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
