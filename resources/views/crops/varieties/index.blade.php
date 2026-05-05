@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crop master</p><h1>Varieties</h1></div><a class="button" href="{{ route('crops.varieties.create') }}">New variety</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Crop</th><th>Code</th><th>Days</th><th>Status</th><th></th></tr></thead><tbody>@forelse($varieties as $variety)<tr><td><a href="{{ route('crops.varieties.show',$variety) }}">{{ $variety->name }}</a></td><td>{{ $variety->crop->name }}</td><td>{{ $variety->code }}</td><td>{{ $variety->expected_growing_days }}</td><td>{{ ucfirst($variety->status) }}</td><td><a href="{{ route('crops.varieties.edit',$variety) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No varieties found.</td></tr>@endforelse</tbody></table></div>{{ $varieties->links() }}
@endsection
