@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock</p><h1>Species</h1></div><a class="button" href="{{ route('livestock.species.create') }}">New species</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Code</th><th>Scope</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($species as $item)<tr><td><a href="{{ route('livestock.species.show',$item) }}">{{ $item->name }}</a></td><td>{{ $item->code }}</td><td>{{ $item->organization?->name ?? 'System' }}</td><td>{{ ucfirst($item->species_type) }}</td><td>{{ ucfirst($item->status) }}</td><td><a href="{{ route('livestock.species.edit',$item) }}">Edit</a></td></tr>@empty
        <tr><td colspan="6">No species found.</td></tr>@endforelse
    </tbody></table></div>{{ $species->links() }}
@endsection
