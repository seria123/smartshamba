@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Team</p>
            <h1>{{ $team->name }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('labour.teams.index') }}">Back</a>
            <a class="button" href="{{ route('labour.teams.edit', $team) }}">Edit</a>
            @if ($team->status !== 'inactive')
                <form method="POST" action="{{ route('labour.teams.deactivate', $team) }}">
                    @csrf
                    <button type="submit">Deactivate</button>
                </form>
            @endif
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Organization</dt><dd>{{ $team->organization->name }}</dd></div>
        <div><dt>Farm</dt><dd>{{ $team->farm->name }}</dd></div>
        <div><dt>Code</dt><dd>{{ $team->code ?? 'Not set' }}</dd></div>
        <div><dt>Type</dt><dd>{{ $team->team_type }}</dd></div>
        <div><dt>Supervisor</dt><dd>{{ $team->supervisor?->name ?? 'Not set' }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($team->status) }}</dd></div>
        <div><dt>Notes</dt><dd>{{ $team->notes ?? 'Not set' }}</dd></div>
    </dl>

    <section class="content-header" style="margin-top: 28px;">
        <div>
            <p class="eyebrow">Members</p>
            <h1>Team workers</h1>
        </div>
    </section>

    <form class="form-panel" method="POST" action="{{ route('labour.teams.workers.store', $team) }}">
        @csrf
        <div class="field-group">
            <label for="labour_worker_id">Worker</label>
            <select id="labour_worker_id" name="labour_worker_id" required>
                <option value="">Select worker</option>
                @foreach ($availableWorkers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }} - {{ $worker->worker_code }}</option>
                @endforeach
            </select>
            @error('labour_worker_id') <span class="error">{{ $message }}</span> @enderror
        </div>
        <button type="submit">Add worker</button>
    </form>

    <div class="table-wrap" style="margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($team->workers as $worker)
                    <tr>
                        <td>{{ $worker->name }}</td>
                        <td>{{ $worker->worker_code }}</td>
                        <td>{{ $worker->primary_role }}</td>
                        <td>
                            <form method="POST" action="{{ route('labour.teams.workers.destroy', [$team, $worker]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No workers assigned to this team.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
