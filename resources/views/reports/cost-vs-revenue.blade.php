@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Cost vs revenue by target</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Target Type</th><th>Target</th><th>Total Cost</th><th>Total Revenue</th><th>Gross Margin</th><th>Margin %</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['target_type'] }}</td><td>{{ $row['target_label'] }}</td><td>KES {{ number_format($row['cost'], 2) }}</td><td>KES {{ number_format($row['revenue'], 2) }}</td><td>KES {{ number_format($row['margin'], 2) }}</td><td>{{ $row['margin_percent'] === null ? '—' : number_format($row['margin_percent'], 1).'%' }}</td></tr>
        @empty
            <tr><td colspan="6">No target cost/revenue data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
