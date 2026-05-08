@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>Sales records</h1></div><a class="button" href="{{ route('sales.records.create') }}">New sale</a></header>
    <table><thead><tr><th>Date</th><th>No.</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th>Balance</th><th>Action</th></tr></thead><tbody>@foreach($records as $record)<tr><td>{{ $record->sale_date?->toDateString() }}</td><td>{{ $record->sale_number }}</td><td>{{ $record->customer?->name ?? 'Unspecified' }}</td><td>{{ $record->status }}</td><td>{{ $record->payment_status }}</td><td>{{ $record->currency }} {{ number_format($record->total_amount, 2) }}</td><td>{{ number_format($record->balance_amount, 2) }}</td><td><a href="{{ route('sales.records.show', $record) }}">View</a></td></tr>@endforeach</tbody></table>{{ $records->links() }}
@endsection
