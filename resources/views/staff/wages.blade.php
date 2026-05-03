@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Wages: {{ $staff->fullName() }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('staff.show', $staff) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWageModal">
                            <i class="fas fa-plus me-2"></i> Create Wage Record
                        </button>
                    </div>
                </div>
<div class="card-body">
                     <div class="row mb-4">
                         <div class="col-md-4">
                             <div class="card">
                                 <div class="card-body text-center">
                                     <h5 class="text-muted mb-2">Daily Wage</h5>
                                     <h3 class="text-success mb-0">{{ number_format($staff->daily_wage, 2) }}</h3>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="card">
                                 <div class="card-body text-center">
                                     <h5 class="text-muted mb-2">Payment Type</h5>
                                     <h3 class="mb-0">{{ ucfirst($staff->payment_type) }}</h3>
                                 </div>
                             </div>
                         </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="text-muted mb-2">Total Paid</h5>
                                    <h3 class="text-success mb-0">
                                        {{ number_format($wages->where('status', 'paid')->sum('net_wage'), 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Days Worked</th>
                                    <th>Hours</th>
                                    <th>Daily Rate</th>
                                    <th>Gross Wage</th>
                                    <th>Deductions</th>
                                    <th>Net Wage</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wages as $wage)
                                <tr>
                                    <td>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#wageDetailsModal{{ $wage->id }}">
                                            {{ $wage->period }}
                                        </a>
                                    </td>
                                    <td>{{ $wage->days_worked }}</td>
                                    <td>{{ $wage->hours_worked }}</td>
                                    <td>{{ number_format($wage->daily_rate, 2) }}</td>
                                    <td>{{ number_format($wage->gross_wage, 2) }}</td>
                                    <td>{{ number_format($wage->deductions, 2) }}</td>
                                    <td class="fw-bold text-success">{{ number_format($wage->net_wage, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $wage->status_color }}">
                                            {{ ucfirst($wage->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $wage->payment_date?->format('M d, Y') ?? '-' }}</td>
                                    <td>
                                        @if($wage->status !== 'paid')
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $wage->id }}">
                                            <i class="fas fa-check"></i> Mark Paid
                                        </button>
                                        @endif
                                    </td>
                                </tr>

                                <div class="modal fade" id="markPaidModal{{ $wage->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mark Wage as Paid</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('staff.wages.update', $wage) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="paid">
                                                <div class="modal-body">
                                                    <p>Mark this wage record of <strong>{{ number_format($wage->net_wage, 2) }}</strong> as paid?</p>
                                                    <div class="mb-3">
                                                        <label for="payment_method" class="form-label">Payment Method</label>
                                                        <select class="form-select" name="payment_method" required>
                                                            <option value="cash">Cash</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="mobile_money">Mobile Money</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Confirm Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        No wage records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $wages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createWageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Wage Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('staff.wages.store', $staff) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Create a wage record based on attendance for a specific period.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="period_start" class="form-label">Period Start *</label>
                                <input type="date" class="form-control" id="period_start" name="period_start" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="period_end" class="form-label">Period End *</label>
                                <input type="date" class="form-control" id="period_end" name="period_end" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
