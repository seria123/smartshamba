@extends('layouts.MainLayout')

@section('title', 'Budgets')

@section('content')
@php
    $healthClasses = [
        'healthy' => 'bg-emerald-100 text-emerald-800',
        'at_risk' => 'bg-amber-100 text-amber-800',
        'overrun' => 'bg-rose-100 text-rose-800',
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Budgets & Planning</h1>
            <p class="text-sm text-gray-500">Plan seasonal, crop, livestock, and project spending against actual finance activity.</p>
        </div>
        <a href="{{ route('budgets.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"><i class="fas fa-plus mr-1"></i> New Budget</a>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-gray-500">
                        <th class="px-4 py-3">Budget</th>
                        <th class="px-4 py-3">Activity / Owner</th>
                        <th class="px-4 py-3 text-right">Planned</th>
                        <th class="px-4 py-3 text-right">Actual</th>
                        <th class="px-4 py-3 text-right">Variance</th>
                        <th class="px-4 py-3">Health</th>
                        <th class="px-4 py-3">Projection</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($budgets as $budget)
                    @php
                        $actual = $budget->actualAmount();
                        $variance = $budget->variance();
                        $health = $budget->healthStatus();
                        $healthClass = $healthClasses[$health] ?? 'bg-gray-100 text-gray-700';
                        $alerts = $budget->alertMessages();
                    @endphp
                    <tr class="border-b align-top">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $budget->name }}</div>
                            <div class="text-xs text-gray-500">{{ $budget->season_name ?: Str::headline($budget->budget_type) }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $budget->start_date->format('M d, Y') }} - {{ $budget->end_date->format('M d, Y') }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ Str::headline($budget->approval_status ?: 'draft') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-900">
                                {{ $budget->cropCycle?->code ?? $budget->cropCycle?->crop_name ?? $budget->crop?->name ?? $budget->livestock?->tag_number ?? Str::headline($budget->category ?? 'Farm-wide') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $budget->farm?->name }}</div>
                            <div class="text-xs text-gray-500">{{ $budget->owner?->name ?? $budget->owner_group_name ?? Str::headline($budget->responsible_role ?: 'Unassigned') }}</div>
                            @if($budget->loan)
                                <div class="mt-1 text-xs text-emerald-700">Loan: {{ $budget->loan->loan_name ?: $budget->loan->lender_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">KES {{ number_format($budget->planned_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right {{ $budget->isExceeded() ? 'text-rose-700' : 'text-emerald-700' }}">
                            KES {{ number_format($actual, 2) }}
                            <div class="text-xs text-gray-500">{{ number_format($budget->spentPercent(), 1) }}% used</div>
                        </td>
                        <td class="px-4 py-3 text-right {{ $variance > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            KES {{ number_format($variance, 2) }}
                            <div class="text-xs text-gray-500">{{ $variance > 0 ? 'Overspending' : 'Underspending' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $healthClass }}">{{ Str::headline($health) }}</span>
                            <div class="mt-2 text-xs text-gray-500">Score {{ number_format($budget->efficiencyScore(), 0) }}/100</div>
                            @foreach(array_slice($alerts, -2) as $alert)
                                <div class="mt-1 text-xs text-amber-700">{{ $alert }}</div>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-900">KES {{ number_format($budget->projectedTotal(), 2) }}</div>
                            <div class="text-xs text-gray-500">Weekly burn KES {{ number_format($budget->weeklyBurnRate(), 2) }}</div>
                            @if((float) $budget->generated_profit > 0)
                                <div class="text-xs text-emerald-700">ROI {{ number_format($budget->roi(), 1) }}%</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('budgets.edit', $budget) }}" class="text-emerald-700 hover:text-emerald-800">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No budgets created yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $budgets->links() }}</div>
    </div>
</div>
@endsection
