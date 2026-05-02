@extends('layouts.MainLayout')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Record Sale / Revenue</h1>
                    <p class="text-sm text-gray-500 mt-1">Enter details for a new income transaction</p>
                </div>
                <a href="{{ route('revenues.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('revenues.store') }}" method="POST">
                @csrf
                
                <div class="p-6 space-y-8">
                    <!-- Transaction Type -->
                    <div class="border-b border-gray-100 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Transaction Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm *</label>
                                <select name="farm_id" id="farm_id" class="form-input-modern" required>
                                    <option value="">Select Farm</option>
                                    @foreach(\App\Models\Farm::all() as $farm)
                                    <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="buyer_id" class="block text-sm font-medium text-gray-700 mb-2">Buyer (Optional)</label>
                                <select name="buyer_id" id="buyer_id" class="form-input-modern">
                                    <option value="">Direct Sale</option>
                                    @foreach(\App\Models\Buyer::all() as $buyer)
                                    <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Type -->
                    <div class="border-b border-gray-100 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Product Type
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="livestock_id" class="block text-sm font-medium text-gray-700 mb-2">Livestock (for animal sales)</label>
                                <select name="livestock_id" id="livestock_id" class="form-input-modern">
                                    <option value="">-- Select Livestock --</option>
                                    @foreach($livestock ?? [] as $animal)
                                    <option value="{{ $animal->id }}" {{ old('livestock_id') == $animal->id ? 'selected' : '' }}>
                                        {{ $animal->tag_number ?? 'Untagged' }} - 
                                        {{ $animal->name ?? 'Unnamed' }} 
                                        ({{ $animal->type->name ?? 'Unknown' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-2">Crop (for crop sales)</label>
                                <select name="crop_id" id="crop_id" class="form-input-modern">
                                    <option value="">-- Select Crop --</option>
                                    @foreach(\App\Models\Crop::all() as $crop)
                                    <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                        {{ $crop->name }} ({{ $crop->variety ?? 'Standard' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Amount & Quantity -->
                    <div class="border-b border-gray-100 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Financial Details
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (KES) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                                    <input type="number" step="0.01" name="amount" id="amount" 
                                        class="form-input-modern pl-12" required>
                                </div>
                            </div>
                            <div>
                                <label for="quantity_sold" class="block text-sm font-medium text-gray-700 mb-2">Quantity Sold</label>
                                <input type="number" step="0.01" name="quantity_sold" id="quantity_sold" 
                                    class="form-input-modern" placeholder="e.g. 50">
                            </div>
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                                <input type="text" name="unit" id="unit" 
                                    class="form-input-modern" placeholder="kg, tons, bags...">
                            </div>
                            <div>
                                <label for="price_per_unit" class="block text-sm font-medium text-gray-700 mb-2">Price per Unit (KES)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                                    <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" 
                                        class="form-input-modern pl-12" placeholder="Auto-calculated">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Date & Payment -->
                    <div class="border-b border-gray-100 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Sale & Payment
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="sale_date" class="block text-sm font-medium text-gray-700 mb-2">Sale Date *</label>
                                <input type="date" name="sale_date" id="sale_date" 
                                    class="form-input-modern" required>
                            </div>
                            <div>
                                <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                                <input type="text" name="invoice_number" id="invoice_number" 
                                    class="form-input-modern" placeholder="INV-2026-001">
                            </div>
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-input-modern">
                                    <option value="pending">Pending</option>
                                    <option value="partial">Partial Payment</option>
                                    <option value="paid">Fully Paid</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" 
                                    class="form-input-modern">
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <input type="text" name="payment_method" id="payment_method" 
                                    class="form-input-modern" placeholder="Cash, Bank Transfer, Mobile Money...">
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                            class="form-input-modern resize-none" placeholder="Additional notes about this transaction..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('revenues.index') }}" 
                            class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Sale Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
