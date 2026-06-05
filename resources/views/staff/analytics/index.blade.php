@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Staff Analytics Dashboard</h1>
            <form method="GET" class="d-flex gap-2">
                <select name="farm_id" class="form-select-modern" onchange="this.form.submit()">
                    <option value="">All Farms</option>
                    @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" {{ $farmId == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="card-title">{{ $stats['total_staff'] }}</h3>
                        <p class="card-text">Total Active Staff</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="card-title">{{ $stats['active_today'] }}</h3>
                        <p class="card-text">Active Today</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="card-title">{{ $stats['field_assignments'] }}</h3>
                        <p class="card-text">Field Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 class="card-title">{{ $stats['tasks_pending'] }}</h3>
                        <p class="card-text">Pending Tasks</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-modern p-4">
            <h5 class="mb-3">Staff Performance Overview</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Performance Score</th>
                            <th>Attendance Rate</th>
                            <th>Tasks</th>
                            <th>Current Field</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                        <tr>
                            <td>
                                <a href="{{ route('staff.show', $member) }}">{{ $member->fullName() }}</a>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->role_badge_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($member->performance_score)
                                <span class="fw-bold text-{{ $member->performance_score >= 70 ? 'success' : ($member->performance_score >= 50 ? 'warning' : 'danger') }}">
                                    {{ $member->performance_score }}%
                                </span>
                                @else
                                <span class="text-muted">No reviews</span>
                @endif
                            </td>
                            <td>{{ $member->attendance_rate ?? 'N/A' }}%</td>
                            <td>{{ $member->tasks_count }}</td>
                            <td>{{ $member->currentField->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $member->status_color }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No staff found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
