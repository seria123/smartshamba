@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Core admin</p>
            <h1>Core dashboard</h1>
            <p class="lede">Foundation records for organizations, farm structure, and module activation.</p>
        </div>
    </header>

    <section class="summary-grid" aria-label="Core summary">
        @foreach ($counts as $label => $count)
            <article class="summary-card">
                <strong>{{ $count }}</strong>
                <span>{{ $label }}</span>
            </article>
        @endforeach
    </section>

    <section class="actions">
        <a class="button" href="{{ route('admin.core.sites.create') }}">New site</a>
        <a class="button" href="{{ route('admin.core.fields.create') }}">New field</a>
        <a class="button" href="{{ route('admin.core.paddocks.create') }}">New paddock</a>
        <a class="button" href="{{ route('admin.core.warehouses.create') }}">New warehouse</a>
    </section>
@endsection
