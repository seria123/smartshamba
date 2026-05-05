@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Users and permissions</p>
            <h1>Users</h1>
        </div>
        <a class="button" href="{{ route('access.users.create') }}">New user</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Membership</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td><a href="{{ route('access.users.show', $user) }}">{{ $user->name }}</a></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->status) }}</td>
                        <td>
                            @foreach ($user->memberships as $membership)
                                {{ $membership->organization->name }} / {{ $membership->role?->name ?? $membership->role_key }}<br>
                            @endforeach
                        </td>
                        <td><a href="{{ route('access.users.edit', $user) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection
