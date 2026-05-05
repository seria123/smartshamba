@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $season->code }}</p><h1>{{ $season->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('crops.seasons.index') }}">Back</a><a class="button" href="{{ route('crops.seasons.edit',$season) }}">Edit</a><form method="POST" action="{{ route('crops.seasons.close',$season) }}">@csrf<button>Close</button></form></div></header>
    <dl class="detail-list"><div><dt>Farm</dt><dd>{{ $season->farm?->name ?? 'All farms' }}</dd></div><div><dt>Type</dt><dd>{{ $season->season_type }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($season->status) }}</dd></div><div><dt>Cycles</dt><dd>{{ $season->cycles->count() }}</dd></div></dl>
@endsection
