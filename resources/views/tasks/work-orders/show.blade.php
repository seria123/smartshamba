@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">{{ $workOrder->work_order_number }}</p>
            <h1>{{ $workOrder->title }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('tasks.work-orders.index') }}">Back</a>
            <a class="button" href="{{ route('tasks.work-orders.edit', $workOrder) }}">Edit</a>
            @if ($workOrder->status !== 'cancelled')
                <form method="POST" action="{{ route('tasks.work-orders.cancel', $workOrder) }}">
                    @csrf
                    <button type="submit">Cancel</button>
                </form>
            @endif
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Farm</dt><dd>{{ $workOrder->farm->name }}</dd></div>
        <div><dt>Status</dt><dd>{{ str_replace('_', ' ', ucfirst($workOrder->status)) }}</dd></div>
        <div><dt>Category</dt><dd>{{ ucfirst($workOrder->category) }}</dd></div>
        <div><dt>Priority</dt><dd>{{ ucfirst($workOrder->priority) }}</dd></div>
        <div><dt>Location</dt><dd>{{ $workOrder->field?->name ?? $workOrder->paddock?->name ?? $workOrder->warehouse?->name ?? $workOrder->site?->name ?? 'Not set' }}</dd></div>
        <div><dt>Due date</dt><dd>{{ $workOrder->due_date?->format('Y-m-d') ?? 'Not set' }}</dd></div>
        <div><dt>Notes</dt><dd>{{ $workOrder->notes ?? 'Not set' }}</dd></div>
    </dl>

    <section class="content-header" style="margin-top: 28px;">
        <div>
            <p class="eyebrow">Tasks</p>
            <h1>Work order tasks</h1>
        </div>
        <a class="button" href="{{ route('tasks.items.create') }}">New task</a>
    </section>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Number</th><th>Title</th><th>Status</th><th>Due</th></tr></thead>
            <tbody>
                @forelse ($workOrder->tasks as $task)
                    <tr>
                        <td>{{ $task->task_number }}</td>
                        <td><a href="{{ route('tasks.items.show', $task) }}">{{ $task->title }}</a></td>
                        <td>{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
                        <td>{{ $task->due_date?->format('Y-m-d') ?? 'Not set' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No tasks linked to this work order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
