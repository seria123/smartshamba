@extends('layouts.MainLayout')

@section('title', 'Admin Dashboard - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">SmartShamba Admin Panel</h4>
                    <div class="d-flex gap-2">
                        <span class="badge bg-{{ Auth::user()->isManager() ? 'warning' : 'primary' }}">
                            {{ Auth::user()->isManager() ? 'Manager' : 'Super Admin' }}
                        </span>
                        <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Quick Stats Overview --}}
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Total Farms</h5>
                                    <h3 class="mb-0">{{ $stats['total_farms'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Total Fields</h5>
                                    <h3 class="mb-0">{{ $stats['total_fields'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Total Sensors</h5>
                                    <h3 class="mb-0">{{ $stats['total_sensors'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Active Sensors</h5>
                                    <h3 class="mb-0">{{ $stats['active_sensors'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Total Workers</h5>
                                    <h3 class="mb-0">{{ $stats['total_workers'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Total Harvests</h5>
                                    <h3 class="mb-0">{{ $stats['total_harvests'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Activity --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Details</th>
                                                    <th>User</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentActivity as $activity)
                                                <tr>
                                                    <td>{{ $activity['type'] }}</td>
                                                    <td>{{ $activity['details'] }}</td>
                                                    <td>{{ $activity['user'] }}</td>
                                                    <td>{{ $activity['date']->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        @if($activity['status'] === 'success')
                                                            <span class="badge bg-success">Success</span>
                                                        @else
                                                            <span class="badge bg-danger">Error</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No recent activity</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
