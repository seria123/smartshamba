@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Staff Details</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $staff->fullName() }}</h4>
                        <span class="badge bg-{{ $staff->status_color }} mt-2">
                            {{ ucfirst($staff->status) }}
                        </span>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Role</span>
                            <span class="badge bg-{{ $staff->role_badge_color }}">{{ ucfirst(str_replace('_', ' ', $staff->role)) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Email</span>
                            <span>{{ $staff->email ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Phone</span>
                            <span>{{ $staff->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">National ID</span>
                            <span>{{ $staff->national_id ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Employee ID</span>
                            <span>{{ $staff->employee_id ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Daily Wage</span>
                            <span class="fw-semibold text-success">{{ number_format($staff->daily_wage, 2) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Payment Type</span>
                            <span>{{ ucfirst($staff->payment_type) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Hire Date</span>
                            <span>{{ $staff->hire_date->format('M d, Y') }}</span>
                        </div>
                        @if($staff->termination_date)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Termination Date</span>
                            <span>{{ $staff->termination_date->format('M d, Y') }}</span>
                        </div>
                        @endif

                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Performance</span>
                            <span class="fw-bold text-{{ $staff->performance_score >= 70 ? 'success' : ($staff->performance_score >= 50 ? 'warning' : 'danger') }}">
                                {{ $staff->performance_score ?? 'N/A' }}%
                            </span>
                        </div>

                        @if($staff->emergency_contact)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Emergency Contact</span>
                            <span>{{ $staff->emergency_contact }} ({{ $staff->emergency_phone ?? 'N/A' }})</span>
                        </div>
                        @endif

                        <div class="p-3 d-flex flex-wrap gap-2">
                            <a href="{{ route('staff.edit', $staff) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i> Edit
                            </a>
                            <a href="{{ route('staff.attendance', $staff) }}" class="btn btn-info text-white">
                                <i class="fas fa-calendar-check me-2"></i> Attendance
                            </a>
                            <a href="{{ route('staff.wages', $staff) }}" class="btn btn-success">
                                <i class="fas fa-dollar-sign me-2"></i> Wages
                            </a>
                            <a href="{{ route('staff.tasks', $staff) }}" class="btn btn-warning text-white">
                                <i class="fas fa-tasks me-2"></i> Tasks
                            </a>
                        </div>
                        <div class="p-3 d-flex flex-wrap gap-2 border-t">
                            <a href="{{ route('staff.field_assignments.index', $staff) }}" class="btn btn-outline-success">
                                <i class="fas fa-map me-1"></i> Fields
                            </a>
                            <a href="{{ route('staff.skills.index', $staff) }}" class="btn btn-outline-info">
                                <i class="fas fa-tools me-1"></i> Skills
                            </a>
                            <a href="{{ route('staff.performance.index', $staff) }}" class="btn btn-outline-warning">
                                <i class="fas fa-star me-1"></i> Performance
                            </a>
                            <a href="{{ route('staff.schedules.index', $staff) }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar me-1"></i> Schedule
                            </a>
                            <a href="{{ route('staff.proofs.index', $staff) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-camera me-1"></i> Proofs
                            </a>
                        </div>
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
                    @if($staff->attendances->count() > 0)
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
                                @foreach($staff->attendances->take(10) as $attendance)
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

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Fields</h5>
                </div>
                <div class="card-body">
                    @forelse($staff->fieldAssignments->take(5) as $assignment)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <strong>{{ $assignment->field->name ?? 'Unknown Field' }}</strong>
                            @if($assignment->is_primary)
                            <span class="badge bg-warning ms-1">Primary</span>
                            @endif
                        </div>
                        <small class="text-muted">Since {{ $assignment->assigned_date->format('M d, Y') }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No field assignments</p>
                    @endforelse
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Skills</h5>
                </div>
                <div class="card-body">
                    @if($staff->skills->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($staff->skills as $skill)
                        <span class="badge bg-{{ $skill->proficiency_level === 'expert' ? 'success' : ($skill->proficiency_level === 'advanced' ? 'primary' : 'info') }}">
                            {{ $skill->skill_name }} ({{ ucfirst($skill->proficiency_level) }})
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No skills recorded</p>
                    @endif
                </div>
            </div>

            @if($staff->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $staff->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
