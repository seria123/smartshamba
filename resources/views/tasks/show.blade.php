@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tasks"></i> Task Details</h1>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>{{ $task->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $task->status_color }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Priority:</strong>
                                    <span class="badge bg-{{ $task->priority_color }}">{{ ucfirst($task->priority) }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Task Type:</strong>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Field:</strong>
                                    {{ $task->field->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p>{{ $task->description ?? 'No description provided.' }}</p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Scheduled Date:</strong>
                                    {{ $task->scheduled_date->format('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Completed Date:</strong>
                                    {{ $task->completed_date?->format('M d, Y H:i') ?? 'Not completed' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Assigned To:</strong>
                                    {{ $task->assignedUser->name ?? 'Unassigned' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Crop:</strong>
                                    {{ $task->crop->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Cost & Time Tracking</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Estimated Hours:</strong>
                                    <p>{{ $task->estimated_hours ?? 0 }} hours</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Actual Hours:</strong>
                                    <p>{{ $task->actual_hours ?? 0 }} hours</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estimated Cost:</strong>
                                    <p>${{ number_format($task->estimated_cost ?? 0, 2) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Actual Cost:</strong>
                                    <p>${{ number_format($task->actual_cost ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($task->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Notes</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $task->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($task->status !== 'completed')
                                    <form action="{{ route('tasks.complete', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Task
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Delete Task
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if($task->isOverdue())
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Overdue!</h5>
                        </div>
                        <div class="card-body">
                            <p>This task was scheduled for {{ $task->scheduled_date->format('M d, Y') }} and is now overdue.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
