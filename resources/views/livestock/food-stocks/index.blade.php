@extends('layouts.MainLayout')

@section('title', 'Food Stocks - SmartShamba')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-semibold"><i class="fas fa-boxes mr-2"></i>Food Stocks</h4>
        <a href="{{ route('food-stocks.create') }}" class="bg-primary text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    @php
    $feedTypes = \App\Models\FeedType::with(['foodStocks' => function($q) { $q->where('is_active', true); }])->get();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
        @foreach($feedTypes as $ft)
        @php $total = $ft->foodStocks->sum('quantity'); @endphp
        <div class="bg-white rounded shadow p-3 text-center @if($total < $ft->min_threshold) border-2 border-red-500 @endif">
            <div class="text-xs text-gray-500">{{ $ft->name }}</div>
            <div class="text-lg font-bold @if($total < $ft->min_threshold) text-red-600 @endif">
                {{ number_format($total, 0) }}
            </div>
            <div class="text-xs text-gray-500">{{ $ft->default_unit }}</div>
            @if($total < $ft->min_threshold)
                <span class="block bg-red-500 text-white text-xs mt-1 py-0.5 rounded">LOW</span>
            @endif
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Feed Type</th>
                    <th class="px-3 py-2 text-right">Qty</th>
                    <th class="px-3 py-2 text-right">Cost</th>
                    <th class="px-3 py-2 text-left">Expiry</th>
                    <th class="px-3 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $stock->feedType->name }}</td>
                    <td class="px-3 py-2 text-right font-medium">{{ $stock->quantity }} <span class="text-gray-500">{{ $stock->unit }}</span></td>
                    <td class="px-3 py-2 text-right">{{ $stock->unit_cost ? '$' . $stock->unit_cost : '-' }}</td>
                    <td class="px-3 py-2">{{ $stock->expiry_date ? $stock->expiry_date->format('d/m/Y') : '-' }}</td>
                    <td class="px-3 py-2">
                        @if($stock->isExpired())
                            <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs">Expired</span>
                        @elseif($stock->isLowStock())
                            <span class="bg-yellow-500 text-white px-2 py-0.5 rounded text-xs">Low</span>
                        @else
                            <span class="bg-green-500 text-white px-2 py-0.5 rounded text-xs">OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">No food stocks found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection