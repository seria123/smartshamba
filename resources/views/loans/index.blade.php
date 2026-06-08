@extends('layouts.MainLayout')

@section('title', 'Loans')

@section('content')
@php
    $riskClasses = [
        'high' => 'bg-rose-100 text-rose-800',
        'medium' => 'bg-amber-100 text-amber-800',
        'low' => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Loans & Credit</h1>
            <p class="text-sm text-gray-500">Track borrowed capital, repayments, loan risk, farm activity links, and ROI.</p>
        </div>
        <a href="{{ route('loans.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"><i class="fas fa-plus mr-1"></i> New Loan</a>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left text-gray-500">
                        <th class="px-4 py-3">Loan</th>
                        <th class="px-4 py-3">Farm Link</th>
                        <th class="px-4 py-3 text-right">Balance</th>
                        <th class="px-4 py-3">Risk</th>
                        <th class="px-4 py-3 text-right">ROI</th>
                        <th class="px-4 py-3">Next Due</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($loans as $loan)
                    @php
                        $nextInstallment = $loan->nextInstallment();
                        $riskClass = $riskClasses[$loan->risk_level] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <tr class="border-b align-top">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $loan->loan_name ?: $loan->lender_name }}</div>
                            <div class="text-xs text-gray-500">{{ $loan->lender_name }} @if($loan->lender_type) · {{ Str::headline($loan->lender_type) }} @endif</div>
                            <div class="mt-1 text-xs text-gray-500">{{ Str::headline($loan->purpose_tag ?: 'general loan') }} · {{ Str::headline($loan->interest_method ?: 'flat') }} · {{ $loan->interest_rate }}%</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-900">{{ $loan->farm?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">
                                @if($loan->cropCycle)
                                    Crop cycle #{{ $loan->cropCycle->id }}
                                @elseif($loan->livestock)
                                    {{ $loan->livestock->tag_number }} {{ $loan->livestock->name ? '- '.$loan->livestock->name : '' }}
                                @else
                                    No activity linked
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="font-semibold text-rose-700">KES {{ number_format($loan->balance(), 2) }}</div>
                            <div class="text-xs text-gray-500">Principal KES {{ number_format($loan->principal_amount, 2) }}</div>
                            <div class="text-xs text-emerald-700">Paid KES {{ number_format($loan->amount_repaid, 2) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $riskClass }}">{{ Str::headline($loan->risk_level ?: 'not assessed') }}</span>
                            <div class="mt-2 max-w-xs text-xs text-gray-500">{{ $loan->risk_notes ?: 'Add income snapshot to calculate repayment pressure.' }}</div>
                            @if($loan->debtToIncomeRatio() > 0)
                                <div class="mt-1 text-xs text-gray-500">{{ number_format($loan->debtToIncomeRatio(), 1) }}% debt-to-income</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="font-medium {{ $loan->roi() >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($loan->roi(), 1) }}%</div>
                            @if((float) $loan->interest_saved_estimate > 0)
                                <div class="text-xs text-gray-500">Save KES {{ number_format($loan->interest_saved_estimate, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $nextInstallment['due_date'] ?? $loan->due_date?->format('M d, Y') ?? '-' }}</div>
                            @if($nextInstallment)
                                <div class="text-xs text-gray-500">KES {{ number_format($nextInstallment['total'], 2) }}</div>
                            @endif
                            @if($loan->isDueSoon())
                                <span class="mt-1 inline-block rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-800">Due soon</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('loans.edit', $loan) }}" class="text-emerald-700 hover:text-emerald-800">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No loans recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $loans->links() }}</div>
    </div>
</div>
@endsection
