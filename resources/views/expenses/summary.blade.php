@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Profit & Loss Statement - {{ $year }}</h4>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex gap-2">
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </form>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i> All Expenses
                        </a>
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Add Expense
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">KES {{ number_format($totalRevenue, 2) }}</h3>
                                    <small>Total Revenue</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">KES {{ number_format($totalExpenses, 2) }}</h3>
                                    <small>Total Expenses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $netProfit >= 0 ? 'bg-emerald-600' : 'bg-rose-600' }} text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">KES {{ number_format($netProfit, 2) }}</h3>
                                    <small>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Profit/Loss Chart -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Monthly Profit/Loss - {{ $year }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th class="text-end">Revenue</th>
                                                    <th class="text-end">Expenses</th>
                                                    <th class="text-end">Profit/Loss</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($m = 1; $m <= 12; $m++)
                                                @php
                                                    $monthRevenue = $monthlyRevenue->get($m, 0);
                                                    $monthExpense = $monthlyExpenses->get($m, 0);
                                                    $monthProfit = $monthRevenue - $monthExpense;
                                                @endphp
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}</td>
                                                    <td class="text-end text-success">{{ number_format($monthRevenue, 2) }}</td>
                                                    <td class="text-end text-danger">{{ number_format($monthExpense, 2) }}</td>
                                                    <td class="text-end {{ $monthProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} fw-bold">
                                                        KES {{ number_format($monthProfit, 2) }}
                                                    </td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                            <tfoot class="table-dark">
                                                <tr>
                                                    <th>Total</th>
                                                    <th class="text-end text-success">KES {{ number_format($totalRevenue, 2) }}</th>
                                                    <th class="text-end text-danger">KES {{ number_format($totalExpenses, 2) }}</th>
                                                    <th class="text-end {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} fw-bold">
                                                        KES {{ number_format($netProfit, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses by Type -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Expenses by Type</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($typeExpenses as $type)
                                                <tr>
                                                    <td>
                                                        {{ match($type->expense_type) {
                                                            'inputs' => 'Inputs',
                                                            'labor' => 'Labor',
                                                            'equipment' => 'Equipment',
                                                            'fertilizer' => 'Fertilizer',
                                                            'seeds' => 'Seeds',
                                                            'pesticides' => 'Pesticides',
                                                            'animal_feed' => 'Animal Feed',
                                                            'veterinary' => 'Veterinary',
                                                            'fuel' => 'Fuel',
                                                            'maintenance' => 'Maintenance',
                                                            'transport' => 'Transport',
                                                            'utilities' => 'Utilities',
                                                            'other' => 'Other',
                                                            default => $type->expense_type
                                                        } }}
                                                    </td>
                                                    <td class="text-end text-danger">{{ number_format($type->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue by Crop -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Revenue by Crop</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Crop</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($revenueByCrop as $revenue)
                                                <tr>
                                                    <td>{{ $revenue->crop->name ?? 'Unknown' }}</td>
                                                    <td class="text-end text-success">{{ number_format($revenue->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                @if($revenueByCrop->isEmpty())
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">No revenue recorded for this year.</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
