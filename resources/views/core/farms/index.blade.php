@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Core admin</p>
            <h1>Farms</h1>
        </div>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Sites</th>
                    <th>Fields</th>
                    <th>Paddocks</th>
                    <th>Warehouses</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($farms as $farm)
                    <tr>
                        <td><a href="{{ route('admin.core.farms.show', $farm) }}">{{ $farm->name }}</a></td>
                        <td>{{ $farm->organization->name }}</td>
                        <td>{{ $farm->code ?? 'Not set' }}</td>
                        <td>{{ ucfirst($farm->status) }}</td>
                        <td>{{ $farm->sites_count }}</td>
                        <td>{{ $farm->fields_count }}</td>
                        <td>{{ $farm->paddocks_count }}</td>
                        <td>{{ $farm->warehouses_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No farms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $farms->links() }}
@endsection
