@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Expenses Summary</h4>
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
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($typeExpenses->sum('total'), 2) }}</h3>
                                    <small>Total Expenses ({{ $year }})</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $typeExpenses->count() }}</h3>
                                    <small>Expense Categories</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($typeExpenses->avg('total'), 2) }}</h3>
                                    <small>Average per Category</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Monthly Expenses - {{ $year }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($m = 1; $m <= 12; $m++)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}</td>
                                                    <td class="text-end {{ $monthlyExpenses->get($m) ? 'text-danger fw-bold' : 'text-muted' }}">
                                                        {{ number_format($monthlyExpenses->get($m, 0), 2) }}
                                                    </td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                                            'fuel' => 'Fuel',
                                                            'maintenance' => 'Maintenance',
                                                            'transport' => 'Transport',
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Percentage by Type</h5>
                                </div>
                                <div class="card-body">
                                    @php $total = $typeExpenses->sum('total'); @endphp
                                    @foreach($typeExpenses as $type)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>{{ match($type->expense_type) {
                                                'inputs' => 'Inputs',
                                                'labor' => 'Labor',
                                                'equipment' => 'Equipment',
                                                'fertilizer' => 'Fertilizer',
                                                'seeds' => 'Seeds',
                                                'pesticides' => 'Pesticides',
                                                'fuel' => 'Fuel',
                                                'maintenance' => 'Maintenance',
                                                'transport' => 'Transport',
                                                'other' => 'Other',
                                                default => $type->expense_type
                                            } }}</span>
                                            <span class="fw-bold">{{ number_format(($type->total / $total) * 100, 1) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($type->total / $total) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @if($total == 0)
                                    <p class="text-muted text-center">No expenses recorded for this year.</p>
                                    @endif
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
