@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Category</p><h1>{{ $category ? 'Edit category' : 'New category' }}</h1></div><a class="button secondary" href="{{ route('inventory.categories.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $category ? route('inventory.categories.update', $category) : route('inventory.categories.store') }}">
        @csrf @if($category) @method('PUT') @endif
        @include('inventory::partials.organization-field', ['record' => $category, 'required' => false])
        <div class="field-group"><label for="parent_id">Parent</label><select id="parent_id" name="parent_id"><option value="">No parent</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected((string)old('parent_id', $category?->parent_id)===(string)$parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="name">Name</label><input id="name" name="name" value="{{ old('name', $category?->name) }}" required>@error('name') <span class="error">{{ $message }}</span> @enderror</div>
        <div class="field-group"><label for="slug">Slug</label><input id="slug" name="slug" value="{{ old('slug', $category?->slug) }}"></div>
        <div class="field-group"><label for="description">Description</label><textarea id="description" name="description">{{ old('description', $category?->description) }}</textarea></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status" required>@foreach(['active','inactive'] as $status)<option value="{{ $status }}" @selected(old('status', $category?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save category</button>
    </form>
@endsection
