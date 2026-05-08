@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Asset health</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Asset</th><th>Category</th><th>Location</th><th>Status</th><th>Open Breakdowns</th><th>Last Service</th><th>Next Service</th><th>Usage Count</th><th>Maintenance Cost</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['asset'] }}</td><td>{{ $row['category'] }}</td><td>{{ $row['location'] }}</td><td>{{ $row['status'] }}</td><td>{{ $row['open_breakdowns'] }}</td><td>{{ $row['last_service_date'] ?? '—' }}</td><td>{{ $row['next_service_date'] ?? '—' }}</td><td>{{ $row['usage_count'] }}</td><td>KES {{ number_format($row['maintenance_cost'], 2) }}</td></tr>
        @empty
            <tr><td colspan="9">No assets found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
