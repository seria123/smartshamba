@extends('layouts.MainLayout')

@section('title', 'Add Food Stock - SmartShamba')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0"><i class="fas fa-plus mr-2"></i>Add Food Stock</h1>
            <a href="{{ route('food-stocks.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                ← Back to Food Stocks
            </a>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card-modern p-4 sm:p-6 space-y-6" style="border-left: 4px solid #6D4C41;">
            <form action="{{ route('food-stocks.store') }}" method="POST">
                @csrf

{{-- Feed Type --}}
                <div>
                    <label for="feed_type_id" class="form-label">Feed Type <span class="text-danger">*</span></label>
                    <select class="form-select-brown @error('feed_type_id') is-invalid @enderror" id="feed_type_id" name="feed_type_id" required>
                        <option value="">Select Feed Type</option>
                        @foreach($feedTypes as $type)
                            <option value="{{ $type->id }}" {{ old('feed_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->default_unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('feed_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Quantity & Unit --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-input-modern @error('quantity') is-invalid @enderror"
                            id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-input-modern @error('unit') is-invalid @enderror"
                            id="unit" name="unit" value="{{ old('unit') }}" placeholder="kg, bags, etc." required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Cost & Expiry --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="unit_cost" class="form-label">Unit Cost</label>
                        <input type="number" step="0.01" class="form-input-modern @error('unit_cost') is-invalid @enderror"
                            id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" placeholder="0.00">
                        @error('unit_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-input-modern @error('expiry_date') is-invalid @enderror"
                            id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary text-center">
                        <i class="fas fa-save me-2"></i>Add Food Stock
                    </button>
                    <a href="{{ route('food-stocks.index') }}" class="btn btn-outline-secondary text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection