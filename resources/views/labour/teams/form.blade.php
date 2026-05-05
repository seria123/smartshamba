@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Team</p>
            <h1>{{ $team ? 'Edit team' : 'New team' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('labour.teams.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $team ? route('labour.teams.update', $team) : route('labour.teams.store') }}">
        @csrf
        @if ($team)
            @method('PUT')
        @endif

        <div class="field-group">
            <label for="organization_id">Organization</label>
            <select id="organization_id" name="organization_id" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) old('organization_id', $team?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="farm_id">Farm</label>
            <select id="farm_id" name="farm_id" required>
                <option value="">Select farm</option>
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((string) old('farm_id', $team?->farm_id) === (string) $farm->id)>{{ $farm->name }} - {{ $farm->organization->name }}</option>
                @endforeach
            </select>
            @error('farm_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $team?->name) }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $team?->code) }}">
            @error('code') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="team_type">Team type</label>
            <input id="team_type" name="team_type" value="{{ old('team_type', $team?->team_type ?? 'general') }}" required>
            @error('team_type') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="supervisor_worker_id">Supervisor</label>
            <select id="supervisor_worker_id" name="supervisor_worker_id">
                <option value="">No supervisor</option>
                @foreach ($workers as $worker)
                    <option value="{{ $worker->id }}" @selected((string) old('supervisor_worker_id', $team?->supervisor_worker_id) === (string) $worker->id)>{{ $worker->name }} - {{ $worker->farm->name }}</option>
                @endforeach
            </select>
            @error('supervisor_worker_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $team?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes">{{ old('notes', $team?->notes) }}</textarea>
            @error('notes') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save team</button>
    </form>
@endsection
