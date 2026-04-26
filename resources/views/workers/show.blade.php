@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Worker Details</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $worker->fullName() }}</h4>
                        <span class="badge bg-{{ $worker->status === 'active' ? 'success' : ($worker->status === 'inactive' ? 'warning' : 'secondary') }} mt-2">
                            {{ ucfirst($worker->status) }}
                        </span>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Role</span>
                            <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $worker->role)) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Phone</span>
                            <span>{{ $worker->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">National ID</span>
                            <span>{{ $worker->national_id ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Daily Wage</span>
                            <span class="fw-semibold text-success">{{ number_format($worker->daily_wage, 2) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Payment Type</span>
                            <span>{{ ucfirst($worker->payment_type) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Hire Date</span>
                            <span>{{ $worker->hire_date->format('M d, Y') }}</span>
                        </div>
                        @if($worker->termination_date)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Termination Date</span>
                            <span>{{ $worker->termination_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($worker->emergency_contact)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Emergency Contact</span>
                            <span>{{ $worker->emergency_contact }} ({{ $worker->emergency_phone ?? 'N/A' }})</span>
                        </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit Worker
                        </a>
                        <a href="{{ route('workers.attendance', $worker) }}" class="btn btn-info text-white">
                            <i class="fas fa-calendar-check me-2"></i> View Attendance
                        </a>
                        <a href="{{ route('workers.wages', $worker) }}" class="btn btn-success">
                            <i class="fas fa-dollar-sign me-2"></i> View Wages
                        </a>
                        <a href="{{ route('workers.tasks', $worker) }}" class="btn btn-warning">
                            <i class="fas fa-tasks me-2"></i> Assigned Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Attendance</h5>
                </div>
                <div class="card-body">
                    @if($worker->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($worker->attendances->take(10) as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status_color }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->clock_in ?? '-' }}</td>
                                    <td>{{ $attendance->clock_out ?? '-' }}</td>
                                    <td>{{ $attendance->hours_worked }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No attendance records found</p>
                    @endif
                </div>
            </div>

            @if($worker->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $worker->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
