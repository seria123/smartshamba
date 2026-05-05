@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Task</p>
            <h1>{{ $task ? 'Edit task' : 'New task' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('tasks.items.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $task ? route('tasks.items.update', $task) : route('tasks.items.store') }}">
        @csrf
        @if ($task)
            @method('PUT')
        @endif

        @include('tasks::partials.scope-fields', ['record' => $task])

        <div class="field-group">
            <label for="work_order_id">Work order</label>
            <select id="work_order_id" name="work_order_id">
                <option value="">Standalone task</option>
                @foreach ($workOrders as $workOrder)
                    <option value="{{ $workOrder->id }}" @selected((string) old('work_order_id', $task?->work_order_id) === (string) $workOrder->id)>{{ $workOrder->work_order_number }} - {{ $workOrder->title }}</option>
                @endforeach
            </select>
            @error('work_order_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        @include('tasks::partials.location-fields', ['record' => $task])

        <div class="field-group">
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title', $task?->title) }}" required>
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $task?->description) }}</textarea>
            @error('description') <span class="error">{{ $message }}</span> @enderror
        </div>

        @include('tasks::partials.classification-fields', ['record' => $task, 'statuses' => ['draft', 'assigned', 'in_progress', 'submitted', 'needs_correction', 'approved', 'completed', 'cancelled', 'rejected']])

        <div class="field-group">
            <label for="supervisor_user_id">Supervisor</label>
            <select id="supervisor_user_id" name="supervisor_user_id">
                <option value="">No supervisor</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('supervisor_user_id', $task?->supervisor_user_id) === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('supervisor_user_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        @include('tasks::partials.date-notes-fields', ['record' => $task])

        <button type="submit">Save task</button>
    </form>
@endsection
