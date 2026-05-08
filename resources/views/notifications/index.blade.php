@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Notifications / Alerts</p><h1>All notifications</h1></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('notifications::partials-filters')
    @include('notifications::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Severity</th><th>Status</th><th>Source</th><th>Farm</th><th>Due</th><th>Generated</th><th></th></tr></thead><tbody>
        @forelse($notifications as $notification)
            <tr><td>{{ $notification->title }}</td><td>{{ ucfirst($notification->severity) }}</td><td>{{ ucfirst($notification->status) }}</td><td>{{ $notification->source_module }}</td><td>{{ $notification->farm?->name ?? '—' }}</td><td>{{ $notification->due_at?->toDateString() ?? '—' }}</td><td>{{ $notification->generated_at?->diffForHumans() ?? '—' }}</td><td><a href="{{ route('notifications.show', $notification) }}">View</a></td></tr>
        @empty
            <tr><td colspan="8">No notifications found.</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $notifications->links() }}
@endsection
