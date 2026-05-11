@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Trail</p><h1>Activity dashboard</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <section class="summary-grid">
        <article class="summary-card"><span>Total events</span><strong>{{ $totalEvents }}</strong></article>
        <article class="summary-card"><span>Events today</span><strong>{{ $eventsToday }}</strong></article>
        <article class="summary-card"><span>Events this week</span><strong>{{ $eventsThisWeek }}</strong></article>
    </section>
    <h2>Recent activity</h2>
    <div class="table-wrap"><table><thead><tr><th>Time</th><th>Actor</th><th>Module</th><th>Event</th><th>Subject</th><th></th></tr></thead><tbody>
        @forelse($recentActivity as $log)
            <tr><td>{{ $log->occurred_at?->format('Y-m-d H:i') }}</td><td>{{ $log->actor_display_name }}</td><td>{{ $log->module_label }}</td><td>{{ $log->event_label }}</td><td>{{ $log->subject_display }}</td><td><a href="{{ route('audit.logs.show', $log) }}">View</a></td></tr>
        @empty
            <tr><td colspan="6">No audit activity found.</td></tr>
        @endforelse
    </tbody></table></div>
    <h2>High-risk actions</h2>
    <div class="table-wrap"><table><thead><tr><th>Time</th><th>Actor</th><th>Event</th><th>Description</th></tr></thead><tbody>
        @forelse($highRiskActions as $log)
            <tr><td>{{ $log->occurred_at?->format('Y-m-d H:i') }}</td><td>{{ $log->actor_display_name }}</td><td>{{ $log->event_label }}</td><td>{{ $log->description }}</td></tr>
        @empty
            <tr><td colspan="4">No high-risk actions in this filter.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
