@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">User</p>
            <h1>{{ $user ? 'Edit user' : 'New user' }}</h1>
        </div>
        <a class="button secondary" href="{{ route('access.users.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $user ? route('access.users.update', $user) : route('access.users.store') }}">
        @csrf
        @if ($user)
            @method('PUT')
        @endif

        <div class="field-group">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $user?->name) }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" required>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" @required(! $user)>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $user?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="organization_id">Organization</label>
            <select id="organization_id" name="organization_id" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) old('organization_id', $membership?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
            @error('organization_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="farm_id">Farm scope</label>
            <select id="farm_id" name="farm_id">
                <option value="">All farms in organization</option>
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((string) old('farm_id', $membership?->farm_id) === (string) $farm->id)>{{ $farm->name }} - {{ $farm->organization->name }}</option>
                @endforeach
            </select>
            @error('farm_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" required>
                <option value="">Select role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id', $membership?->role_id) === (string) $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save user</button>
    </form>
@endsection
