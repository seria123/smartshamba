@extends('layouts.MainLayout')

@section('title', 'Add Feed Type - SmartShamba')

@section('content')
<div class="p-4 max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('feed-types.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Feed Types
        </a>
    </div>

    <div class="bg-green-50 rounded-lg shadow-md p-6" style="border-left: 4px solid #43A047;">
        <h4 class="text-lg font-semibold mb-4"><i class="fas fa-plus mr-2"></i>Add New Feed Type</h4>

        <form action="{{ route('feed-types.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="form-label">Feed Type Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="form-input-modern @error('name') border-red-500 @enderror"
                        placeholder="e.g., Hay, Maize Bran, Silage">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Default Unit -->
                <div>
                    <label for="default_unit" class="form-label">Default Unit</label>
                    <input type="text" name="default_unit" id="default_unit" value="{{ old('default_unit') }}"
                        class="form-input-modern"
                        placeholder="e.g., kg, bags, tonnes">
                    @error('default_unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Threshold -->
                <div>
                    <label for="min_threshold" class="form-label">Minimum Stock Threshold</label>
                    <input type="number" name="min_threshold" id="min_threshold" value="{{ old('min_threshold') }}" step="0.01" min="0"
                        class="form-input-modern"
                        placeholder="Alert when stock falls below this level">
                    @error('min_threshold')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="form-textarea-modern @error('description') border-red-500 @enderror"
                    placeholder="Optional description of this feed type...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('feed-types.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Create Feed Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
