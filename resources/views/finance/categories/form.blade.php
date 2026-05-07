@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Finance / Costing</p><h1>{{ $category ? 'Edit' : 'New' }} cost category</h1></div></header>
    <form method="POST" action="{{ $category ? route('finance.categories.update',$category) : route('finance.categories.store') }}">@csrf @if($category) @method('PUT') @endif
        <label>Organization<select name="organization_id"><option value="">Global</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id',$category?->organization_id)==$organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
        <label>Farm<select name="farm_id"><option value="">All farms</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(old('farm_id',$category?->farm_id)==$farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
        <label>Name<input name="name" value="{{ old('name',$category?->name) }}" required></label><label>Code<input name="code" value="{{ old('code',$category?->code) }}"></label>
        <label>Nature<select name="cost_nature">@foreach(['variable','fixed','capital','overhead','other'] as $nature)<option value="{{ $nature }}" @selected(old('cost_nature',$category?->cost_nature ?? 'other')===$nature)>{{ $nature }}</option>@endforeach</select></label>
        <label>Default source<select name="default_source_module"><option value="">None</option>@foreach(['manual','labour','inventory','crop','livestock','irrigation','asset','maintenance','task','other'] as $source)<option value="{{ $source }}" @selected(old('default_source_module',$category?->default_source_module)===$source)>{{ $source }}</option>@endforeach</select></label>
        <label>Description<textarea name="description">{{ old('description',$category?->description) }}</textarea></label><label>Sort order<input type="number" name="sort_order" value="{{ old('sort_order',$category?->sort_order ?? 0) }}"></label><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$category?->is_active ?? true))> Active</label>
        @include('finance::partials.errors')<button class="button" type="submit">Save category</button>
    </form>
@endsection
