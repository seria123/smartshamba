@extends('layouts.app')

@section('content')
    <section class="page-header">
        <p class="eyebrow">Modular mixed-farm platform</p>
        <h1>SmartShamba foundation</h1>
        <p class="lede">
            A clean Laravel modular monolith foundation for future farm
            operations, teams, permissions, reporting, and settings.
        </p>
    </section>

    <section class="module-grid" aria-label="Planned modules">
        @foreach ([
            'Core',
            'Users and Permissions',
            'Workers',
            'Tasks',
            'Inventory',
            'Crops',
            'Livestock',
            'Irrigation',
            'Finance',
            'Sales and Traceability',
            'Reports',
            'Settings',
        ] as $module)
            <article class="module-card">
                <h2>{{ $module }}</h2>
                <p>Reserved module boundary.</p>
            </article>
        @endforeach
    </section>
@endsection
