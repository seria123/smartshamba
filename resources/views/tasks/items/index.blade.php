@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Tasks / Work Orders</p>
            <h1>Tasks</h1>
        </div>
        <a class="button" href="{{ route('tasks.items.create') }}">New task</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Farm</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned</th>
                    <th>Due</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>{{ $task->task_number }}</td>
                        <td><a href="{{ route('tasks.items.show', $task) }}">{{ $task->title }}</a></td>
                        <td>{{ $task->farm->name }}</td>
                        <td>{{ ucfirst($task->category) }}</td>
                        <td>{{ ucfirst($task->priority) }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
                        <td>{{ $task->assignments->map(fn ($assignment) => $assignment->worker?->name ?? $assignment->team?->name ?? $assignment->user?->name)->filter()->join(', ') ?: 'Unassigned' }}</td>
                        <td>{{ $task->due_date?->format('Y-m-d') ?? 'Not set' }}</td>
                        <td><a href="{{ route('tasks.items.edit', $task) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9">No tasks found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tasks->links() }}
@endsection
