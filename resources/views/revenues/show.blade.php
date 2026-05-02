@extends('layouts.MainLayout')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb & Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('revenues.index') }}" class="text-gray-500 hover:text-emerald-600">Sales</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">Sale Details</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('revenues.edit', $revenue) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('revenues.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Badge -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wider font-medium">Sale Amount</p>
                            <p class="text-4xl font-bold text-gray-900 mt-1">KES {{ number_format($revenue->amount, 2) }}</p>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ 
                            $revenue->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                            ($revenue->payment_status === 'partial' ? 'bg-blue-100 text-blue-800' : 
                            ($revenue->payment_status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'))
                        }}">
                            {{ ucfirst($revenue->payment_status) }}
                        </span>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Product Information
                    </h3>
                    @if($revenue->livestock_id)
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-purple-900">Livestock Sale</h4>
                                    <p class="text-sm text-purple-700">{{ $revenue->livestock->name ?? 'Unnamed' }} - {{ $revenue->livestock->tag_number ?? 'Untagged' }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($revenue->crop_id)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-green-900">Crop Sale</h4>
                                    <p class="text-sm text-green-700">{{ $revenue->crop->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Other Sale</h4>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Quantity</p>
                            <p class="text-lg font-medium text-gray-900">{{ $revenue->quantity_sold ?? '--' }} {{ $revenue->unit ?? '' }}</p>
                        </div>
                        @if($revenue->price_per_unit)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Price per Unit</p>
                            <p class="text-lg font-medium text-gray-900">KES {{ number_format($revenue->price_per_unit, 2) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Transaction Details
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-500">Sale Date</span>
                            <span class="font-medium text-gray-900">{{ $revenue->sale_date->format('F j, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-500">Invoice Number</span>
                            <span class="font-medium text-gray-900">{{ $revenue->invoice_number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-500">Payment Method</span>
                            <span class="font-medium text-gray-900">{{ $revenue->payment_method ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-gray-500">Payment Date</span>
                            <span class="font-medium text-gray-900">{{ $revenue->payment_date?->format('F j, Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                @if($revenue->notes)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Notes
                    </h3>
                    <p class="text-gray-600 leading-relaxed">{{ $revenue->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Buyer Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Customer
                    </h3>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-900">{{ $revenue->buyer?->name ?? 'Direct Sale' }}</p>
                        @if($revenue->buyer?->contact)
                        <p class="text-sm text-gray-500">{{ $revenue->buyer->contact }}</p>
                        @endif
                    </div>
                </div>

                <!-- Farm Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                        Farm
                    </h3>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-900">{{ $revenue->farm?->name ?? 'N/A' }}</p>
                        @if($revenue->farm?->location)
                        <p class="text-sm text-gray-500">{{ $revenue->farm->location }}</p>
                        @endif
                    </div>
                </div>

                <!-- Profit Analysis -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-sm border border-emerald-100 p-6">
                    <h3 class="text-lg font-semibold text-emerald-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Profit Analysis
                    </h3>
                    @php
                        $profit = $revenue->calculateProfit();
                    @endphp
                    <div class="text-center">
                        @if($profit > 0)
                            <p class="text-3xl font-bold text-emerald-600">KES {{ number_format($profit, 2) }}</p>
                            <p class="text-sm text-emerald-700 mt-1">Net Profit</p>
                            <div class="mt-3 inline-flex items-center px-3 py-1 bg-emerald-100 rounded-full">
                                <svg class="w-4 h-4 text-emerald-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                <span class="text-xs font-medium text-emerald-700">Positive</span>
                            </div>
                        @elseif($profit < 0)
                            <p class="text-3xl font-bold text-red-600">KES {{ number_format($profit, 2) }}</p>
                            <p class="text-sm text-red-700 mt-1">Net Loss</p>
                            <div class="mt-3 inline-flex items-center px-3 py-1 bg-red-100 rounded-full">
                                <svg class="w-4 h-4 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                <span class="text-xs font-medium text-red-700">Loss</span>
                            </div>
                        @else
                            <p class="text-3xl font-bold text-gray-600">KES 0.00</p>
                            <p class="text-sm text-gray-700 mt-1">Break Even</p>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-emerald-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-emerald-700">Revenue</span>
                            <span class="font-medium text-emerald-900">KES {{ number_format($revenue->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-emerald-700">Expenses (to date)</span>
                            <span class="font-medium text-emerald-900">KES {{ number_format($revenue->amount - $profit, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        @if($revenue->payment_status !== 'paid')
                        <form action="{{ route('revenues.markAsPaid', $revenue) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" 
                                onclick="return confirm('Mark this sale as fully paid?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mark as Paid
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('revenues.edit', $revenue) }}" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium rounded-lg border border-amber-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
