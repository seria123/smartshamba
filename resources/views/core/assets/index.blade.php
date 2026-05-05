@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Core admin</p>
            <h1>{{ Str::plural($label) }}</h1>
        </div>
        <a class="button" href="{{ route($routeBase.'.create') }}">New {{ strtolower($label) }}</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Farm</th>
                    @if ($allowsSite)
                        <th>Site</th>
                    @endif
                    <th>Code</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td><a href="{{ route($routeBase.'.show', $item) }}">{{ $item->name }}</a></td>
                        <td>{{ $item->organization->name }}</td>
                        <td>{{ $item->farm->name }}</td>
                        @if ($allowsSite)
                            <td>{{ $item->site?->name ?? 'None' }}</td>
                        @endif
                        <td>{{ $item->code ?? 'Not set' }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td><a href="{{ route($routeBase.'.edit', $item) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $allowsSite ? 7 : 6 }}">No {{ strtolower(Str::plural($label)) }} found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
@endsection
