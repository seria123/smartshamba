@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Attachments by category</h1></div></header>
    @include('documents::partials-filters')
    @include('documents::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Category</th><th>Count</th><th>Total size</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row->label }}</td><td>{{ $row->total }}</td><td>{{ number_format(($row->size ?? 0) / 1024, 1) }} KB</td></tr>@empty<tr><td colspan="3">No attachment data found.</td></tr>@endforelse</tbody></table></div>
@endsection
