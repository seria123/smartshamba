@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Irrigation</p><h1>Zones</h1></div><a class="button" href="{{ route('irrigation.zones.create') }}">New zone</a></header>
    <div class="table-wrap"><table><thead><tr><th>Code</th><th>Name</th><th>Farm</th><th>Field</th><th>Water source</th><th>Method</th><th>Status</th><th></th></tr></thead><tbody>@forelse($zones as $zone)<tr><td><a href="{{ route('irrigation.zones.show',$zone) }}">{{ $zone->code }}</a></td><td>{{ $zone->name }}</td><td>{{ $zone->farm?->name }}</td><td>{{ $zone->field?->name ?? '-' }}</td><td>{{ $zone->waterSource?->name ?? '-' }}</td><td>{{ ucfirst(str_replace('_',' ',$zone->irrigation_method)) }}</td><td>{{ ucfirst(str_replace('_',' ',$zone->status)) }}</td><td><a href="{{ route('irrigation.zones.edit',$zone) }}">Edit</a></td></tr>@empty<tr><td colspan="8">No zones found.</td></tr>@endforelse</tbody></table></div>{{ $zones->links() }}
@endsection
