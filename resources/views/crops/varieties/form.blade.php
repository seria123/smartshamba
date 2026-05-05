@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Variety</p><h1>{{ $variety ? 'Edit variety' : 'New variety' }}</h1></div><a class="button secondary" href="{{ route('crops.varieties.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $variety ? route('crops.varieties.update',$variety) : route('crops.varieties.store') }}">@csrf @if($variety) @method('PUT') @endif
        <div class="field-group"><label for="crop_id">Crop</label><select id="crop_id" name="crop_id" required><option value="">Select crop</option>@foreach($crops as $crop)<option value="{{ $crop->id }}" @selected((string)old('crop_id',$variety?->crop_id)===(string)$crop->id)>{{ $crop->name }}</option>@endforeach</select>@error('crop_id')<span class="error">{{ $message }}</span>@enderror</div>
        @foreach(['name'=>'Name','code'=>'Code','seed_rate_unit'=>'Seed rate unit'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$variety?->$field) }}" @if($field==='name') required @endif></div>@endforeach
        <div class="field-group"><label for="expected_growing_days">Expected growing days</label><input id="expected_growing_days" name="expected_growing_days" type="number" min="1" value="{{ old('expected_growing_days',$variety?->expected_growing_days) }}"></div>
        <div class="field-group"><label for="seed_rate">Seed rate</label><input id="seed_rate" name="seed_rate" type="number" min="0" step="0.01" value="{{ old('seed_rate',$variety?->seed_rate) }}"></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive'] as $status)<option value="{{ $status }}" @selected(old('status',$variety?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save variety</button>
    </form>
@endsection
