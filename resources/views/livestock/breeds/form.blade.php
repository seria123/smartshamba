@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Livestock breeds</p><h1>{{ $breed ? 'Edit breed' : 'New breed' }}</h1></div><a class="button secondary" href="{{ route('livestock.breeds.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $breed ? route('livestock.breeds.update',$breed) : route('livestock.breeds.store') }}">@csrf @if($breed) @method('PUT') @endif
        <div class="field-group"><label for="organization_id">Scope</label><select id="organization_id" name="organization_id"><option value="">System-level breed</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected((string)old('organization_id',$breed?->organization_id)===(string)$organization->id)>{{ $organization->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="species_id">Species</label><select id="species_id" name="species_id" required>@foreach($species as $item)<option value="{{ $item->id }}" @selected((string)old('species_id',$breed?->species_id)===(string)$item->id)>{{ $item->name }}</option>@endforeach</select>@error('species_id')<span class="error">{{ $message }}</span>@enderror</div>
        @foreach(['name'=>'Name','code'=>'Code'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$breed?->$field) }}" @if($field==='name') required @endif>@error($field)<span class="error">{{ $message }}</span>@enderror</div>@endforeach
        <div class="field-group"><label for="description">Description</label><textarea id="description" name="description">{{ old('description',$breed?->description) }}</textarea></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive','archived'] as $status)<option value="{{ $status }}" @selected(old('status',$breed?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save breed</button>
    </form>
@endsection
