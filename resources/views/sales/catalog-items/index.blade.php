@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>Catalog items</h1></div><a class="button" href="{{ route('sales.catalog-items.create') }}">New item</a></header>
    <table><thead><tr><th>Name</th><th>Category</th><th>Unit</th><th>Default price</th><th>Status</th><th>Action</th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->category }}</td><td>{{ $item->unit }}</td><td>{{ $item->currency }} {{ number_format((float) $item->default_unit_price, 2) }}</td><td>{{ $item->is_active ? 'active' : 'inactive' }}</td><td><a href="{{ route('sales.catalog-items.show', $item) }}">View</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}
@endsection
