@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Performance Reviews - {{ $staff->fullName() }}</h1>
            <a href="{{ route('staff.performance.create', $staff) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Review
            </a>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Performance Overview</h5>
                    </div>
                    <div class="card-body">
                        @if($staff->performanceReviews->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Tasks (Assigned/Completed)</th>
                                        <th>Attendance Rate</th>
                                        <th>Quality Score</th>
                                        <th>Efficiency</th>
                                        <th>Overall Score</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff->performanceReviews as $review)
                                    <tr>
                                        <td>{{ $review->review_period_start->format('M Y') }} - {{ $review->review_period_end->format('M Y') }}</td>
                                        <td>{{ $review->tasks_completed }} / {{ $review->tasks_assigned }}</td>
                                        <td>{{ $review->attendance_rate }}%</td>
                                        <td>{{ $review->work_quality_score ?? 'N/A' }}</td>
                                        <td>{{ $review->efficiency_rating ?? 'N/A' }}</td>
                                        <td>
                                            <span class="fw-bold text-{{ $review->overall_score >= 70 ? 'success' : ($review->overall_score >= 50 ? 'warning' : 'danger') }}">
                                                {{ $review->overall_score }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $review->rating === 'excellent' ? 'success' : ($review->rating === 'good' ? 'primary' : ($review->rating === 'average' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $review->rating)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted text-center py-4">No performance reviews yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
