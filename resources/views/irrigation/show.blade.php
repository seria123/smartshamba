@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tint"></i> {{ $irrigation->name }}</h1>
                <a href="{{ route('irrigation.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Zone Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Field:</strong> {{ $irrigation->field->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $irrigation->is_active ? 'success' : 'secondary' }}">
                                        {{ ucfirst($irrigation->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Controller ID:</strong> {{ $irrigation->controller_id ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Valve ID:</strong> {{ $irrigation->valve_id ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Flow Rate:</strong> {{ $irrigation->flow_rate_lph ?? 'N/A' }} LPH
                                </div>
                                <div class="col-md-6">
                                    <strong>Duration:</strong> {{ $irrigation->duration_minutes ?? 'N/A' }} minutes
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Schedule Type:</strong> {{ ucfirst($irrigation->schedule_type) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Start Time:</strong> {{ $irrigation->start_time ?? 'N/A' }}
                                </div>
                            </div>
                            @if($irrigation->soil_moisture_threshold)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Moisture Threshold:</strong> {{ $irrigation->soil_moisture_threshold }}%
                                </div>
                            </div>
                            @endif
                            @if($irrigation->description)
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p>{{ $irrigation->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Today's Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>{{ number_format($irrigation->getTotalWaterUsedToday(), 1) }}</h3>
                                    <p>Liters Used Today</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>{{ $irrigation->getTotalDurationToday() }}</h3>
                                    <p>Minutes Today</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>
                                        @if($irrigation->isRunning())
                                            <span class="badge bg-success">Running</span>
                                        @else
                                            <span class="badge bg-secondary">Idle</span>
                                        @endif
                                    </h3>
                                    <p>Status</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Irrigation Logs</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Duration</th>
                                            <th>Water Used</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($irrigation->logs as $log)
                                            <tr>
                                                <td>{{ $log->started_at->format('M d, H:i') }}</td>
                                                <td>{{ ucfirst($log->event_type) }}</td>
                                                <td>{{ $log->duration_minutes ?? '-' }} min</td>
                                                <td>{{ $log->water_used_liters ?? '-' }} L</td>
                                                <td>
                                                    @php
                                                        $logStatusClass = match($log->status) {
                                                            'completed' => 'success',
                                                            'running' => 'info',
                                                            default => 'warning',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $logStatusClass }}">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No irrigation logs yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($irrigation->is_active && !$irrigation->isRunning())
                                    <form action="{{ route('irrigation.start', $irrigation) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-play"></i> Start Irrigation
                                        </button>
                                    </form>
                                @elseif($irrigation->isRunning())
                                    <form action="{{ route('irrigation.stop', $irrigation) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-stop"></i> Stop Irrigation
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('irrigation.edit', $irrigation) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Zone
                                </a>
                                <form action="{{ route('irrigation.destroy', $irrigation) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Delete Zone
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
