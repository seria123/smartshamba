@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Reports</p><h1>Action activity</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Event</th><th>Action label</th><th>Count</th><th>Latest activity</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row->event }}</td><td>{{ $row->action_label ?? Str::headline($row->event) }}</td><td>{{ $row->event_count }}</td><td>{{ $row->latest_activity }}</td></tr>@empty<tr><td colspan="4">No action activity found.</td></tr>@endforelse</tbody></table></div>
@endsection
