@extends('layouts.MainLayout')

@section('title', 'Food Types - SmartShamba')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Food Types</h1>
            <p class="text-sm text-gray-500">Central hub for pricing, stock, source linkage, production cost, quality, demand, and profitability.</p>
        </div>
        <a href="{{ route('feed-types.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
            <i class="fas fa-plus mr-2"></i>Add Food Type
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.card class="text-center"><p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-sm text-gray-500">Food types</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold text-rose-700">{{ $stats['low_stock'] }}</p><p class="text-sm text-gray-500">Low stock alerts</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold text-amber-700">{{ $stats['price_alerts'] }}</p><p class="text-sm text-gray-500">Price below cost</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold text-sky-700">{{ $stats['expiry_alerts'] }}</p><p class="text-sm text-gray-500">Expiry warnings</p></x-ui.card>
    </div>

    <x-ui.card>
        <form method="GET" class="grid gap-3 md:grid-cols-3">
            <select name="category" class="rounded-lg border-gray-300">
                <option value="">All categories</option>
                @foreach(['crop_based' => 'Crop-based', 'animal_based' => 'Animal-based', 'processed' => 'Processed'] as $key => $label)
                    <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="demand_level" class="rounded-lg border-gray-300">
                <option value="">All demand levels</option>
                @foreach(['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $key => $label)
                    <option value="{{ $key }}" @selected(request('demand_level') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white">Filter</button>
        </form>
    </x-ui.card>

    <div class="grid gap-4">
        @forelse($feedTypes as $feedType)
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex gap-4">
                        @if($feedType->image_path)
                            <img src="{{ asset('storage/'.$feedType->image_path) }}" alt="{{ $feedType->name }}" class="h-20 w-20 rounded-lg object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"><i class="fas fa-utensils text-2xl"></i></div>
                        @endif
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">{{ $feedType->name }}</h2>
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">{{ Str::headline($feedType->category) }}</span>
                                @if($feedType->quality_grade)<span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800">Grade {{ $feedType->quality_grade }}</span>@endif
                                @if($feedType->demand_level)<span class="rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-800">{{ ucfirst($feedType->demand_level) }} demand</span>@endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ Str::limit($feedType->description, 120) }}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600">
                                <span><i class="fas fa-boxes mr-1"></i>{{ number_format($feedType->totalQuantity(), 2) }} {{ $feedType->default_unit }}</span>
                                <span><i class="fas fa-link mr-1"></i>{{ $feedType->linkedCrop?->name ?? $feedType->linkedLivestock?->tag_number ?? $feedType->supplier?->name ?? 'No source linked' }}</span>
                                @if($feedType->batch_number)<span><i class="fas fa-barcode mr-1"></i>{{ $feedType->batch_number }}</span>@endif
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 text-sm sm:grid-cols-4 lg:w-[560px]">
                        <div><p class="text-gray-500">Selling</p><p class="font-semibold">KES {{ number_format($feedType->selling_price ?? 0, 2) }}</p></div>
                        <div><p class="text-gray-500">Cost/unit</p><p class="font-semibold">KES {{ number_format($feedType->costPerUnit() ?: $feedType->averageUnitCost(), 2) }}</p></div>
                        <div><p class="text-gray-500">Margin</p><p class="font-semibold {{ $feedType->profitMargin() >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($feedType->profitMargin(), 1) }}%</p></div>
                        <div><p class="text-gray-500">Alerts</p>
                            <div class="space-y-1">
                                @if($feedType->isBelowThreshold())<span class="block text-rose-700">Low stock</span>@endif
                                @if($feedType->priceBelowCost())<span class="block text-amber-700">Price below cost</span>@endif
                                @if($feedType->expiryAlert())<span class="block text-orange-700">{{ $feedType->expiryAlert() }}</span>@endif
                                @if(! $feedType->isBelowThreshold() && ! $feedType->priceBelowCost() && ! $feedType->expiryAlert())<span class="text-emerald-700">OK</span>@endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 border-t pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600"><i class="fas fa-robot text-emerald-600 mr-2"></i>{{ $feedType->recommendation() }}</p>
                    <div class="flex gap-3">
                        <a href="{{ route('feed-types.show', $feedType) }}" class="text-emerald-700 hover:text-emerald-800">Details</a>
                        <a href="{{ route('feed-types.edit', $feedType) }}" class="text-blue-700 hover:text-blue-800">Edit</a>
                        <form action="{{ route('feed-types.destroy', $feedType) }}" method="POST" onsubmit="return confirm('Delete this food type?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-700 hover:text-rose-800">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <x-ui.card class="text-center text-gray-500">No food types found.</x-ui.card>
        @endforelse
    </div>
</div>
@endsection
