@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>{{ $centre ? 'Edit' : 'New' }} cost centre</h1></div></header>
    <form method="POST" action="{{ $centre ? route('finance.cost-centres.update',$centre) : route('finance.cost-centres.store') }}">@csrf @if($centre) @method('PUT') @endif
        <label>Organization<select name="organization_id" required>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id',$centre?->organization_id)==$organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
        <label>Farm<select name="farm_id"><option value="">All farms</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(old('farm_id',$centre?->farm_id)==$farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
        <label>Name<input name="name" value="{{ old('name',$centre?->name) }}" required></label><label>Code<input name="code" value="{{ old('code',$centre?->code) }}"></label>
        <label>Type<select name="centre_type">@foreach(['farm','field','crop','livestock','irrigation','asset','labour','admin','project','other'] as $type)<option value="{{ $type }}" @selected(old('centre_type',$centre?->centre_type ?? 'other')===$type)>{{ $type }}</option>@endforeach</select></label>
        <label>Description<textarea name="description">{{ old('description',$centre?->description) }}</textarea></label><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$centre?->is_active ?? true))> Active</label>
        @include('finance::partials.errors')<button class="button" type="submit">Save cost centre</button>
    </form>
@endsection
