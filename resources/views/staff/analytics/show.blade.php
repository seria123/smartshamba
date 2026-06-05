@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Analytics - {{ $staff->fullName() }}</h1>
            <a href="{{ route('staff.analytics.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $staff->activityLogs()->count() }}</h3>
                        <p class="text-muted">Activity Logs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $staff->tasks()->count() }}</h3>
                        <p class="text-muted">Total Tasks</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $staff->attendances()->count() }}</h3>
                        <p class="text-muted">Attendance Records</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $staff->proofsOfWork()->count() }}</h3>
                        <p class="text-muted">Proofs of Work</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-modern p-4">
            <h5>Recent Activity</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity</th>
                            <th>Title</th>
                            <th>Field</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff->activityLogs->take(20) as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ ucfirst($log->activity_type) }}</td>
                            <td>{{ $log->title }}</td>
                            <td>{{ $log->field->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No activity logs</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
