@extends('layouts.MainLayout')

@section('title', 'Add Fertilizer - SmartShamba')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">
                    Add New Fertilizer
                </h1>
            </div>

            <div class="p-6">
                <form action="{{ route('fertilizers.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fertilizer Type -->
                        <div>
                            <label for="fertilizer_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Fertilizer Type
                            </label>
                            <select id="fertilizer_type_id" name="fertilizer_type_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a fertilizer type</option>
                                @foreach ($fertilizerTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Name
                            </label>
                            <input type="text" id="name" name="name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                Quantity
                            </label>
                            <input type="number" id="quantity" name="quantity" required
                                   min="0" step="0.01"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                                Unit
                            </label>
                            <input type="text" id="unit" name="unit" required
                                   value="kg"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Unit Cost -->
                        <div>
                            <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-1">
                                Unit Cost (Optional)
                            </label>
                            <input type="number" id="unit_cost" name="unit_cost"
                                   min="0" step="0.01"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Expiry Date (Optional)
                            </label>
                            <input type="date" id="expiry_date" name="expiry_date"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Supplier ID -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Supplier ID (Optional)
                            </label>
                            <input type="number" id="supplier_id" name="supplier_id"
                                   min="0"
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
                                       checked>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="btn btn-primary w-flex items-center justify-center">
                            Create Fertilizer
                        </button>
                        <a href="{{ route('fertilizers.index') }}"
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