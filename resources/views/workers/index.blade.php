@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Worker / Labor Management</h4>
                    <a href="{{ route('workers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Worker
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search by name, phone, or ID..." 
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="role" class="form-select">
                                    <option value="">All Roles</option>
                                    <option value="general_worker" {{ request('role') == 'general_worker' ? 'selected' : '' }}>General Worker</option>
                                    <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="driver" {{ request('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                                    <option value="harvester" {{ request('role') == 'harvester' ? 'selected' : '' }}>Harvester</option>
                                    <option value="planting" {{ request('role') == 'planting' ? 'selected' : '' }}>Planting</option>
                                    <option value="irrigation" {{ request('role') == 'irrigation' ? 'selected' : '' }}>Irrigation</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Daily Wage</th>
                                    <th>Hire Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workers as $worker)
                                <tr>
                                    <td>
                                        <a href="{{ route('workers.show', $worker) }}">
                                            {{ $worker->fullName() }}
                                        </a>
                                    </td>
                                    <td>{{ $worker->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $worker->role)) }}</span>
                                    </td>
                                    <td>{{ number_format($worker->daily_wage, 2) }}</td>
                                    <td>{{ $worker->hire_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $worker->status === 'active' ? 'success' : ($worker->status === 'inactive' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($worker->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('workers.show', $worker) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('workers.edit', $worker) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('workers.attendance', $worker) }}" class="btn btn-outline-info">
                                                <i class="fas fa-calendar-check"></i>
                                            </a>
                                            <a href="{{ route('workers.wages', $worker) }}" class="btn btn-outline-success">
                                                <i class="fas fa-dollar-sign"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No workers found. <a href="{{ route('workers.create') }}">Add your first worker</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $workers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
