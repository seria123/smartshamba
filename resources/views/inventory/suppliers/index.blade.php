@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Suppliers</h1></div><a class="button" href="{{ route('inventory.suppliers.create') }}">New supplier</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Code</th><th>Type</th><th>Phone</th><th>Status</th><th></th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td><a href="{{ route('inventory.suppliers.show',$supplier) }}">{{ $supplier->name }}</a></td><td>{{ $supplier->code ?? 'Not set' }}</td><td>{{ $supplier->supplier_type }}</td><td>{{ $supplier->phone ?? 'Not set' }}</td><td>{{ ucfirst($supplier->status) }}</td><td><a href="{{ route('inventory.suppliers.edit',$supplier) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No suppliers found.</td></tr>@endforelse</tbody></table></div>{{ $suppliers->links() }}
@endsection
