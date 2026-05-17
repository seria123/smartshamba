@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Staff / Labor Management</h1>
            <a href="{{ route('staff.create') }}" class="btn btn-primary whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Staff
            </a>
        </div>

        {{-- Filter Form --}}
        <div class="card-modern p-4">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-input-modern"
                        placeholder="Search by name, phone, or ID..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select-modern">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select-modern">
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
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>

        {{-- Staff Table --}}
        <div class="card-modern overflow-hidden flex flex-col" style="max-height: calc(100vh - 240px);">
            <div class="overflow-y-auto flex-1">
                <table class="table table-striped table-hover mb-0">
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
                        @forelse($staff as $staffMember)
                        <tr>
                            <td>
                                <a href="{{ route('staff.show', $staffMember) }}">
                                    {{ $staffMember->fullName() }}
                                </a>
                            </td>
                            <td>{{ $staffMember->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $staffMember->role)) }}</span>
                            </td>
                            <td>{{ number_format($staffMember->daily_wage, 2) }}</td>
                            <td>{{ $staffMember->hire_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $staffMember->status === 'active' ? 'success' : ($staffMember->status === 'inactive' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($staffMember->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.show', $staffMember) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.edit', $staffMember) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('staff.attendance', $staffMember) }}" class="btn btn-outline-info">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                    <a href="{{ route('staff.wages', $staffMember) }}" class="btn btn-outline-success">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No staff members found. <a href="{{ route('staff.create') }}">Add your first staff member</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t bg-gray-50">
                {{ $staff->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
