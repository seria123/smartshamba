@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Work order</p>
            <h1>{{ $workOrder ? 'Edit work order' : 'New work order' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('tasks.work-orders.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $workOrder ? route('tasks.work-orders.update', $workOrder) : route('tasks.work-orders.store') }}">
        @csrf
        @if ($workOrder)
            @method('PUT')
        @endif

        @include('tasks::partials.scope-fields', ['record' => $workOrder])
        @include('tasks::partials.location-fields', ['record' => $workOrder])

        <div class="field-group">
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title', $workOrder?->title) }}" required>
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $workOrder?->description) }}</textarea>
            @error('description') <span class="error">{{ $message }}</span> @enderror
        </div>

        @include('tasks::partials.classification-fields', ['record' => $workOrder, 'statuses' => ['draft', 'planned', 'assigned', 'in_progress', 'submitted', 'approved', 'completed', 'cancelled', 'rejected', 'needs_correction']])

        <div class="field-group">
            <label for="requested_by">Requested by</label>
            <select id="requested_by" name="requested_by">
                <option value="">Not set</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('requested_by', $workOrder?->requested_by) === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('requested_by') <span class="error">{{ $message }}</span> @enderror
        </div>

        @include('tasks::partials.date-notes-fields', ['record' => $workOrder])

        <button type="submit">Save work order</button>
    </form>
@endsection
