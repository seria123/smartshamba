@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>Revenue summary</h1></div></header>@include('sales::reports.partials-filters')
    <div class="stats-grid"><div class="stat-card"><span>Confirmed</span><strong>{{ number_format($summary['confirmedTotal'],2) }}</strong></div><div class="stat-card"><span>Paid</span><strong>{{ number_format($summary['paidTotal'],2) }}</strong></div><div class="stat-card"><span>Outstanding</span><strong>{{ number_format($summary['balanceTotal'],2) }}</strong></div></div>
    <div class="actions"><a class="button secondary" href="{{ route('sales.reports.customers') }}">Customers</a><a class="button secondary" href="{{ route('sales.reports.items') }}">Items</a><a class="button secondary" href="{{ route('sales.reports.crop-cycles') }}">Crop cycles</a><a class="button secondary" href="{{ route('sales.reports.livestock') }}">Livestock</a><a class="button secondary" href="{{ route('sales.reports.gross-margin') }}">Gross margin</a></div>
    <h2>By category</h2><table><thead><tr><th>Category</th><th>Total</th></tr></thead><tbody>@foreach($summary['byCategory'] as $row)<tr><td>{{ $row->label }}</td><td>{{ number_format($row->total,2) }}</td></tr>@endforeach</tbody></table>
@endsection
