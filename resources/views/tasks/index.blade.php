@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tasks"></i> Tasks & Activities</h1>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Task
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Priority</label>
                            <select name="priority" class="form-control">
                                <option value="">All</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Field</label>
                            <select name="field_id" class="form-control">
                                <option value="">All Fields</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" {{ request('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Type</label>
                            <select name="task_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="planting" {{ request('task_type') == 'planting' ? 'selected' : '' }}>Planting</option>
                                <option value="harvesting" {{ request('task_type') == 'harvesting' ? 'selected' : '' }}>Harvesting</option>
                                <option value="irrigation" {{ request('task_type') == 'irrigation' ? 'selected' : '' }}>Irrigation</option>
                                <option value="fertilizing" {{ request('task_type') == 'fertilizing' ? 'selected' : '' }}>Fertilizing</option>
                                <option value="pest_control" {{ request('task_type') == 'pest_control' ? 'selected' : '' }}>Pest Control</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-reset"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Field</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Scheduled</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                                        <td>{{ $task->id }}</td>
                                        <td>
                                            <a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
                                            @if($task->isOverdue())
                                                <span class="badge bg-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</span>
                                        </td>
                                        <td>{{ $task->field->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $task->status_color }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->priority_color }}">{{ ucfirst($task->priority) }}</span>
                                        </td>
                                        <td>{{ $task->scheduled_date->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($task->status !== 'completed')
                                                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Mark Complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
