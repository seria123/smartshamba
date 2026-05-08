@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>{{ $customer->name }}</h1></div><a class="button secondary" href="{{ route('sales.customers.edit', $customer) }}">Edit</a></header>
    <p>{{ $customer->customer_type }} {{ $customer->phone ? ' / '.$customer->phone : '' }}</p>
    <h2>Recent sales</h2><table><thead><tr><th>Date</th><th>No.</th><th>Status</th><th>Total</th></tr></thead><tbody>@foreach($customer->records as $sale)<tr><td>{{ $sale->sale_date?->toDateString() }}</td><td><a href="{{ route('sales.records.show',$sale) }}">{{ $sale->sale_number }}</a></td><td>{{ $sale->status }}</td><td>{{ $sale->currency }} {{ number_format($sale->total_amount,2) }}</td></tr>@endforeach</tbody></table>
@endsection
