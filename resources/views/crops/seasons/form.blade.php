@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Season</p><h1>{{ $season ? 'Edit season' : 'New season' }}</h1></div><a class="button secondary" href="{{ route('crops.seasons.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $season ? route('crops.seasons.update',$season) : route('crops.seasons.store') }}">@csrf @if($season) @method('PUT') @endif
        <div class="field-group"><label for="organization_id">Organization</label><select id="organization_id" name="organization_id" required>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected((string)old('organization_id',$season?->organization_id)===(string)$organization->id)>{{ $organization->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="farm_id">Farm</label><select id="farm_id" name="farm_id"><option value="">All farms</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected((string)old('farm_id',$season?->farm_id)===(string)$farm->id)>{{ $farm->name }}</option>@endforeach</select>@error('farm_id')<span class="error">{{ $message }}</span>@enderror</div>
        @foreach(['name'=>'Name','code'=>'Code','season_type'=>'Season type'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field,$season?->$field ?? ($field==='season_type' ? 'main' : null)) }}" required></div>@endforeach
        <div class="field-group"><label for="start_date">Start date</label><input id="start_date" name="start_date" type="date" value="{{ old('start_date',$season?->start_date?->format('Y-m-d')) }}" required></div>
        <div class="field-group"><label for="end_date">End date</label><input id="end_date" name="end_date" type="date" value="{{ old('end_date',$season?->end_date?->format('Y-m-d')) }}"></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['planned','active','closed','cancelled'] as $status)<option value="{{ $status }}" @selected(old('status',$season?->status ?? 'planned')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save season</button>
    </form>
@endsection
