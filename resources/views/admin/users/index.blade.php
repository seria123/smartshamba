@extends('layouts.MainLayout')

@section('title', 'User Management - SmartShamba')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">👥 User Management</h3>
            <small class="text-muted">Manage system users, roles, and access</small>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    {{-- SEARCH & FILTERS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Role Filter --}}
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User (Farmer)</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- USERS TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Seen</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full d-flex items-center justify-center text-white fw-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-secondary ms-1">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleBadge = match($user->role) {
                                        'admin' => 'danger',
                                        'manager' => 'warning',
                                        default => 'secondary'
                                    };
                                    $roleLabel = match($user->role) {
                                        'admin' => 'Admin',
                                        'manager' => 'Manager',
                                        default => 'Farmer/User'
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleBadge }}">
                                    <i class="fas fa-{{ $user->role === 'admin' ? 'user-shield' : ($user->role === 'manager' ? 'user-cog' : 'user') }} me-1"></i>
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($user->status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'danger',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBadge }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }}"
                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $user->id }}"
                                            title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $user->status === 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                    @if(!$user->isAdmin())
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $user->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>

                                {{-- Status Toggle Modal --}}
                                <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Change User Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Change <strong>{{ $user->name }}</strong>'s status from <strong>{{ $user->status }}</strong> to:</p>
                                                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                                    <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
                                                    <p class="text-{{ $user->status === 'active' ? 'warning' : 'success' }}">
                                                        <i class="fas fa-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }} me-2"></i>
                                                        Set as {{ $user->status === 'active' ? 'Inactive' : 'Active' }}
                                                    </p>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-{{ $user->status === 'active' ? 'warning' : 'success' }}">
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete Modal --}}
                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $user->name }}</strong>? This action cannot be undone.</p>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-3"></i><br>
                                No users found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
