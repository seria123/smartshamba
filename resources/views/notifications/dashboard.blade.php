@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Notifications / Alerts</p><h1>Alerts dashboard</h1></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('notifications::partials-filters')
    @include('notifications::partials-nav')
    <section class="summary-grid">
        <article class="summary-card"><span>Unread</span><strong>{{ $unreadCount }}</strong></article>
        <article class="summary-card"><span>High / critical open</span><strong>{{ $highCriticalOpenCount }}</strong></article>
        <article class="summary-card"><span>Due soon</span><strong>{{ $dueSoonCount }}</strong></article>
    </section>
    <section class="grid-two">
        <article><h2>By source module</h2><div class="table-wrap"><table><thead><tr><th>Source</th><th>Total</th></tr></thead><tbody>@forelse($bySource as $row)<tr><td>{{ $row->label }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2">No alerts found.</td></tr>@endforelse</tbody></table></div></article>
        <article><h2>By severity</h2><div class="table-wrap"><table><thead><tr><th>Severity</th><th>Total</th></tr></thead><tbody>@forelse($bySeverity as $row)<tr><td>{{ ucfirst($row->label) }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2">No alerts found.</td></tr>@endforelse</tbody></table></div></article>
    </section>
    <h2>Recently generated alerts</h2>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Severity</th><th>Status</th><th>Source</th><th>Due</th><th></th></tr></thead><tbody>
        @forelse($recentAlerts as $notification)
            <tr><td>{{ $notification->title }}</td><td>{{ ucfirst($notification->severity) }}</td><td>{{ ucfirst($notification->status) }}</td><td>{{ $notification->source_module }}</td><td>{{ $notification->due_at?->toDateString() ?? '—' }}</td><td><a href="{{ route('notifications.show', $notification) }}">View</a></td></tr>
        @empty
            <tr><td colspan="6">No notifications found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
