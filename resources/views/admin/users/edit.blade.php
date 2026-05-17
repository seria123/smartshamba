@extends('layouts.MainLayout')

@section('title', 'Edit User - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="text-2xl font-bold mb-0">✏️ Edit User</h3>
                <small class="text-gray-500">Modify user roles and permissions</small>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>

        {{-- USER INFO CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-2xl fw-bold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-gray-500 mb-0">{{ $user->email }}</p>
                    <small class="text-gray-400">Member since {{ $user->created_at->format('M d, Y') }}</small>
                </div>
            </div>
        </div>

        {{-- EDIT FORM --}}
        <div class="card-modern p-4 sm:p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- ROLE ASSIGNMENT --}}
                <div>
                    <label class="form-label fw-semibold">Role</label>
                    <p class="text-gray-500 text-sm mb-3">Determine what the user can access and do in the system.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $roles = [
                                'admin' => ['icon' => 'fa-user-shield', 'color' => 'danger', 'desc' => 'Full system access, can manage all resources and users'],
                                'manager' => ['icon' => 'fa-user-cog', 'color' => 'warning', 'desc' => 'Operational access, can manage farms, crops, livestock'],
                                'user' => ['icon' => 'fa-user', 'color' => 'info', 'desc' => 'Standard farmer - can manage own farms and data'],
                            ];
                        @endphp
                        @foreach($roles as $roleValue => $role)
                            <div class="form-check card p-3 h-100 {{ $user->role === $roleValue ? 'border-primary bg-primary bg-opacity-10' : 'border border-gray-200' }}">
                                <input class="form-check-input" type="radio" name="role" id="role{{ $roleValue }}"
                                       value="{{ $roleValue }}" {{ $user->role === $roleValue ? 'checked' : '' }}
                                       {{ $user->isAdmin() && $user->id !== auth()->id() ? 'disabled' : '' }}>
                                <label class="form-check-label w-100" for="role{{ $roleValue }}">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fas {{ $role['icon'] }} text-{{ $role['color'] }}"></i>
                                        <strong class="text-capitalize">{{ $roleValue }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $role['desc'] }}</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- STATUS TOGGLE --}}
                <div>
                    <label class="form-label fw-semibold">Account Status</label>
                    <p class="text-gray-500 text-sm mb-3">Control whether the user can log in and access the system.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $statuses = [
                                'active' => ['icon' => 'fa-check-circle', 'color' => 'success', 'desc' => 'User can log in and use the system normally'],
                                'inactive' => ['icon' => 'fa-pause-circle', 'color' => 'secondary', 'desc' => 'User cannot log in but data is preserved'],
                                'suspended' => ['icon' => 'fa-ban-circle', 'color' => 'danger', 'desc' => 'User access revoked - account flagged for review'],
                            ];
                        @endphp
                        @foreach($statuses as $statusValue => $status)
                            <div class="form-check card p-3 h-100 {{ $user->status === $statusValue ? 'border-primary bg-primary bg-opacity-10' : 'border border-gray-200' }}">
                                <input class="form-check-input" type="radio" name="status" id="status{{ $statusValue }}"
                                       value="{{ $statusValue }}" {{ $user->status === $statusValue ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="status{{ $statusValue }}">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fas {{ $status['icon'] }} text-{{ $status['color'] }}"></i>
                                        <strong class="text-capitalize">{{ $statusValue }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $status['desc'] }}</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- WARNING --}}
                @if($user->isAdmin() && $user->id !== auth()->id())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This user has admin privileges. Changes are restricted to prevent accidental lockout.
                    </div>
                @endif

                @if($user->id === auth()->id())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are editing your own account. Role and status cannot be changed.
                    </div>
                @endif

                {{-- ACTIONS --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary text-center {{ $user->id === auth()->id() ? 'disabled' : '' }}">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection