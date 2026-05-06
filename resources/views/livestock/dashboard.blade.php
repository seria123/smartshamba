@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock</p><h1>Livestock dashboard</h1></div><div class="actions"><a class="button" href="{{ route('livestock.animals.create') }}">New animal</a><a class="button secondary" href="{{ route('livestock.groups.create') }}">New group</a></div></header>
    <section class="summary-grid">
        <article class="summary-card"><span>Species</span><strong>{{ $speciesCount }}</strong></article>
        <article class="summary-card"><span>Animals</span><strong>{{ $animalCount }}</strong></article>
        <article class="summary-card"><span>Groups</span><strong>{{ $groupCount }}</strong></article>
        <article class="summary-card"><span>Treatments</span><strong>{{ $treatmentCount }}</strong></article>
        <article class="summary-card"><span>Active withdrawals</span><strong>{{ $activeWithdrawals }}</strong></article>
    </section>
    <div class="actions"><a class="button secondary" href="{{ route('livestock.species.index') }}">Species</a><a class="button secondary" href="{{ route('livestock.breeds.index') }}">Breeds</a><a class="button secondary" href="{{ route('livestock.animals.index') }}">Animals</a><a class="button secondary" href="{{ route('livestock.groups.index') }}">Animal Groups</a></div>
@endsection
