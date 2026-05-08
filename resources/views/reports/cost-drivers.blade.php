@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Cost drivers by category</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Cost Category</th><th>Total Amount</th><th>Entry Count</th><th>Average Entry</th><th>% of Total</th><th>Top Farm / Target</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['category'] }}</td><td>KES {{ number_format($row['total_amount'], 2) }}</td><td>{{ $row['entry_count'] }}</td><td>KES {{ number_format($row['average_amount'], 2) }}</td><td>{{ $row['percent'] === null ? '—' : number_format($row['percent'], 1).'%' }}</td><td>{{ $row['top_farm'] }}</td></tr>
        @empty
            <tr><td colspan="6">No confirmed cost data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
