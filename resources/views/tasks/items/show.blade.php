@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">{{ $task->task_number }}</p>
            <h1>{{ $task->title }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('tasks.items.index') }}">Back</a>
            <a class="button" href="{{ route('tasks.items.edit', $task) }}">Edit</a>
            @if (! in_array($task->status, ['completed', 'cancelled'], true))
                <form method="POST" action="{{ route('tasks.items.start', $task) }}">@csrf<button type="submit">Start</button></form>
                <form method="POST" action="{{ route('tasks.items.submit', $task) }}">@csrf<button type="submit">Submit</button></form>
                <form method="POST" action="{{ route('tasks.items.approve', $task) }}">@csrf<button type="submit">Approve</button></form>
                <form method="POST" action="{{ route('tasks.items.reject', $task) }}">@csrf<button type="submit">Request correction</button></form>
                <form method="POST" action="{{ route('tasks.items.cancel', $task) }}">@csrf<button type="submit">Cancel</button></form>
            @endif
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Farm</dt><dd>{{ $task->farm->name }}</dd></div>
        <div><dt>Work order</dt><dd>{{ $task->workOrder?->work_order_number ?? 'Standalone' }}</dd></div>
        <div><dt>Status</dt><dd>{{ str_replace('_', ' ', ucfirst($task->status)) }}</dd></div>
        <div><dt>Category</dt><dd>{{ ucfirst($task->category) }}</dd></div>
        <div><dt>Priority</dt><dd>{{ ucfirst($task->priority) }}</dd></div>
        <div><dt>Location</dt><dd>{{ $task->field?->name ?? $task->paddock?->name ?? $task->warehouse?->name ?? $task->site?->name ?? 'Not set' }}</dd></div>
        <div><dt>Due date</dt><dd>{{ $task->due_date?->format('Y-m-d') ?? 'Not set' }}</dd></div>
        <div><dt>Notes</dt><dd>{{ $task->notes ?? 'Not set' }}</dd></div>
    </dl>

    <section class="content-header" style="margin-top: 28px;">
        <div>
            <p class="eyebrow">Assignments</p>
            <h1>Assigned work</h1>
        </div>
    </section>

    <form class="form-panel" method="POST" action="{{ route('tasks.items.assign', $task) }}">
        @csrf
        <div class="field-group">
            <label for="assignment_type">Assignment type</label>
            <select id="assignment_type" name="assignment_type" required>
                @foreach (['worker', 'team', 'user'] as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            @error('assignment_type') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="field-group">
            <label for="worker_id">Worker</label>
            <select id="worker_id" name="worker_id">
                <option value="">No worker</option>
                @foreach ($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }} - {{ $worker->worker_code }}</option>
                @endforeach
            </select>
            @error('worker_id') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="field-group">
            <label for="team_id">Team</label>
            <select id="team_id" name="team_id">
                <option value="">No team</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            @error('team_id') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="field-group">
            <label for="user_id">User</label>
            <select id="user_id" name="user_id">
                <option value="">No user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="field-group">
            <label for="role_on_task">Role on task</label>
            <input id="role_on_task" name="role_on_task">
        </div>
        <div class="field-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        <button type="submit">Assign</button>
    </form>

    <div class="table-wrap" style="margin-top: 20px;">
        <table>
            <thead><tr><th>Type</th><th>Assignee</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($task->assignments as $assignment)
                    <tr>
                        <td>{{ ucfirst($assignment->assignment_type) }}</td>
                        <td>{{ $assignment->worker?->name ?? $assignment->team?->name ?? $assignment->user?->name ?? 'Not set' }}</td>
                        <td>{{ ucfirst($assignment->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No assignments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <section class="content-header" style="margin-top: 28px;">
        <div>
            <p class="eyebrow">Checklist</p>
            <h1>Checklist items</h1>
        </div>
    </section>

    <form class="form-panel" method="POST" action="{{ route('tasks.items.checklist.store', $task) }}">
        @csrf
        <div class="field-group">
            <label for="label">Item</label>
            <input id="label" name="label" required>
            @error('label') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div class="field-group">
            <label for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="0">
        </div>
        <button type="submit">Add checklist item</button>
    </form>

    <div class="table-wrap" style="margin-top: 20px;">
        <table>
            <thead><tr><th>Item</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($task->checklistItems as $item)
                    <tr>
                        <td>{{ $item->label }}</td>
                        <td>{{ $item->is_completed ? 'Completed' : 'Open' }}</td>
                        <td>
                            <form method="POST" action="{{ route('tasks.checklist.toggle', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Toggle</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">No checklist items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <section class="content-header" style="margin-top: 28px;">
        <div>
            <p class="eyebrow">Progress</p>
            <h1>Updates</h1>
        </div>
    </section>

    <form class="form-panel" method="POST" action="{{ route('tasks.items.updates.store', $task) }}">
        @csrf
        <div class="field-group">
            <label for="update_type">Update type</label>
            <select id="update_type" name="update_type" required>
                @foreach (['comment', 'progress', 'issue', 'submission', 'approval', 'rejection', 'correction_request', 'completion'] as $type)
                    <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-group">
            <label for="progress_percent">Progress percent</label>
            <input id="progress_percent" name="progress_percent" type="number" min="0" max="100">
        </div>
        <div class="field-group">
            <label for="quantity_done">Quantity done</label>
            <input id="quantity_done" name="quantity_done" type="number" min="0" step="0.01">
        </div>
        <div class="field-group">
            <label for="quantity_unit">Quantity unit</label>
            <input id="quantity_unit" name="quantity_unit">
        </div>
        <div class="field-group">
            <label for="update_notes">Notes</label>
            <textarea id="update_notes" name="notes"></textarea>
        </div>
        <button type="submit">Add update</button>
    </form>

    <div class="table-wrap" style="margin-top: 20px;">
        <table>
            <thead><tr><th>Type</th><th>Status</th><th>Progress</th><th>Notes</th></tr></thead>
            <tbody>
                @forelse ($task->updates as $update)
                    <tr>
                        <td>{{ str_replace('_', ' ', ucfirst($update->update_type)) }}</td>
                        <td>{{ $update->status_from || $update->status_to ? ($update->status_from ?? 'new').' -> '.($update->status_to ?? 'none') : 'No status change' }}</td>
                        <td>{{ $update->progress_percent !== null ? $update->progress_percent.'%' : 'Not set' }}</td>
                        <td>{{ $update->notes ?? 'Not set' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No updates yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
