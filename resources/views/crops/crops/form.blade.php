@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Crop master</p><h1>{{ $crop ? 'Edit crop' : 'New crop' }}</h1></div><a class="button secondary" href="{{ route('crops.crops.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $crop ? route('crops.crops.update',$crop) : route('crops.crops.store') }}">@csrf @if($crop) @method('PUT') @endif
        <div class="field-group"><label for="organization_id">Scope</label><select id="organization_id" name="organization_id"><option value="">System-level crop</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected((string)old('organization_id',$crop?->organization_id)===(string)$organization->id)>{{ $organization->name }}</option>@endforeach</select></div>
        @foreach(['name'=>'Name','code'=>'Code','crop_type'=>'Crop type','scientific_name'=>'Scientific name'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$crop?->$field) }}" @if(in_array($field,['name','code','crop_type'])) required @endif>@error($field)<span class="error">{{ $message }}</span>@enderror</div>@endforeach
        <div class="field-group"><label for="default_growing_days">Default growing days</label><input id="default_growing_days" name="default_growing_days" type="number" min="1" value="{{ old('default_growing_days',$crop?->default_growing_days) }}"></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive'] as $status)<option value="{{ $status }}" @selected(old('status',$crop?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save crop</button>
    </form>
@endsection
