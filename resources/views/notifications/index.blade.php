@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-bell"></i> Notifications</h1>
                <div>
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-double"></i> Mark All as Read
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Clear all notifications?');">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="alert" {{ request('type') == 'alert' ? 'selected' : '' }}>Alert</option>
                                <option value="task" {{ request('type') == 'task' ? 'selected' : '' }}>Task</option>
                                <option value="irrigation" {{ request('type') == 'irrigation' ? 'selected' : '' }}>Irrigation</option>
                                <option value="sensor" {{ request('type') == 'sensor' ? 'selected' : '' }}>Sensor</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Priority</label>
                            <select name="priority" class="form-control">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Read Status</label>
                            <select name="read" class="form-control">
                                <option value="">All</option>
                                <option value="true" {{ request('read') == 'true' ? 'selected' : '' }}>Read</option>
                                <option value="false" {{ request('read') == 'false' ? 'selected' : '' }}>Unread</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('notifications.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card">
                <div class="card-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item border-bottom py-3 {{ !$notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-{{ $notification->priority_color }}">
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> {{ ucfirst($notification->type) }} &bull;
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    @if(!$notification->is_read)
                                        <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as Read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No notifications found.</p>
                        </div>
                    @endforelse
                    
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
