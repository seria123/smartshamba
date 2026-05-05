@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Workers and labour</p>
            <h1>Workers</h1>
        </div>
        <a class="button" href="{{ route('labour.workers.create') }}">New worker</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Farm</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workers as $worker)
                    <tr>
                        <td><a href="{{ route('labour.workers.show', $worker) }}">{{ $worker->name }}</a></td>
                        <td>{{ $worker->worker_code }}</td>
                        <td>{{ $worker->farm->name }}</td>
                        <td>{{ $worker->primary_role }}</td>
                        <td>{{ ucfirst($worker->status) }}</td>
                        <td><a href="{{ route('labour.workers.edit', $worker) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No workers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $workers->links() }}
@endsection
