@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Reports</p><h1>Daily activity</h1></div></header>
    @include('audit::partials-filters')
    @include('audit::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Event count</th><th>Unique actors</th><th>Top module</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row->activity_date }}</td><td>{{ $row->event_count }}</td><td>{{ $row->unique_actors }}</td><td>{{ Str::headline($row->top_module ?? '') }}</td></tr>@empty<tr><td colspan="4">No daily activity found.</td></tr>@endforelse</tbody></table></div>
@endsection
