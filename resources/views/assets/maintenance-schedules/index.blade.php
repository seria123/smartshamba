@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Assets / Maintenance</p><h1>Maintenance schedules</h1></div><a class="button" href="{{ route('assets.maintenance-schedules.create') }}">New schedule</a></header>
    <table><thead><tr><th>Number</th><th>Asset</th><th>Type</th><th>Date</th><th>Priority</th><th>Status</th><th>Assigned</th><th>Actions</th></tr></thead><tbody>@foreach($schedules as $schedule)<tr><td>{{ $schedule->schedule_number }}</td><td>{{ $schedule->asset?->name }}</td><td>{{ str_replace('_',' ',$schedule->maintenance_type) }}</td><td>{{ $schedule->scheduled_date?->toDateString() }}</td><td>{{ $schedule->priority }}</td><td>{{ $schedule->status }}</td><td>{{ $schedule->assignedWorker?->name ?? $schedule->assignedTeam?->name ?? 'Unassigned' }}</td><td><a href="{{ route('assets.maintenance-schedules.show',$schedule) }}">View</a></td></tr>@endforeach</tbody></table>{{ $schedules->links() }}
@endsection
