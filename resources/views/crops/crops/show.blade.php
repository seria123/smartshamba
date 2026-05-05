@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $crop->code }}</p><h1>{{ $crop->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('crops.crops.index') }}">Back</a><a class="button" href="{{ route('crops.crops.edit',$crop) }}">Edit</a><form method="POST" action="{{ route('crops.crops.deactivate',$crop) }}">@csrf<button>Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Scope</dt><dd>{{ $crop->organization?->name ?? 'System' }}</dd></div><div><dt>Type</dt><dd>{{ $crop->crop_type }}</dd></div><div><dt>Scientific name</dt><dd>{{ $crop->scientific_name ?? 'None' }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($crop->status) }}</dd></div><div><dt>Varieties</dt><dd>{{ $crop->varieties->count() }}</dd></div></dl>
@endsection
