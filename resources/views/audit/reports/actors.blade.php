@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Reports</p><h1>Actor activity</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Actor</th><th>Email</th><th>Event count</th><th>Latest activity</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row->actor_name ?? 'System' }}</td><td>{{ $row->actor_email }}</td><td>{{ $row->event_count }}</td><td>{{ $row->latest_activity }}</td></tr>@empty<tr><td colspan="4">No actor activity found.</td></tr>@endforelse</tbody></table></div>
@endsection
