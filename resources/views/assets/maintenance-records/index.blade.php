@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Assets / Maintenance</p><h1>Maintenance records</h1></div><a class="button" href="{{ route('assets.maintenance-records.create') }}">Record service</a></header>
    <table><thead><tr><th>Number</th><th>Asset</th><th>Type</th><th>Date</th><th>Performed by</th><th>Status</th><th>Actions</th></tr></thead><tbody>@foreach($records as $record)<tr><td>{{ $record->record_number }}</td><td>{{ $record->asset?->name }}</td><td>{{ str_replace('_',' ',$record->maintenance_type) }}</td><td>{{ $record->maintenance_date?->toDateString() }}</td><td>{{ $record->performedByWorker?->name ?? $record->team?->name ?? 'Unassigned' }}</td><td>{{ $record->status }}</td><td><a href="{{ route('assets.maintenance-records.show',$record) }}">View</a></td></tr>@endforeach</tbody></table>{{ $records->links() }}
@endsection
