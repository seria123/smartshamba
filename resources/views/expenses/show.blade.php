@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Expense Details</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit
                        </a>
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="mb-1">{{ $expense->title ?? $expense->description }}</h3>
                        <div class="text-muted">Expense entry</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Date</strong></td>
                                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type</strong></td>
                                    <td>
                                        @php
                                            $types = $expense->expense_type;
                                            $firstType = is_array($types) ? ($types[0] ?? 'other') : $types;
                                        @endphp
                                        <span class="badge bg-{{ $firstType === 'inputs' ? 'primary' : ($firstType === 'labor' ? 'success' : ($firstType === 'equipment' ? 'info' : 'secondary')) }}">
                                            {{ $expense->type_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Farm</strong></td>
                                    <td>{{ $expense->farm?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Crop</strong></td>
                                    <td>{{ $expense->crop?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Worker/Staff</strong></td>
                                    <td>{{ $expense->staff?->fullName() ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Amount</strong></td>
                                    <td class="text-danger fw-bold fs-5">{{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method</strong></td>
                                    <td>{{ $expense->payment_method ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Receipt Number</strong></td>
                                    <td>{{ $expense->receipt_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created</strong></td>
                                    <td>{{ $expense->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Description</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $expense->description ?: 'No extra description provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($expense->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $expense->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
