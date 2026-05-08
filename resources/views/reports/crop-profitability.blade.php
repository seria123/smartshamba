@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Crop cycle profitability</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Crop Cycle</th><th>Farm / Field</th><th>Crop</th><th>Status</th><th>Area</th><th>Cost</th><th>Revenue</th><th>Margin</th><th>Margin %</th><th>Harvest Qty</th><th>Revenue / Unit</th><th>Cost / Unit</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['label'] }}</td><td>{{ $row['farm'] }} / {{ $row['field'] }}</td><td>{{ $row['crop'] ?: '—' }}</td><td>{{ $row['status'] }}</td><td>{{ $row['area'] }}</td><td>KES {{ number_format($row['costs'], 2) }}</td><td>KES {{ number_format($row['revenue'], 2) }}</td><td>KES {{ number_format($row['margin'], 2) }}</td><td>{{ $row['margin_percent'] === null ? '—' : number_format($row['margin_percent'], 1).'%' }}</td><td>{{ number_format($row['harvest_quantity'], 2) }}</td><td>{{ $row['revenue_per_unit'] === null ? '—' : number_format($row['revenue_per_unit'], 2) }}</td><td>{{ $row['cost_per_unit'] === null ? '—' : number_format($row['cost_per_unit'], 2) }}</td></tr>
        @empty
            <tr><td colspan="12">No crop cycle profitability data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
