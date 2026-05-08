@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Inventory snapshot</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Product</th><th>Category</th><th>Location</th><th>Quantity</th><th>Unit</th><th>Unit Cost</th><th>Value</th><th>Expiry</th><th>Low Stock</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['product'] }}</td><td>{{ $row['category'] }}</td><td>{{ $row['location'] }}</td><td>{{ number_format($row['quantity'], 2) }}</td><td>{{ $row['unit'] }}</td><td>{{ $row['currency'] }} {{ number_format($row['unit_cost'], 2) }}</td><td>{{ $row['currency'] }} {{ number_format($row['value'], 2) }}</td><td>{{ $row['expiry_date'] ?? '—' }}</td><td>{{ $row['low_stock'] ? 'Yes' : 'No' }}</td></tr>
        @empty
            <tr><td colspan="9">No inventory stock lots found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
