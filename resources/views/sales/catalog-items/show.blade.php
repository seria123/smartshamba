@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>{{ $item->name }}</h1></div><a class="button secondary" href="{{ route('sales.catalog-items.edit', $item) }}">Edit</a></header>
    <p>{{ $item->category }} / {{ $item->unit }} / {{ $item->currency }} {{ number_format((float) $item->default_unit_price, 2) }}</p>
    <h2>Recent usage</h2><table><thead><tr><th>Sale</th><th>Description</th><th>Quantity</th><th>Total</th></tr></thead><tbody>@foreach($item->lines->take(10) as $line)<tr><td><a href="{{ route('sales.records.show',$line->record) }}">{{ $line->record?->sale_number }}</a></td><td>{{ $line->description }}</td><td>{{ $line->quantity }} {{ $line->unit }}</td><td>{{ number_format($line->line_total_amount,2) }}</td></tr>@endforeach</tbody></table>
@endsection
