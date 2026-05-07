@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>{{ $category->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('finance.categories.edit',$category) }}">Edit</a><form method="POST" action="{{ route('finance.categories.deactivate',$category) }}">@csrf<button class="button secondary" type="submit">Deactivate</button></form></div></header>
    <dl><dt>Code</dt><dd>{{ $category->code ?? 'None' }}</dd><dt>Nature</dt><dd>{{ $category->cost_nature }}</dd><dt>Default source</dt><dd>{{ $category->default_source_module ?? 'None' }}</dd><dt>Scope</dt><dd>{{ $category->farm?->name ?? $category->organization?->name ?? 'Global' }}</dd><dt>Status</dt><dd>{{ $category->is_active ? 'active' : 'inactive' }}</dd><dt>Description</dt><dd>{{ $category->description ?? 'None' }}</dd></dl>
@endsection
