@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Assets / Maintenance</p><h1>Asset categories</h1></div><a class="button" href="{{ route('assets.categories.create') }}">New category</a></header>
    <table><thead><tr><th>Name</th><th>Parent</th><th>Status</th><th>Assets</th><th>Actions</th></tr></thead><tbody>@foreach($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->parent?->name ?? 'None' }}</td><td>{{ $category->status }}</td><td>{{ $category->assets_count }}</td><td><a href="{{ route('assets.categories.show',$category) }}">View</a></td></tr>@endforeach</tbody></table>{{ $categories->links() }}
@endsection
