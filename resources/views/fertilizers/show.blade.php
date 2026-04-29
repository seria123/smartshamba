@extends('layouts.MainLayout')

@section('title', 'Fertilizer Details - SmartShamba')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $fertilizer->name }}
                    </h1>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('fertilizers.edit', $fertilizer) }}" class="btn btn-outline btn-secondary">
                            Edit
                        </a>
                        <a href="{{ route('fertilizers.index') }}" class="btn btn-outline">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <p class="text-gray-600"><strong>Name:</strong> {{ $fertilizer->name }}</p>
                        <p class="text-gray-600"><strong>Type:</strong> {{ ucfirst($fertilizer->fertilizerType->type) }}</p>
                        <p class="text-gray-600"><strong>Description:</strong> {{ $fertilizer->fertilizerType->description ?? 'N/A' }}</p>
                    </div>

                    <!-- Stock Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Information</h3>
                        <p class="text-gray-600"><strong>Quantity:</strong> {{ $fertilizer->quantity }} {{ $fertilizer->unit }}</p>
                        <p class="text-gray-600"><strong>Unit Cost:</strong> ${{ number_format($fertilizer->unit_cost ?: 0, 2) }}</p>
                        <p class="text-gray-600"><strong>Total Cost:</strong> ${{ number_format($fertilizer->totalCost(), 2) }}</p>
                        <p class="text-gray-600"><strong>Expiry Date:</strong> {{ $fertilizer->expiry_date ? $fertilizer->expiry_date->format('M d, Y') : 'N/A' }}</p>
                        <p class="text-gray-600"><strong>Status:</strong> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $fertilizer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $fertilizer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection