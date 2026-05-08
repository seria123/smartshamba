@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Monthly trends</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Month</th><th>Costs</th><th>Revenue</th><th>Payments Received</th><th>Outstanding Created</th><th>Gross Margin</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['month'] }}</td><td>KES {{ number_format($row['costs'], 2) }}</td><td>KES {{ number_format($row['revenue'], 2) }}</td><td>KES {{ number_format($row['payments'], 2) }}</td><td>KES {{ number_format($row['outstanding'], 2) }}</td><td>KES {{ number_format($row['margin'], 2) }}</td></tr>
        @empty
            <tr><td colspan="6">No monthly trend data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
