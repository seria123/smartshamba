@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Product categories</h1></div><a class="button" href="{{ route('inventory.categories.create') }}">New category</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Scope</th><th>Parent</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse ($categories as $category)
            <tr><td><a href="{{ route('inventory.categories.show', $category) }}">{{ $category->name }}</a></td><td>{{ $category->organization?->name ?? 'System' }}</td><td>{{ $category->parent?->name ?? 'None' }}</td><td>{{ ucfirst($category->status) }}</td><td><a href="{{ route('inventory.categories.edit', $category) }}">Edit</a></td></tr>
        @empty <tr><td colspan="5">No categories found.</td></tr> @endforelse
    </tbody></table></div>{{ $categories->links() }}
@endsection
