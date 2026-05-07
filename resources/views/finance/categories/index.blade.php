@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>Cost categories</h1></div><a class="button" href="{{ route('finance.categories.create') }}">New category</a></header>
    <table><thead><tr><th>Name</th><th>Code</th><th>Nature</th><th>Source</th><th>Scope</th><th>Status</th><th>Actions</th></tr></thead><tbody>@foreach($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->code }}</td><td>{{ $category->cost_nature }}</td><td>{{ $category->default_source_module }}</td><td>{{ $category->farm?->name ?? $category->organization?->name ?? 'Global' }}</td><td>{{ $category->is_active ? 'active' : 'inactive' }}</td><td><a href="{{ route('finance.categories.show',$category) }}">View</a></td></tr>@endforeach</tbody></table>{{ $categories->links() }}
@endsection
