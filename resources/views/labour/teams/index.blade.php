@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Workers and labour</p>
            <h1>Teams</h1>
        </div>
        <a class="button" href="{{ route('labour.teams.create') }}">New team</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Farm</th>
                    <th>Type</th>
                    <th>Supervisor</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teams as $team)
                    <tr>
                        <td><a href="{{ route('labour.teams.show', $team) }}">{{ $team->name }}</a></td>
                        <td>{{ $team->code ?? 'Not set' }}</td>
                        <td>{{ $team->farm->name }}</td>
                        <td>{{ $team->team_type }}</td>
                        <td>{{ $team->supervisor?->name ?? 'Not set' }}</td>
                        <td>{{ ucfirst($team->status) }}</td>
                        <td><a href="{{ route('labour.teams.edit', $team) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No teams found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $teams->links() }}
@endsection
