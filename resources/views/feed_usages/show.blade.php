@extends('layouts.MainLayout')

@section('title', 'Feed Usage Details - SmartShamba')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $feedUsage->feedType?->name }} Feeding</h1>
            <p class="text-sm text-gray-500">{{ $feedUsage->assignmentLabel() }} - {{ $feedUsage->usage_date->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('feed_usages.index') }}" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50">Back</a>
            <a href="{{ route('feed_usages.edit', $feedUsage) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Edit</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.card><p class="text-sm text-gray-500">Feed Intake</p><p class="text-xl font-bold">{{ number_format($feedUsage->quantity, 2) }} {{ $feedUsage->unit }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">Total Cost</p><p class="text-xl font-bold">KES {{ number_format($feedUsage->totalCost(), 2) }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">Output</p><p class="text-xl font-bold">{{ $feedUsage->output_quantity ? number_format($feedUsage->output_quantity, 2).' '.$feedUsage->output_unit : '-' }}</p></x-ui.card>
        <x-ui.card><p class="text-sm text-gray-500">FCR</p><p class="text-xl font-bold">{{ $feedUsage->feed_conversion_ratio ? number_format($feedUsage->feed_conversion_ratio, 2) : '-' }}</p></x-ui.card>
    </div>

    <x-ui.card>
        <h2 class="mb-3 text-lg font-semibold text-gray-900">AI Feed Optimization</h2>
        <p class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-900"><i class="fas fa-robot mr-2"></i>{{ $feedUsage->ai_recommendation }}</p>
        <p class="mt-2 rounded-lg bg-sky-50 p-3 text-sm text-sky-900"><i class="fas fa-balance-scale mr-2"></i>{{ $feedUsage->nutritionalBalanceSummary() }}</p>
    </x-ui.card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Schedule, Staff & Inventory</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Frequency</dt><dd>{{ Str::headline($feedUsage->feeding_frequency) }}</dd></div>
                <div><dt class="text-gray-500">Feeding time</dt><dd>{{ Str::headline($feedUsage->feeding_time ?? 'not set') }}</dd></div>
                <div><dt class="text-gray-500">Reminder</dt><dd>{{ $feedUsage->reminder_at?->format('M d, Y H:i') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Staff</dt><dd>{{ $feedUsage->staff?->fullName() ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Stock before</dt><dd>{{ $feedUsage->stock_before ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Stock after</dt><dd>{{ $feedUsage->stock_after ?? '-' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card>
            <h2 class="mb-4 text-lg font-semibold">Performance & Trends</h2>
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-gray-500">Output type</dt><dd>{{ Str::headline($feedUsage->output_type ?? 'none') }}</dd></div>
                <div><dt class="text-gray-500">Weight gain</dt><dd>{{ $feedUsage->weight_gain ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Output efficiency</dt><dd>{{ number_format($feedUsage->outputEfficiency(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Trend change</dt><dd>{{ $feedUsage->trend_change_percent !== null ? number_format($feedUsage->trend_change_percent, 1).'%' : '-' }}</dd></div>
                <div><dt class="text-gray-500">Anomaly</dt><dd>{{ Str::headline($feedUsage->anomaly_status ?? 'normal') }}</dd></div>
                <div><dt class="text-gray-500">Season / Stage</dt><dd>{{ Str::headline($feedUsage->season ?? '-') }} / {{ $feedUsage->production_stage ?? '-' }}</dd></div>
            </dl>
        </x-ui.card>
    </div>

    <x-ui.card>
        <h2 class="mb-4 text-lg font-semibold">Batch, Supplier & Feed Quality</h2>
        <div class="grid gap-4 md:grid-cols-3 text-sm">
            <div><p class="text-gray-500">Batch</p><p class="font-semibold">{{ $feedUsage->batch_number ?? '-' }}</p></div>
            <div><p class="text-gray-500">Supplier</p><p class="font-semibold">{{ $feedUsage->supplier?->name ?? '-' }}</p></div>
            <div><p class="text-gray-500">Spoilage</p><p class="font-semibold">{{ Str::headline($feedUsage->spoilage_status ?? 'none') }}</p></div>
        </div>
        @if($feedUsage->quality_image_path)
            <img src="{{ asset('storage/'.$feedUsage->quality_image_path) }}" class="mt-4 max-h-64 rounded-lg object-cover" alt="Feed quality">
        @endif
        @if($feedUsage->quality_notes)
            <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">{{ $feedUsage->quality_notes }}</p>
        @endif
        @if($feedUsage->notes)
            <p class="mt-3 text-sm text-gray-600">{{ $feedUsage->notes }}</p>
        @endif
    </x-ui.card>
</div>
@endsection
