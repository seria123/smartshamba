@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Assets / Maintenance</p><h1>Asset usage</h1></div><a class="button" href="{{ route('assets.usage.create') }}">Record usage</a></header>
    <table><thead><tr><th>Date</th><th>Asset</th><th>Type</th><th>Related work</th><th>Used by</th><th>Duration</th></tr></thead><tbody>@foreach($records as $record)<tr><td>{{ $record->usage_date?->toDateString() }}</td><td>{{ $record->asset?->name }}</td><td>{{ str_replace('_',' ',$record->usage_type) }}</td><td>{{ $record->relatedTask?->task_number ?? $record->relatedIrrigationEvent?->event_number ?? 'None' }}</td><td>{{ $record->usedByWorker?->name ?? $record->team?->name ?? 'Unassigned' }}</td><td>{{ $record->duration_minutes ?? 0 }} min</td></tr>@endforeach</tbody></table>{{ $records->links() }}
@endsection
