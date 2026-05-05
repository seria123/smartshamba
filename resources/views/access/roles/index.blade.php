@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Users and permissions</p>
            <h1>Roles</h1>
        </div>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Key</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td><a href="{{ route('access.roles.show', $role) }}">{{ $role->name }}</a></td>
                        <td>{{ $role->key }}</td>
                        <td>{{ $role->permissions_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
