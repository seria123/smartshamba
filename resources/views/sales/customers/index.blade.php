@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>Customers</h1></div><a class="button" href="{{ route('sales.customers.create') }}">New customer</a></header>
    <table><thead><tr><th>Name</th><th>Type</th><th>Phone</th><th>Farm</th><th>Status</th><th>Action</th></tr></thead><tbody>@foreach($customers as $customer)<tr><td>{{ $customer->name }}</td><td>{{ $customer->customer_type }}</td><td>{{ $customer->phone }}</td><td>{{ $customer->farm?->name ?? 'All farms' }}</td><td>{{ $customer->is_active ? 'active' : 'inactive' }}</td><td><a href="{{ route('sales.customers.show', $customer) }}">View</a></td></tr>@endforeach</tbody></table>{{ $customers->links() }}
@endsection
