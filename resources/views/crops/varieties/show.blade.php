@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $variety->crop->name }}</p><h1>{{ $variety->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('crops.varieties.index') }}">Back</a><a class="button" href="{{ route('crops.varieties.edit',$variety) }}">Edit</a><form method="POST" action="{{ route('crops.varieties.deactivate',$variety) }}">@csrf<button>Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Code</dt><dd>{{ $variety->code ?? 'None' }}</dd></div><div><dt>Growing days</dt><dd>{{ $variety->expected_growing_days ?? 'Not set' }}</dd></div><div><dt>Seed rate</dt><dd>{{ $variety->seed_rate }} {{ $variety->seed_rate_unit }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($variety->status) }}</dd></div></dl>
@endsection
