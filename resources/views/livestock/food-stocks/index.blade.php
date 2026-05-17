@extends('layouts.MainLayout')

@section('title', 'Food Stocks - SmartShamba')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="text-2xl font-bold mb-0"><i class="fas fa-boxes mr-2"></i>Food Stocks</h3>
                <small class="text-gray-500">Monitor and manage livestock feed inventory</small>
            </div>
            <a href="{{ route('food-stocks.create') }}" class="btn btn-primary whitespace-nowrap">
                <i class="fas fa-plus me-2"></i>Add Food Stock
            </a>
        </div>

        {{-- Stock Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($feedTypes as $ft)
            @php $total = $ft->foodStocks->sum('quantity'); @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-center @if($total < $ft->min_threshold) border-red-500 @endif">
                <p class="text-xs text-gray-500 mb-1">{{ $ft->name }}</p>
                <p class="text-2xl font-bold @if($total < $ft->min_threshold) text-red-600 @else text-gray-800 @endif">{{ number_format($total, 0) }}</p>
                <p class="text-xs text-gray-400">{{ $ft->default_unit }}</p>
                @if($total < $ft->min_threshold)
                    <span class="inline-block bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded-full mt-2">LOW</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Stock Details Table --}}
        <div class="card-modern overflow-hidden flex flex-col" style="max-height: calc(100vh - 300px);">
            <div class="overflow-y-auto flex-1">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Feed Type</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Cost</th>
                            <th>Expiry</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $stock->feedType->name }}</td>
                            <td class="px-6 py-4 text-right font-medium">{{ $stock->quantity }} <span class="text-gray-500">{{ $stock->unit }}</span></td>
                            <td class="px-6 py-4 text-right">{{ $stock->unit_cost ? '$' . $stock->unit_cost : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $stock->expiry_date ? $stock->expiry_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4">
                                @if($stock->isExpired())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                @elseif($stock->isLowStock())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Low</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-4 text-gray-500">No food stocks found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection