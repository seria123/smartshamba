@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Products</h1></div><a class="button" href="{{ route('inventory.products.create') }}">New product</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Code</th><th>Category</th><th>Type</th><th>Unit</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($products as $product)<tr><td><a href="{{ route('inventory.products.show',$product) }}">{{ $product->name }}</a></td><td>{{ $product->code }}</td><td>{{ $product->category->name }}</td><td>{{ $product->product_type }}</td><td>{{ $product->unit_of_measure }}</td><td>{{ ucfirst($product->status) }}</td><td><a href="{{ route('inventory.products.edit',$product) }}">Edit</a></td></tr>
        @empty <tr><td colspan="7">No products found.</td></tr> @endforelse
    </tbody></table></div>{{ $products->links() }}
@endsection
