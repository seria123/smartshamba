@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tint"></i> Irrigation Management</h1>
                <div>
                    <a href="{{ route('irrigation.logs') }}" class="btn btn-info">
                        <i class="fas fa-history"></i> View Logs
                    </a>
                    <a href="{{ route('irrigation.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Zone
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label>Field</label>
                            <select name="field_id" class="form-control">
                                <option value="">All Fields</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" {{ request('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('irrigation.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Irrigation Zones -->
            <div class="row">
                @forelse($zones as $zone)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $zone->name }}</h5>
                                <span class="badge bg-{{ $zone->is_active ? 'success' : 'secondary' }}">
                                    {{ $zone->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>Field:</strong> {{ $zone->field->name ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($zone->status) }}</p>
                                <p><strong>Schedule:</strong> {{ ucfirst($zone->schedule_type) }}</p>
                                <p><strong>Flow Rate:</strong> {{ $zone->flow_rate_lph ?? 'N/A' }} LPH</p>
                                <p><strong>Duration:</strong> {{ $zone->duration_minutes ?? 'N/A' }} min</p>
                                
                                @if($zone->schedule_type !== 'manual')
                                    <p><strong>Start Time:</strong> {{ $zone->start_time }}</p>
                                @endif

                                @if($zone->soil_moisture_threshold)
                                    <p><strong>Moisture Threshold:</strong> {{ $zone->soil_moisture_threshold }}%</p>
                                @endif

                                @if($zone->isRunning())
                                    <div class="alert alert-info">
                                        <i class="fas fa-spinner fa-spin"></i> Irrigation in progress...
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('irrigation.show', $zone) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <div>
                                        @if($zone->is_active && !$zone->isRunning())
                                            <form action="{{ route('irrigation.start', $zone) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i> Start
                                                </button>
                                            </form>
                                        @elseif($zone->isRunning())
                                            <form action="{{ route('irrigation.stop', $zone) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-stop"></i> Stop
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('irrigation.edit', $zone) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">No irrigation zones found. Create one to get started.</div>
                    </div>
                @endforelse
            </div>

            {{ $zones->links() }}
        </div>
    </div>
</div>
@endsection
