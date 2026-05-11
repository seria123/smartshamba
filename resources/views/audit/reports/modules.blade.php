@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Reports</p><h1>Module activity</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Module</th><th>Event count</th><th>Latest activity</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ Str::headline($row->module) }}</td><td>{{ $row->event_count }}</td><td>{{ $row->latest_activity }}</td></tr>@empty<tr><td colspan="3">No module activity found.</td></tr>@endforelse</tbody></table></div>
@endsection
