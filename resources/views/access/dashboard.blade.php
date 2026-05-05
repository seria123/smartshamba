@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Users and permissions</p>
            <h1>Access dashboard</h1>
        </div>
        <div class="actions">
            <a class="button" href="{{ route('access.users.create') }}">New user</a>
            <a class="button secondary" href="{{ route('access.roles.index') }}">Roles</a>
        </div>
    </header>

    <section class="summary-grid">
        @foreach ($counts as $label => $count)
            <article class="summary-card">
                <strong>{{ $count }}</strong>
                <span>{{ $label }}</span>
            </article>
        @endforeach
    </section>
@endsection
