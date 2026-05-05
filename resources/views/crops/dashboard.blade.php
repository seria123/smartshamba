@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crops</p><h1>Crops dashboard</h1></div><div class="actions"><a class="button" href="{{ route('crops.cycles.create') }}">New cycle</a><a class="button secondary" href="{{ route('crops.crops.index') }}">Crop master</a></div></header>
    <section class="summary-grid">
        <article class="summary-card"><span>Crop master</span><strong>{{ $cropCount }}</strong></article>
        <article class="summary-card"><span>Seasons</span><strong>{{ $seasonCount }}</strong></article>
        <article class="summary-card"><span>Active cycles</span><strong>{{ $activeCycleCount }}</strong></article>
        <article class="summary-card"><span>Activities</span><strong>{{ $activityCount }}</strong></article>
    </section>
    <div class="actions"><a class="button secondary" href="{{ route('crops.varieties.index') }}">Varieties</a><a class="button secondary" href="{{ route('crops.seasons.index') }}">Seasons</a><a class="button secondary" href="{{ route('crops.cycles.index') }}">Crop cycles</a></div>
@endsection
