@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Asset category</p><h1>{{ $category ? 'Edit category' : 'New category' }}</h1></div><a class="button secondary" href="{{ route('assets.categories.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $category ? route('assets.categories.update',$category) : route('assets.categories.store') }}">@csrf @if($category) @method('PUT') @endif
        <div class="field-group"><label for="organization_id">Organization</label><select id="organization_id" name="organization_id"><option value="">System default</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id',$category?->organization_id)==$organization->id)>{{ $organization->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="name">Name</label><input id="name" name="name" value="{{ old('name',$category?->name) }}" required>@error('name')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field-group"><label for="slug">Slug</label><input id="slug" name="slug" value="{{ old('slug',$category?->slug) }}">@error('slug')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field-group"><label for="parent_id">Parent</label><select id="parent_id" name="parent_id"><option value="">None</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected(old('parent_id',$category?->parent_id)==$parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
        <label><input type="checkbox" name="is_system" value="1" @checked(old('is_system',$category?->is_system))> System category</label>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status">@foreach(['active','inactive','archived'] as $value)<option value="{{ $value }}" @selected(old('status',$category?->status ?? 'active')===$value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="description">Description</label><textarea id="description" name="description">{{ old('description',$category?->description) }}</textarea></div><button>Save category</button>
    </form>
@endsection
