@extends('layouts.MainLayout')

@section('title', $feedType->name.' - SmartShamba')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $feedType->name }}</h1>
            <p class="text-sm text-gray-500">{{ $feedType->description ?? 'Food type details' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('feed-types.index') }}" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50">Back</a>
            <a href="{{ route('feed-types.edit', $feedType) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Edit</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.card><p class="text-sm text-gray-500">Stock</p><p class="text-xl font-bold">{{ number_format($feedType->totalQuantity(), 2) }} {{ $feedType->default_unit }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">Stock Value</p><p class="text-xl font-bold">KES {{ number_format($feedType->stockValue(), 2) }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">Cost / Unit</p><p class="text-xl font-bold">KES {{ number_format($feedType->costPerUnit() ?: $feedType->averageUnitCost(), 2) }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">Profit Margin</p><p class="text-xl font-bold {{ $feedType->profitMargin() >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($feedType->profitMargin(), 1) }}%</p></x-ui.card>
    </div>

    <x-ui.card>
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Smart Recommendations & Alerts</h2>
        <div class="space-y-2 text-sm">
            <p class="rounded-lg bg-emerald-50 p-3 text-emerald-900"><i class="fas fa-robot mr-2"></i>{{ $feedType->recommendation() }}</p>
            @if($feedType->isBelowThreshold())<p class="rounded-lg bg-rose-50 p-3 text-rose-800">Low stock: below {{ $feedType->min_threshold }} {{ $feedType->default_unit }}.</p>@endif
            @if($feedType->priceBelowCost())<p class="rounded-lg bg-amber-50 p-3 text-amber-900">Price drop alert: market or selling price is below production cost.</p>@endif
            @if($feedType->expiryAlert())<p class="rounded-lg bg-orange-50 p-3 text-orange-900">{{ $feedType->expiryAlert() }}: expires {{ $feedType->expiresAt()?->format('M d, Y') }}.</p>@endif
        </div>
    </x-ui.card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Identification & Source</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Category</dt><dd>{{ Str::headline($feedType->category) }}</dd></div>
                <div><dt class="text-gray-500">Sub-category</dt><dd>{{ $feedType->sub_category ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Linked crop</dt><dd>{{ $feedType->linkedCrop?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Linked livestock</dt><dd>{{ $feedType->linkedLivestock?->tag_number ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Supplier</dt><dd>{{ $feedType->supplier?->name ?? 'Farm produced' }}</dd></div>
                <div><dt class="text-gray-500">Conversion</dt><dd>{{ $feedType->unit_conversion_label ?? '-' }} {{ $feedType->unit_conversion_factor ? '= '.$feedType->unit_conversion_factor.' '.$feedType->default_unit : '' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Pricing & Market</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Selling price</dt><dd>KES {{ number_format($feedType->selling_price ?? 0, 2) }}</dd></div>
                <div><dt class="text-gray-500">Minimum price</dt><dd>KES {{ number_format($feedType->minimum_price ?? 0, 2) }}</dd></div>
                <div><dt class="text-gray-500">Market price</dt><dd>KES {{ number_format($feedType->market_price ?? 0, 2) }}</dd></div>
                <div><dt class="text-gray-500">Demand</dt><dd>{{ ucfirst($feedType->demand_level ?? 'unknown') }}</dd></div>
                <div><dt class="text-gray-500">Best periods</dt><dd>{{ implode(', ', $feedType->best_selling_periods ?? []) ?: '-' }}</dd></div>
                <div><dt class="text-gray-500">Regions</dt><dd>{{ implode(', ', $feedType->market_regions ?? []) ?: '-' }}</dd></div>
            </dl>
        </x-ui.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Nutrition & Shelf Life</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Protein</dt><dd>{{ $feedType->protein ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Carbohydrates</dt><dd>{{ $feedType->carbohydrates ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Fats</dt><dd>{{ $feedType->fats ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Calories</dt><dd>{{ $feedType->energy_calories ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Vitamins</dt><dd>{{ implode(', ', $feedType->vitamins ?? []) ?: '-' }}</dd></div>
                <div><dt class="text-gray-500">Storage</dt><dd>{{ implode(', ', array_map(fn($v) => Str::headline($v), $feedType->storage_conditions ?? [])) ?: '-' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Processing & Traceability</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Transformation</dt><dd>{{ $feedType->raw_input_name ?? '-' }} {{ $feedType->processed_output_name ? '-> '.$feedType->processed_output_name : '' }}</dd></div>
                <div><dt class="text-gray-500">Processing cost</dt><dd>KES {{ number_format($feedType->processing_cost ?? 0, 2) }}</dd></div>
                <div><dt class="text-gray-500">Yield ratio</dt><dd>{{ $feedType->yield_ratio ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Batch</dt><dd>{{ $feedType->batch_number ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Production date</dt><dd>{{ $feedType->production_date?->format('M d, Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Source batch</dt><dd>{{ $feedType->source_batch ?? '-' }}</dd></div>
            </dl>
        </x-ui.card>
    </div>

    <x-ui.card>
        <h2 class="mb-4 text-lg font-semibold">Quality, Standards & Notes</h2>
        <div class="grid gap-4 md:grid-cols-3 text-sm">
            <div><p class="text-gray-500">Quality grade</p><p class="font-semibold">{{ $feedType->quality_grade ? 'Grade '.$feedType->quality_grade : '-' }}</p></div>
            <div><p class="text-gray-500">Certifications</p><p class="font-semibold">{{ implode(', ', array_map(fn($v) => Str::headline($v), $feedType->certifications ?? [])) ?: '-' }}</p></div>
            <div><p class="text-gray-500">Buyer preferences</p><p>{{ $feedType->buyer_preferences ?? '-' }}</p></div>
        </div>
        @if($feedType->user_notes)
            <div class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">{{ $feedType->user_notes }}</div>
        @endif
    </x-ui.card>
</div>
@endsection
