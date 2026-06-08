@extends('layouts.MainLayout')

@section('title', 'Feed Usage - SmartShamba')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Feed Usage</h1>
            <p class="text-sm text-gray-500">Track animal feeding, cost, production output, FCR, inventory deduction, quality, staff, and anomalies.</p>
        </div>
        <a href="{{ route('feed_usages.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
            <i class="fas fa-plus mr-2"></i>Record Feeding
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.card class="text-center"><p class="text-2xl font-bold">KES {{ number_format($stats['monthly_cost'], 2) }}</p><p class="text-sm text-gray-500">Monthly feed expense</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold">{{ number_format($stats['weekly_usage'], 2) }}</p><p class="text-sm text-gray-500">This week usage</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold {{ ($stats['weekly_trend'] ?? 0) > 30 ? 'text-amber-700' : 'text-gray-900' }}">{{ $stats['weekly_trend'] !== null ? number_format($stats['weekly_trend'], 1).'%' : '-' }}</p><p class="text-sm text-gray-500">Weekly trend</p></x-ui.card>
        <x-ui.card class="text-center"><p class="text-2xl font-bold text-rose-700">{{ $stats['anomalies'] }}</p><p class="text-sm text-gray-500">Anomalies</p></x-ui.card>
    </div>

    <x-ui.card>
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <select name="feed_type_id" class="rounded-lg border-gray-300">
                <option value="">All feeds</option>
                @foreach($feedTypes as $feedType)
                    <option value="{{ $feedType->id }}" @selected(request('feed_type_id') == $feedType->id)>{{ $feedType->name }}</option>
                @endforeach
            </select>
            <select name="feeding_frequency" class="rounded-lg border-gray-300">
                <option value="">All frequencies</option>
                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'custom' => 'Custom'] as $key => $label)
                    <option value="{{ $key }}" @selected(request('feeding_frequency') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="anomaly_status" class="rounded-lg border-gray-300">
                <option value="">All anomaly states</option>
                @foreach(['normal' => 'Normal', 'spike' => 'Spike', 'drop' => 'Drop', 'quality_issue' => 'Quality issue'] as $key => $label)
                    <option value="{{ $key }}" @selected(request('anomaly_status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white">Filter</button>
        </form>
    </x-ui.card>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Feed</th>
                        <th class="px-4 py-3">Animal / Group</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Schedule</th>
                        <th class="px-4 py-3 text-right">Cost</th>
                        <th class="px-4 py-3">Output / FCR</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Insight</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($feedUsages as $usage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $usage->feedType?->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::headline($usage->feedType?->category ?? 'feed') }} {{ $usage->batch_number ? '- Batch '.$usage->batch_number : '' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $usage->assignmentLabel() }}</td>
                            <td class="px-4 py-3">{{ number_format($usage->quantity, 2) }} {{ $usage->unit }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $usage->usage_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ Str::headline($usage->feeding_frequency) }} {{ $usage->feeding_time ? '- '.Str::headline($usage->feeding_time) : '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">KES {{ number_format($usage->totalCost(), 2) }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $usage->output_quantity ? number_format($usage->output_quantity, 2).' '.$usage->output_unit : '-' }}</div>
                                <div class="text-xs text-gray-500">FCR {{ $usage->feed_conversion_ratio ? number_format($usage->feed_conversion_ratio, 2) : '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($usage->inventory_deducted)
                                    <span class="text-emerald-700">{{ number_format($usage->stock_after ?? 0, 2) }} left</span>
                                @else
                                    <span class="text-gray-500">Not deducted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $usage->anomaly_status === 'normal' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                                    {{ Str::headline($usage->anomaly_status ?? 'normal') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('feed_usages.show', $usage) }}" class="text-emerald-700 hover:text-emerald-800">View</a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('feed_usages.edit', $usage) }}" class="text-blue-700 hover:text-blue-800">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">No feed usage records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $feedUsages->links() }}</div>
    </div>
</div>
@endsection
