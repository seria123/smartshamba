@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>Cost centres</h1></div><a class="button" href="{{ route('finance.cost-centres.create') }}">New cost centre</a></header>
    <table><thead><tr><th>Name</th><th>Code</th><th>Type</th><th>Organization</th><th>Farm</th><th>Status</th><th>Actions</th></tr></thead><tbody>@foreach($centres as $centre)<tr><td>{{ $centre->name }}</td><td>{{ $centre->code }}</td><td>{{ $centre->centre_type }}</td><td>{{ $centre->organization?->name }}</td><td>{{ $centre->farm?->name ?? 'All farms' }}</td><td>{{ $centre->is_active ? 'active' : 'inactive' }}</td><td><a href="{{ route('finance.cost-centres.show',$centre) }}">View</a></td></tr>@endforeach</tbody></table>{{ $centres->links() }}
@endsection
