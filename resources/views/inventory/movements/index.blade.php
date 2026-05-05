@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Movement history</h1></div></header>
    <div class="table-wrap"><table><thead><tr><th>Number</th><th>Type</th><th>Date</th><th>Product</th><th>Quantity</th><th>From</th><th>To</th><th>Total</th></tr></thead><tbody>@forelse($movements as $movement)<tr><td>{{ $movement->movement_number }}</td><td>{{ str_replace('_',' ',ucfirst($movement->movement_type)) }}</td><td>{{ $movement->date->format('Y-m-d') }}</td><td>{{ $movement->product->name }}</td><td>{{ $movement->quantity }} {{ $movement->unit }}</td><td>{{ $movement->fromWarehouse?->name ?? $movement->warehouse?->name ?? 'None' }}</td><td>{{ $movement->toWarehouse?->name ?? 'None' }}</td><td>{{ $movement->total_cost }}</td></tr>@empty<tr><td colspan="8">No movements found.</td></tr>@endforelse</tbody></table></div>{{ $movements->links() }}
@endsection
