@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Role</p>
            <h1>{{ $role->name }}</h1>
        </div>
        <a class="button secondary" href="{{ route('access.roles.index') }}">Back</a>
    </header>

    <dl class="detail-list">
        <div><dt>Key</dt><dd>{{ $role->key }}</dd></div>
        <div><dt>Description</dt><dd>{{ $role->description }}</dd></div>
        <div>
            <dt>Permissions</dt>
            <dd>
                @foreach ($role->permissions as $permission)
                    {{ $permission->key }}<br>
                @endforeach
            </dd>
        </div>
    </dl>
@endsection
