@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Workers and labour</p>
            <h1>Labour dashboard</h1>
            <p class="lede">Foundation records for farm workers, teams, and simple attendance.</p>
        </div>
    </header>

    <section class="summary-grid" aria-label="Labour summary">
        @foreach ($counts as $label => $count)
            <article class="summary-card">
                <strong>{{ $count }}</strong>
                <span>{{ $label }}</span>
            </article>
        @endforeach
    </section>

    <section class="actions">
        <a class="button" href="{{ route('labour.workers.index') }}">Workers</a>
        <a class="button" href="{{ route('labour.teams.index') }}">Teams</a>
        <a class="button" href="{{ route('labour.attendance.index') }}">Attendance</a>
    </section>
@endsection
