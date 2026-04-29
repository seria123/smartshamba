@extends('layouts.MainLayout')

@section('title', 'Edit Fertilizer Type - SmartShamba')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Fertilizer Type
                </h1>
            </div>

            <div class="p-6">
                <form action="{{ route('fertilizer_types.update', $fertilizerType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Name
                            </label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name', $fertilizerType->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Type
                            </label>
                            <select id="type" name="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a type</option>
                                <option value="nitrogen" {{ old('type', $fertilizerType->type) == 'nitrogen' ? 'selected' : '' }}>Nitrogen</option>
                                <option value="phosphorus" {{ old('type', $fertilizerType->type) == 'phosphorus' ? 'selected' : '' }}>Phosphorus</option>
                                <option value="potassium" {{ old('type', $fertilizerType->type) == 'potassium' ? 'selected' : '' }}>Potassium</option>
                                <option value="compound" {{ old('type', $fertilizerType->type) == 'compound' ? 'selected' : '' }}>Compound</option>
                                <option value="organic" {{ old('type', $fertilizerType->type) == 'organic' ? 'selected' : '' }}>Organic</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description (Optional)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $fertilizerType->description) }}</textarea>
                        </div>

                        <!-- Default Unit -->
                        <div>
                            <label for="default_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                Default Unit
                            </label>
                            <input type="text" id="default_unit" name="default_unit" required
                                   value="{{ old('default_unit', $fertilizerType->default_unit) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Min Threshold -->
                        <div>
                            <label for="min_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                                Minimum Threshold
                            </label>
                            <input type="number" id="min_threshold" name="min_threshold" required
                                   min="0"
                                   value="{{ old('min_threshold', $fertilizerType->min_threshold) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <div class="mt-1 flex items-center">
                                <input type="checkbox" id="is_active" name="is_active"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       {{ old('is_active', $fertilizerType->is_active) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="btn btn-primary w-flex items-center justify-center">
                            Update Fertilizer Type
                        </button>
                        <a href="{{ route('fertilizer_types.show', $fertilizerType) }}"
                           class="btn btn-outline ml-3 w-flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection