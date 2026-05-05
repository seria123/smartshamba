@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Worker</p>
            <h1>{{ $worker->name }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('labour.workers.index') }}">Back</a>
            <a class="button" href="{{ route('labour.workers.edit', $worker) }}">Edit</a>
            @if ($worker->status !== 'inactive')
                <form method="POST" action="{{ route('labour.workers.deactivate', $worker) }}">
                    @csrf
                    <button type="submit">Deactivate</button>
                </form>
            @endif
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Organization</dt><dd>{{ $worker->organization->name }}</dd></div>
        <div><dt>Farm</dt><dd>{{ $worker->farm->name }}</dd></div>
        <div><dt>User account</dt><dd>{{ $worker->user?->email ?? 'Not linked' }}</dd></div>
        <div><dt>Worker code</dt><dd>{{ $worker->worker_code }}</dd></div>
        <div><dt>Primary role</dt><dd>{{ $worker->primary_role }}</dd></div>
        <div><dt>Employment type</dt><dd>{{ $worker->employment_type }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($worker->status) }}</dd></div>
        <div><dt>Rate</dt><dd>{{ $worker->rate_type ? $worker->rate_type.' '.$worker->default_rate : 'Not set' }}</dd></div>
        <div><dt>Teams</dt><dd>{{ $worker->teams->pluck('name')->join(', ') ?: 'None' }}</dd></div>
        <div><dt>Notes</dt><dd>{{ $worker->notes ?? 'Not set' }}</dd></div>
    </dl>
@endsection
