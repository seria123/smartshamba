@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Tasks / Work Orders</p>
            <h1>Task dashboard</h1>
            <p class="lede">Operational work planning, assignment, progress, and approval.</p>
        </div>
    </header>

    <section class="summary-grid" aria-label="Task summary">
        @foreach ($counts as $label => $count)
            <article class="summary-card">
                <strong>{{ $count }}</strong>
                <span>{{ $label }}</span>
            </article>
        @endforeach
    </section>

    <section class="actions">
        <a class="button" href="{{ route('tasks.work-orders.index') }}">Work Orders</a>
        <a class="button" href="{{ route('tasks.items.index') }}">Tasks</a>
        <a class="button secondary" href="{{ route('tasks.items.create') }}">New Task</a>
    </section>
@endsection
