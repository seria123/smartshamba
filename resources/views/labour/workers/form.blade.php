@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Worker</p>
            <h1>{{ $worker ? 'Edit worker' : 'New worker' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('labour.workers.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $worker ? route('labour.workers.update', $worker) : route('labour.workers.store') }}">
        @csrf
        @if ($worker)
            @method('PUT')
        @endif

        <div class="field-group">
            <label for="organization_id">Organization</label>
            <select id="organization_id" name="organization_id" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) old('organization_id', $worker?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="farm_id">Farm</label>
            <select id="farm_id" name="farm_id" required>
                <option value="">Select farm</option>
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((string) old('farm_id', $worker?->farm_id) === (string) $farm->id)>{{ $farm->name }} - {{ $farm->organization->name }}</option>
                @endforeach
            </select>
            @error('farm_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="user_id">User account</label>
            <select id="user_id" name="user_id">
                <option value="">No linked user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $worker?->user_id) === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                @endforeach
            </select>
            @error('user_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="worker_code">Worker code</label>
            <input id="worker_code" name="worker_code" value="{{ old('worker_code', $worker?->worker_code) }}" required>
            @error('worker_code') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $worker?->name) }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" value="{{ old('phone', $worker?->phone) }}">
            @error('phone') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $worker?->email) }}">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="employment_type">Employment type</label>
            <input id="employment_type" name="employment_type" value="{{ old('employment_type', $worker?->employment_type ?? 'full-time') }}" required>
            @error('employment_type') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="primary_role">Primary role</label>
            <input id="primary_role" name="primary_role" value="{{ old('primary_role', $worker?->primary_role) }}" required>
            @error('primary_role') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $worker?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="start_date">Start date</label>
            <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $worker?->start_date?->format('Y-m-d')) }}">
            @error('start_date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="rate_type">Rate type</label>
            <select id="rate_type" name="rate_type">
                <option value="">Not set</option>
                @foreach (['hourly', 'daily', 'monthly', 'contract'] as $rateType)
                    <option value="{{ $rateType }}" @selected(old('rate_type', $worker?->rate_type) === $rateType)>{{ ucfirst($rateType) }}</option>
                @endforeach
            </select>
            @error('rate_type') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="default_rate">Default rate</label>
            <input id="default_rate" name="default_rate" type="number" min="0" step="0.01" value="{{ old('default_rate', $worker?->default_rate) }}">
            @error('default_rate') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes">{{ old('notes', $worker?->notes) }}</textarea>
            @error('notes') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save worker</button>
    </form>
@endsection
