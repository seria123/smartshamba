@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $breed->species?->name }}</p><h1>{{ $breed->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('livestock.breeds.index') }}">Back</a><a class="button" href="{{ route('livestock.breeds.edit',$breed) }}">Edit</a><form method="POST" action="{{ route('livestock.breeds.deactivate',$breed) }}">@csrf<button>Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Species</dt><dd>{{ $breed->species?->name }}</dd></div><div><dt>Code</dt><dd>{{ $breed->code ?? '-' }}</dd></div><div><dt>Scope</dt><dd>{{ $breed->organization?->name ?? 'System' }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($breed->status) }}</dd></div><div><dt>Description</dt><dd>{{ $breed->description ?? 'None' }}</dd></div></dl>
@endsection
