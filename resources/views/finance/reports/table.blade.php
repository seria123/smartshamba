@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>{{ $title }}</h1></div></header>
    <table><thead><tr><th>Label</th><th>Total</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ str_replace('_',' ',$row->label) }}</td><td>{{ number_format($row->total, 2) }}</td></tr>@empty<tr><td colspan="2">No confirmed costs for this report.</td></tr>@endforelse</tbody></table>
@endsection
