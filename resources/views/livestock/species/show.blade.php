@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $species->code }}</p><h1>{{ $species->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('livestock.species.index') }}">Back</a><a class="button" href="{{ route('livestock.species.edit',$species) }}">Edit</a><form method="POST" action="{{ route('livestock.species.deactivate',$species) }}">@csrf<button>Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Scope</dt><dd>{{ $species->organization?->name ?? 'System' }}</dd></div><div><dt>Type</dt><dd>{{ ucfirst($species->species_type) }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($species->status) }}</dd></div><div><dt>Breeds</dt><dd>{{ $species->breeds->count() }}</dd></div><div><dt>Description</dt><dd>{{ $species->description ?? 'None' }}</dd></div></dl>
@endsection
