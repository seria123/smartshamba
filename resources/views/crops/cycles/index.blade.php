@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crops</p><h1>Crop cycles</h1></div><a class="button" href="{{ route('crops.cycles.create') }}">New cycle</a></header>
    <div class="table-wrap"><table><thead><tr><th>Cycle</th><th>Crop</th><th>Farm</th><th>Field</th><th>Status</th><th></th></tr></thead><tbody>@forelse($cycles as $cycle)<tr><td><a href="{{ route('crops.cycles.show',$cycle) }}">{{ $cycle->cycle_number }}</a><br>{{ $cycle->name }}</td><td>{{ $cycle->crop->name }} {{ $cycle->variety?->name }}</td><td>{{ $cycle->farm->name }}</td><td>{{ $cycle->field->name }}</td><td>{{ ucfirst($cycle->status) }}</td><td><a href="{{ route('crops.cycles.edit',$cycle) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No crop cycles found.</td></tr>@endforelse</tbody></table></div>{{ $cycles->links() }}
@endsection
