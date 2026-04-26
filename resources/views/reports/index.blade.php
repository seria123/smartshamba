@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                <a href="{{ route('reports.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Generate Report
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label>Farm</label>
                            <select name="farm_id" class="form-control">
                                <option value="">All Farms</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Report Type</label>
                            <select name="report_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="daily_summary" {{ request('report_type') == 'daily_summary' ? 'selected' : '' }}>Daily Summary</option>
                                <option value="weekly_summary" {{ request('report_type') == 'weekly_summary' ? 'selected' : '' }}>Weekly Summary</option>
                                <option value="monthly_summary" {{ request('report_type') == 'monthly_summary' ? 'selected' : '' }}>Monthly Summary</option>
                                <option value="irrigation_report" {{ request('report_type') == 'irrigation_report' ? 'selected' : '' }}>Irrigation Report</option>
                                <option value="sensor_analysis" {{ request('report_type') == 'sensor_analysis' ? 'selected' : '' }}>Sensor Analysis</option>
                                <option value="crop_analysis" {{ request('report_type') == 'crop_analysis' ? 'selected' : '' }}>Crop Analysis</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="generating" {{ request('status') == 'generating' ? 'selected' : '' }}>Generating</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Farm</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th>Generated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ $report->id }}</td>
                                        <td>
                                            <a href="{{ route('reports.show', $report) }}">{{ $report->title }}</a>
                                        </td>
                                        <td>{{ $report->report_type_name }}</td>
                                        <td>{{ $report->farm->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $report->report_period_start->format('M d') }} - {{ $report->report_period_end->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $report->status_color }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($report->status === 'completed')
                                                <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
