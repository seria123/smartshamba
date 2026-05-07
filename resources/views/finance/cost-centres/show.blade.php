@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>{{ $centre->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('finance.cost-centres.edit',$centre) }}">Edit</a><form method="POST" action="{{ route('finance.cost-centres.deactivate',$centre) }}">@csrf<button class="button secondary" type="submit">Deactivate</button></form></div></header>
    <dl><dt>Code</dt><dd>{{ $centre->code ?? 'None' }}</dd><dt>Type</dt><dd>{{ $centre->centre_type }}</dd><dt>Organization</dt><dd>{{ $centre->organization?->name }}</dd><dt>Farm</dt><dd>{{ $centre->farm?->name ?? 'All farms' }}</dd><dt>Status</dt><dd>{{ $centre->is_active ? 'active' : 'inactive' }}</dd><dt>Description</dt><dd>{{ $centre->description ?? 'None' }}</dd></dl>
@endsection
