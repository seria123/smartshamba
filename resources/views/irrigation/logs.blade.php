@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-history"></i> Irrigation Logs</h1>
                <div>
                    @if(auth()->user()->role !== 'user')
                        <a href="{{ route('irrigation.index') }}" class="btn btn-info me-2">
                            <i class="fas fa-tint"></i> Manage Zones
                        </a>
                    @endif
                    <a href="{{ route('irrigation.logs') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Zones
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label>Zone</label>
                            <select name="zone_id" class="form-control">
                                <option value="">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Running</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('irrigation.logs') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Zone</th>
                                    <th>Field</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Water Source</th>
                                    <th>Duration</th>
                                    <th>Volume</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->irrigationZone->name ?? 'N/A' }}</td>
                                        <td>{{ $log->irrigationZone->field->name ?? 'N/A' }}</td>
                                        <td>{{ $log->started_at->format('M d, Y') }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $log->irrigation_method ?? 'N/A')) }}</td>
                                        <td>{{ ucfirst($log->water_source ?? 'N/A') }}</td>
                                        <td>{{ $log->duration_minutes ?? '-' }} min</td>
                                        <td>{{ $log->water_used_liters ?? $log->estimated_volume_liters ?? '-' }} L</td>
                                        <td>{{ $log->cost ? 'KES ' . number_format($log->cost, 2) : '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($log->status) {
                                                    'completed' => 'success',
                                                    'running' => 'info',
                                                    'failed' => 'danger',
                                                    default => 'warning',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($log->notes ?? '-', 30) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No irrigation logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
