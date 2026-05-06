@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock</p><h1>Breeds</h1></div><a class="button" href="{{ route('livestock.breeds.create') }}">New breed</a></header>
    <div class="table-wrap"><table><thead><tr><th>Species</th><th>Breed</th><th>Code</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($breeds as $breed)<tr><td>{{ $breed->species?->name }}</td><td><a href="{{ route('livestock.breeds.show',$breed) }}">{{ $breed->name }}</a></td><td>{{ $breed->code ?? '-' }}</td><td>{{ ucfirst($breed->status) }}</td><td><a href="{{ route('livestock.breeds.edit',$breed) }}">Edit</a></td></tr>@empty
        <tr><td colspan="5">No breeds found.</td></tr>@endforelse
    </tbody></table></div>{{ $breeds->links() }}
@endsection
