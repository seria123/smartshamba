@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Asset category</p><h1>{{ $category->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('assets.categories.edit',$category) }}">Edit</a><form method="POST" action="{{ route('assets.categories.deactivate',$category) }}">@csrf<button class="secondary">Deactivate</button></form></div></header>
    <section class="detail-grid"><article><strong>Status</strong><p>{{ $category->status }}</p></article><article><strong>Organization</strong><p>{{ $category->organization?->name ?? 'System default' }}</p></article><article><strong>Parent</strong><p>{{ $category->parent?->name ?? 'None' }}</p></article><article><strong>Assets</strong><p>{{ $category->assets_count }}</p></article></section>
    <p>{{ $category->description }}</p>
@endsection
