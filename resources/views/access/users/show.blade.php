@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">User</p>
            <h1>{{ $user->name }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('access.users.index') }}">Back</a>
            <a class="button" href="{{ route('access.users.edit', $user) }}">Edit</a>
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($user->status) }}</dd></div>
        <div>
            <dt>Memberships</dt>
            <dd>
                @forelse ($user->memberships as $membership)
                    {{ $membership->organization->name }}
                    @if ($membership->farm)
                        / {{ $membership->farm->name }}
                    @endif
                    / {{ $membership->role?->name ?? $membership->role_key }}<br>
                @empty
                    No memberships.
                @endforelse
            </dd>
        </div>
    </dl>

    @if ($user->status === 'active')
        <form method="POST" action="{{ route('access.users.deactivate', $user) }}" style="margin-top: 18px;">
            @csrf
            <button type="submit">Deactivate user</button>
        </form>
    @endif
@endsection
