@extends('layouts.MainLayout')

@section('title', 'Finance Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Finance Dashboard</h1>
            <p class="text-sm text-gray-500">Income, expenses, profit, budgets, loans, inventory value, and farm finance insights.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="year" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                @endfor
            </select>
            <a href="{{ route('revenues.create') }}" class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700"><i class="fas fa-plus mr-1"></i> Income</a>
            <a href="{{ route('expenses.create') }}" class="rounded-md bg-rose-600 px-3 py-2 text-sm font-medium text-white hover:bg-rose-700"><i class="fas fa-plus mr-1"></i> Expense</a>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs font-semibold uppercase text-gray-500">Total Income</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">KES {{ number_format($totalIncome, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs font-semibold uppercase text-gray-500">Total Expenses</p>
            <p class="mt-2 text-2xl font-bold text-rose-700">KES {{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs font-semibold uppercase text-gray-500">Net Profit</p>
            <p class="mt-2 text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">KES {{ number_format($netProfit, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs font-semibold uppercase text-gray-500">Inventory Value</p>
            <p class="mt-2 text-2xl font-bold text-sky-700">KES {{ number_format($inventoryValue, 2) }}</p>
        </div>
    </div>

    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Financial Alerts & Insights</h2>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Smart adviser</span>
        </div>
        <div class="space-y-2">
            @foreach($insights as $insight)
                <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-lightbulb mr-2"></i>{{ $insight }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Income Tracking</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500"><th class="py-2">Type</th><th class="py-2 text-right">Amount</th></tr></thead>
                    <tbody>
                    @forelse($incomeByType as $item)
                        <tr class="border-b"><td class="py-2">{{ Str::headline($item->income_type ?? 'other') }}</td><td class="py-2 text-right font-semibold text-emerald-700">KES {{ number_format($item->total, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">No income recorded.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Expense Management</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500"><th class="py-2">Category</th><th class="py-2 text-right">Amount</th></tr></thead>
                    <tbody>
                    @forelse($expenseByCategory as $item)
                        <tr class="border-b"><td class="py-2">{{ Str::headline($item->category ?? 'uncategorized') }}</td><td class="py-2 text-right font-semibold text-rose-700">KES {{ number_format($item->total, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">No expenses recorded.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Profit Per Crop</h2>
            @include('finance.partials.profit-table', ['rows' => $cropProfitability, 'labelResolver' => fn($row) => $row->crop?->name ?? 'Unknown crop'])
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Profit Per Animal</h2>
            @include('finance.partials.profit-table', ['rows' => $livestockProfitability, 'labelResolver' => fn($row) => $row->livestock?->name ?: ($row->livestock?->tag_number ?? 'Unknown animal')])
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Budgets & Planning</h2>
                <a href="{{ route('budgets.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">Manage</a>
            </div>
            @forelse($budgets as $budget)
                @php($actual = $budget->actualSpend())
                <div class="mb-3 rounded-md border border-gray-200 p-3">
                    <div class="flex justify-between gap-3 text-sm"><span class="font-medium text-gray-900">{{ $budget->name }}</span><span>KES {{ number_format($actual, 2) }} / {{ number_format($budget->planned_amount, 2) }}</span></div>
                    <div class="mt-2 h-2 rounded-full bg-gray-100"><div class="h-2 rounded-full {{ $budget->isExceeded() ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $budget->planned_amount > 0 ? ($actual / $budget->planned_amount) * 100 : 0) }}%"></div></div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No budgets yet.</p>
            @endforelse
        </div>

        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Loans & Credit</h2>
                <a href="{{ route('loans.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">Manage</a>
            </div>
            @forelse($loans as $loan)
                <div class="mb-3 rounded-md border border-gray-200 p-3 text-sm">
                    <div class="flex justify-between"><span class="font-medium">{{ $loan->lender_name }}</span><span class="font-semibold text-rose-700">KES {{ number_format($loan->balance(), 2) }}</span></div>
                    <p class="mt-1 text-gray-500">Due {{ $loan->due_date?->format('M d, Y') ?? 'not set' }} @if($loan->isDueSoon())<span class="font-semibold text-amber-700">- due soon</span>@endif</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No active loans.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Feature Coverage</h2>
        <div class="grid gap-3 md:grid-cols-3">
            @foreach([
                'Income linked to crops, animals, buyers, invoices, receipts',
                'Expenses with categories, receipts, recurring schedules',
                'Profit & loss dashboard by year',
                'Seasonal budgets with actual-vs-planned alerts',
                'Payment tracking for paid, partial, and unpaid sales',
                'Inventory stock value from feed costs',
                'Cost of production per crop and animal',
                'Financial alerts and insight messages',
                'Loans, interest, balances, and due reminders',
                'Reports and exports via existing Reports/Data Tools',
                'AI-ready insight layer for forecasting and recommendations',
                'Linked modules: crops, livestock, staff, inventory, harvests',
            ] as $feature)
                <div class="rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700"><i class="fas fa-check text-emerald-600 mr-2"></i>{{ $feature }}</div>
            @endforeach
        </div>
    </div>
</div>
@endsection
