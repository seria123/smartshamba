@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Irrigation</p><h1>Water sources</h1></div><a class="button" href="{{ route('irrigation.water-sources.create') }}">New source</a></header>
    <div class="table-wrap"><table><thead><tr><th>Code</th><th>Name</th><th>Type</th><th>Farm</th><th>Capacity</th><th>Status</th><th></th></tr></thead><tbody>@forelse($sources as $source)<tr><td><a href="{{ route('irrigation.water-sources.show',$source) }}">{{ $source->code }}</a></td><td>{{ $source->name }}</td><td>{{ ucfirst($source->source_type) }}</td><td>{{ $source->farm?->name }}</td><td>{{ $source->capacity ? $source->capacity.' '.$source->capacity_unit : '-' }}</td><td>{{ ucfirst(str_replace('_',' ',$source->status)) }}</td><td><a href="{{ route('irrigation.water-sources.edit',$source) }}">Edit</a></td></tr>@empty<tr><td colspan="7">No water sources found.</td></tr>@endforelse</tbody></table></div>{{ $sources->links() }}
@endsection
