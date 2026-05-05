@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crops</p><h1>Seasons</h1></div><a class="button" href="{{ route('crops.seasons.create') }}">New season</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Code</th><th>Farm</th><th>Dates</th><th>Status</th><th></th></tr></thead><tbody>@forelse($seasons as $season)<tr><td><a href="{{ route('crops.seasons.show',$season) }}">{{ $season->name }}</a></td><td>{{ $season->code }}</td><td>{{ $season->farm?->name ?? 'All farms' }}</td><td>{{ $season->start_date?->format('Y-m-d') }} - {{ $season->end_date?->format('Y-m-d') ?? 'Open' }}</td><td>{{ ucfirst($season->status) }}</td><td><a href="{{ route('crops.seasons.edit',$season) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No seasons found.</td></tr>@endforelse</tbody></table></div>{{ $seasons->links() }}
@endsection
