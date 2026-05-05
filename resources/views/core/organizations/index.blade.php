@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Core admin</p>
            <h1>Organizations</h1>
        </div>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Farms</th>
                    <th>Sites</th>
                    <th>Fields</th>
                    <th>Paddocks</th>
                    <th>Warehouses</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($organizations as $organization)
                    <tr>
                        <td><a href="{{ route('admin.core.organizations.show', $organization) }}">{{ $organization->name }}</a></td>
                        <td>{{ ucfirst($organization->status) }}</td>
                        <td>{{ $organization->farms_count }}</td>
                        <td>{{ $organization->sites_count }}</td>
                        <td>{{ $organization->fields_count }}</td>
                        <td>{{ $organization->paddocks_count }}</td>
                        <td>{{ $organization->warehouses_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No organizations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $organizations->links() }}
@endsection
