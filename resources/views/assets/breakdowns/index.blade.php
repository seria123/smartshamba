@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Assets / Maintenance</p><h1>Breakdowns</h1></div><a class="button" href="{{ route('assets.breakdowns.create') }}">Record breakdown</a></header>
    <table><thead><tr><th>Number</th><th>Asset</th><th>Date</th><th>Issue</th><th>Severity</th><th>Status</th><th>Actions</th></tr></thead><tbody>@foreach($breakdowns as $breakdown)<tr><td>{{ $breakdown->breakdown_number }}</td><td>{{ $breakdown->asset?->name }}</td><td>{{ $breakdown->breakdown_date?->toDateString() }}</td><td>{{ str_replace('_',' ',$breakdown->issue_type) }}</td><td>{{ $breakdown->severity }}</td><td>{{ $breakdown->status }}</td><td><a href="{{ route('assets.breakdowns.show',$breakdown) }}">View</a></td></tr>@endforeach</tbody></table>{{ $breakdowns->links() }}
@endsection
