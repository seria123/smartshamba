@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Water source</p><h1>{{ $source ? 'Edit source' : 'New source' }}</h1></div><a class="button secondary" href="{{ route('irrigation.water-sources.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $source ? route('irrigation.water-sources.update',$source) : route('irrigation.water-sources.store') }}">@csrf @if($source) @method('PUT') @endif
        @include('irrigation::partials.scope-fields', ['record' => $source])
        @foreach(['name'=>'Name','code'=>'Code','capacity_unit'=>'Capacity unit','location_description'=>'Location'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$source?->$field) }}" @if(in_array($field,['name','code'])) required @endif>@error($field)<span class="error">{{ $message }}</span>@enderror</div>@endforeach
        <div class="field-group"><label for="source_type">Type</label><select id="source_type" name="source_type">@foreach(['borehole','river','dam','tank','well','municipal','rainwater','pond','other'] as $value)<option value="{{ $value }}" @selected(old('source_type',$source?->source_type ?? 'borehole')===$value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="capacity">Capacity</label><input id="capacity" name="capacity" type="number" step="0.01" min="0" value="{{ old('capacity',$source?->capacity) }}"></div>
        <div class="field-group"><label for="latitude">Latitude</label><input id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude',$source?->latitude) }}"></div>
        <div class="field-group"><label for="longitude">Longitude</label><input id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude',$source?->longitude) }}"></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive','under_maintenance','archived'] as $value)<option value="{{ $value }}" @selected(old('status',$source?->status ?? 'active')===$value)>{{ ucfirst(str_replace('_',' ',$value)) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes',$source?->notes) }}</textarea></div><button>Save source</button>
    </form>
@endsection
