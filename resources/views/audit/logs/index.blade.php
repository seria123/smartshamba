@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Trail</p><h1>Activity logs</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Timestamp</th><th>Actor</th><th>Scope</th><th>Module</th><th>Event</th><th>Subject</th><th></th></tr></thead><tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $log->occurred_at?->format('Y-m-d H:i:s') }}</td>
                <td>{{ $log->actor_display_name }}<br><small>{{ $log->actor_email }}</small></td>
                <td>{{ $log->organization?->name ?? 'System' }}<br><small>{{ $log->farm?->name ?? 'All farms' }}</small></td>
                <td>{{ $log->module_label }}</td>
                <td>{{ $log->event_label }}</td>
                <td>{{ $log->subject_display }}</td>
                <td><a href="{{ route('audit.logs.show', $log) }}">View</a></td>
            </tr>
        @empty
            <tr><td colspan="7">No audit logs found.</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $logs->links() }}
@endsection
