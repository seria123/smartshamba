@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Customer revenue</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <div class="table-wrap"><table><thead><tr><th>Customer</th><th>Sales Count</th><th>Total Sales</th><th>Amount Paid</th><th>Outstanding</th><th>Last Sale</th><th>Top Item</th></tr></thead><tbody>
        @forelse($rows as $row)
            <tr><td>{{ $row['customer'] }}</td><td>{{ $row['sales_count'] }}</td><td>KES {{ number_format($row['total_sales'], 2) }}</td><td>KES {{ number_format($row['amount_paid'], 2) }}</td><td>KES {{ number_format($row['outstanding_amount'], 2) }}</td><td>{{ $row['last_sale_date'] }}</td><td>{{ $row['top_item'] }}</td></tr>
        @empty
            <tr><td colspan="7">No customer revenue data found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
