@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crop master</p><h1>Crops</h1></div><a class="button" href="{{ route('crops.crops.create') }}">New crop</a></header>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Code</th><th>Scope</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($crops as $crop)<tr><td><a href="{{ route('crops.crops.show',$crop) }}">{{ $crop->name }}</a></td><td>{{ $crop->code }}</td><td>{{ $crop->organization?->name ?? 'System' }}</td><td>{{ $crop->crop_type }}</td><td>{{ ucfirst($crop->status) }}</td><td><a href="{{ route('crops.crops.edit',$crop) }}">Edit</a></td></tr>@empty
        <tr><td colspan="6">No crops found.</td></tr>@endforelse
    </tbody></table></div>{{ $crops->links() }}
@endsection
