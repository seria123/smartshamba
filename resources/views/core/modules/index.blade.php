@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Core admin</p>
            <h1>Module registry</h1>
        </div>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Key</th>
                    <th>Status</th>
                    <th>Activated</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($modules as $module)
                    <tr>
                        <td>{{ $module->name }}</td>
                        <td>{{ $module->key }}</td>
                        <td>{{ ucfirst($module->status) }}</td>
                        <td>{{ $module->activated_at?->toDateString() ?? 'Not active' }}</td>
                        <td>{{ $module->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No modules registered.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
