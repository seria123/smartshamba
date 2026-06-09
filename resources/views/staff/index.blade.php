@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Staff / Labor Management</h1>
            <a href="{{ route('staff.create') }}" class="btn btn-primary whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Staff
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <a href="{{ route('staff.analytics.index') }}" class="card bg-primary text-white text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Analytics</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('staff.presence.fields') }}" class="card bg-success text-white text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Field Presence</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('staff.schedules.all') }}" class="card bg-info text-white text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Schedules</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="card bg-warning text-dark text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Reports</h5>
                    </div>
                </a>
            </div>
        </div>

        <div class="card-modern p-4">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-input-modern"
                        placeholder="Name, phone, ID..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select-modern">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select-modern">
                        <option value="">All Roles</option>
                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="worker" {{ request('role') === 'worker' ? 'selected' : '' }}>Worker</option>
                        <option value="agronomist" {{ request('role') === 'agronomist' ? 'selected' : '' }}>Agronomist</option>
                        <option value="general_worker" {{ request('role') === 'general_worker' ? 'selected' : '' }}>General Worker</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-modern overflow-hidden flex flex-col" style="max-height: calc(100vh - 240px);">
            <div class="overflow-y-auto flex-1">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Performance</th>
                            <th>Current Field</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $staffMember)
                        <tr>
                            <td>
                                <a href="{{ route('staff.show', $staffMember) }}">
                                    {{ $staffMember->fullName() }}
                                </a>
                                @if($staffMember->activeLocation)
                                <i class="fas fa-map-marker-alt text-success ms-1" title="Currently checked in"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $staffMember->role_badge_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $staffMember->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($staffMember->performance_score)
                                <div class="progress" style="height: 20px; width: 120px;">
                                    <div class="progress-bar bg-{{ $staffMember->performance_score >= 70 ? 'success' : ($staffMember->performance_score >= 50 ? 'warning' : 'danger') }}"
                                         style="width: {{ $staffMember->performance_score }}%">
                                        {{ $staffMember->performance_score }}%
                                    </div>
                                </div>
                                @else
                                <span class="text-muted small">No review</span>
                                @endif
                            </td>
                            <td>{{ $staffMember->currentField->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $staffMember->status_color }}">
                                    {{ ucfirst($staffMember->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.show', $staffMember) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.field_assignments.index', $staffMember) }}" class="btn btn-outline-success" title="Fields">
                                        <i class="fas fa-map"></i>
                                    </a>
                                    <a href="{{ route('staff.performance.index', $staffMember) }}" class="btn btn-outline-warning" title="Performance">
                                        <i class="fas fa-star"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No staff members found. <a href="{{ route('staff.create') }}">Add your first staff member</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t bg-gray-50">
                @if(method_exists($staff, 'links'))
                {{ $staff->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
