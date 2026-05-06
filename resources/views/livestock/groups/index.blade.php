@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock</p><h1>Animal groups</h1></div><a class="button" href="{{ route('livestock.groups.create') }}">New group</a></header>
    <div class="table-wrap"><table><thead><tr><th>Code</th><th>Name</th><th>Species</th><th>Breed</th><th>Farm</th><th>Paddock</th><th>Count</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($groups as $group)<tr><td><a href="{{ route('livestock.groups.show',$group) }}">{{ $group->group_code }}</a></td><td>{{ $group->name }}</td><td>{{ $group->species?->name }}</td><td>{{ $group->breed?->name ?? '-' }}</td><td>{{ $group->farm?->name }}</td><td>{{ $group->paddock?->name ?? '-' }}</td><td>{{ $group->current_count }}</td><td>{{ ucfirst($group->status) }}</td><td><a href="{{ route('livestock.groups.edit',$group) }}">Edit</a></td></tr>@empty
        <tr><td colspan="9">No animal groups found.</td></tr>@endforelse
    </tbody></table></div>{{ $groups->links() }}
@endsection
