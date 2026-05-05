@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Attendance</p>
            <h1>{{ $attendance ? 'Edit attendance' : 'Record attendance' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('labour.attendance.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $attendance ? route('labour.attendance.update', $attendance) : route('labour.attendance.store') }}">
        @csrf
        @if ($attendance)
            @method('PUT')
        @endif

        <div class="field-group">
            <label for="organization_id">Organization</label>
            <select id="organization_id" name="organization_id" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) old('organization_id', $attendance?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="farm_id">Farm</label>
            <select id="farm_id" name="farm_id" required>
                <option value="">Select farm</option>
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((string) old('farm_id', $attendance?->farm_id) === (string) $farm->id)>{{ $farm->name }} - {{ $farm->organization->name }}</option>
                @endforeach
            </select>
            @error('farm_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="labour_worker_id">Worker</label>
            <select id="labour_worker_id" name="labour_worker_id" required>
                <option value="">Select worker</option>
                @foreach ($workers as $worker)
                    <option value="{{ $worker->id }}" @selected((string) old('labour_worker_id', $attendance?->labour_worker_id) === (string) $worker->id)>{{ $worker->name }} - {{ $worker->farm->name }}</option>
                @endforeach
            </select>
            @error('labour_worker_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="labour_team_id">Team</label>
            <select id="labour_team_id" name="labour_team_id">
                <option value="">No team</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" @selected((string) old('labour_team_id', $attendance?->labour_team_id) === (string) $team->id)>{{ $team->name }} - {{ $team->farm->name }}</option>
                @endforeach
            </select>
            @error('labour_team_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="date">Date</label>
            <input id="date" name="date" type="date" value="{{ old('date', $attendance?->date?->format('Y-m-d') ?? now()->toDateString()) }}" required>
            @error('date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['present', 'absent', 'leave', 'sick', 'half-day'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $attendance?->status ?? 'present') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="check_in_at">Check in</label>
            <input id="check_in_at" name="check_in_at" type="time" value="{{ old('check_in_at', $attendance?->check_in_at) }}">
            @error('check_in_at') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="check_out_at">Check out</label>
            <input id="check_out_at" name="check_out_at" type="time" value="{{ old('check_out_at', $attendance?->check_out_at) }}">
            @error('check_out_at') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="hours_worked">Hours worked</label>
            <input id="hours_worked" name="hours_worked" type="number" min="0" max="24" step="0.01" value="{{ old('hours_worked', $attendance?->hours_worked) }}">
            @error('hours_worked') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes">{{ old('notes', $attendance?->notes) }}</textarea>
            @error('notes') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save attendance</button>
    </form>
@endsection
