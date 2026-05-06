@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock species</p><h1>{{ $species ? 'Edit species' : 'New species' }}</h1></div><a class="button secondary" href="{{ route('livestock.species.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $species ? route('livestock.species.update',$species) : route('livestock.species.store') }}">@csrf @if($species) @method('PUT') @endif
        <div class="field-group"><label for="organization_id">Scope</label><select id="organization_id" name="organization_id"><option value="">System-level species</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected((string)old('organization_id',$species?->organization_id)===(string)$organization->id)>{{ $organization->name }}</option>@endforeach</select></div>
        @foreach(['name'=>'Name','code'=>'Code'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$species?->$field) }}" required>@error($field)<span class="error">{{ $message }}</span>@enderror</div>@endforeach
        <div class="field-group"><label for="species_type">Type</label><select id="species_type" name="species_type">@foreach(['mammal','bird','fish','insect','other'] as $type)<option value="{{ $type }}" @selected(old('species_type',$species?->species_type ?? 'mammal')===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="description">Description</label><textarea id="description" name="description">{{ old('description',$species?->description) }}</textarea></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive','archived'] as $status)<option value="{{ $status }}" @selected(old('status',$species?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save species</button>
    </form>
@endsection
