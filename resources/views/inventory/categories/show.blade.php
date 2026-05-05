@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Category</p><h1>{{ $category->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('inventory.categories.index') }}">Back</a><a class="button" href="{{ route('inventory.categories.edit', $category) }}">Edit</a><form method="POST" action="{{ route('inventory.categories.deactivate', $category) }}">@csrf<button type="submit">Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Scope</dt><dd>{{ $category->organization?->name ?? 'System' }}</dd></div><div><dt>Slug</dt><dd>{{ $category->slug }}</dd></div><div><dt>Parent</dt><dd>{{ $category->parent?->name ?? 'None' }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($category->status) }}</dd></div></dl>
@endsection
