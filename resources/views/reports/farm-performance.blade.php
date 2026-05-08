@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Farm performance summary</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Farm</th><th>Total Costs</th><th>Total Revenue</th><th>Gross Margin</th><th>Margin %</th><th>Outstanding</th><th>Crop Cycles</th><th>Livestock</th><th>Open Tasks</th><th>Breakdowns</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['farm'] }}</td><td>KES {{ number_format($row['costs'], 2) }}</td><td>KES {{ number_format($row['revenue'], 2) }}</td><td>KES {{ number_format($row['margin'], 2) }}</td><td>{{ $row['margin_percent'] === null ? '—' : number_format($row['margin_percent'], 1).'%' }}</td><td>KES {{ number_format($row['outstanding'], 2) }}</td><td>{{ $row['active_crop_cycles'] }}</td><td>{{ $row['livestock'] }}</td><td>{{ $row['open_tasks'] }}</td><td>{{ $row['asset_breakdowns'] }}</td></tr>
        @empty
            <tr><td colspan="10">No farm performance data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
