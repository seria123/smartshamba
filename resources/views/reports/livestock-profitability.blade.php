@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Livestock profitability</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Target</th><th>Type</th><th>Species / Breed</th><th>Farm / Paddock</th><th>Cost</th><th>Revenue</th><th>Margin</th><th>Yield Qty</th><th>Status</th><th>Mortality</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['target'] }}</td><td>{{ $row['type'] }}</td><td>{{ $row['species'] ?: '—' }}</td><td>{{ $row['farm'] ?: '—' }}</td><td>KES {{ number_format($row['costs'], 2) }}</td><td>KES {{ number_format($row['revenue'], 2) }}</td><td>KES {{ number_format($row['margin'], 2) }}</td><td>{{ number_format($row['yield_quantity'], 2) }}</td><td>{{ $row['status'] }}</td><td>{{ $row['mortality'] ? 'Yes' : 'No' }}</td></tr>
        @empty
            <tr><td colspan="10">No livestock profitability data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
