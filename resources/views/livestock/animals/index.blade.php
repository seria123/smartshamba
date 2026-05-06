@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock</p><h1>Animals</h1></div><a class="button" href="{{ route('livestock.animals.create') }}">New animal</a></header>
    <div class="table-wrap"><table><thead><tr><th>Code</th><th>Tag</th><th>Name</th><th>Species</th><th>Breed</th><th>Sex</th><th>Farm</th><th>Paddock</th><th>Status</th><th>Health</th><th></th></tr></thead><tbody>
        @forelse($animals as $animal)<tr><td><a href="{{ route('livestock.animals.show',$animal) }}">{{ $animal->animal_code }}</a></td><td>{{ $animal->tag_number ?? '-' }}</td><td>{{ $animal->name ?? '-' }}</td><td>{{ $animal->species?->name }}</td><td>{{ $animal->breed?->name ?? '-' }}</td><td>{{ ucfirst($animal->sex) }}</td><td>{{ $animal->farm?->name }}</td><td>{{ $animal->paddock?->name ?? '-' }}</td><td>{{ ucfirst($animal->status) }}</td><td>{{ ucfirst($animal->health_status ?? 'unknown') }}</td><td><a href="{{ route('livestock.animals.edit',$animal) }}">Edit</a></td></tr>@empty
        <tr><td colspan="11">No animals found.</td></tr>@endforelse
    </tbody></table></div>{{ $animals->links() }}
@endsection
