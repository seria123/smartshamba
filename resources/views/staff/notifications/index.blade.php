@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Notifications - {{ $staff->fullName() }}</h1>
            @if($unreadCount > 0)
            <a href="{{ route('staff.notifications.markAllRead', $staff) }}" class="btn btn-outline-primary">
                <i class="fas fa-check-double me-1"></i> Mark All Read ({{ $unreadCount }})
            </a>
            @endif
        </div>

        <div class="card-modern p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Read</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr class="{{ !$notification->is_read ? 'table-warning' : '' }}">
                            <td>{{ $notification->title }}</td>
                            <td>{{ Str::limit($notification->message, 60) }}</td>
                            <td>
                                <span class="badge bg-{{ $notification->type === 'alert' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info') }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td>
                                @if($notification->is_read)
                                <i class="fas fa-check-circle text-success"></i>
                                @else
                                <i class="fas fa-circle text-warning"></i>
                                @endif
                            </td>
                            <td>{{ $notification->created_at->diffForHumans() }}</td>
                            <td>
                                @if(!$notification->is_read)
                                <a href="{{ route('staff.notifications.markRead', [$staff, $notification]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check"></i>
                                </a>
                                @endif
                                <form action="{{ route('staff.notifications.destroy', [$staff, $notification]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete notification?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No notifications</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
