@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Irrigation</p><h1>Water readings</h1></div><a class="button" href="{{ route('irrigation.readings.create') }}">New reading</a></header>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Source / Zone</th><th>Type</th><th>Value</th><th>Unit</th><th>Recorded by</th></tr></thead><tbody>@forelse($readings as $reading)<tr><td>{{ $reading->reading_date?->format('Y-m-d') }}</td><td>{{ $reading->waterSource?->name ?? $reading->zone?->name }}</td><td>{{ ucfirst(str_replace('_',' ',$reading->reading_type)) }}</td><td>{{ $reading->value }}</td><td>{{ $reading->unit_of_measure }}</td><td>{{ $reading->recordedByWorker?->name ?? $reading->recordedByUser?->name ?? '-' }}</td></tr>@empty<tr><td colspan="6">No readings found.</td></tr>@endforelse</tbody></table></div>{{ $readings->links() }}
@endsection
