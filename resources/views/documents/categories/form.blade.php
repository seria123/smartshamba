@csrf
@if($errors->any())<div class="module-card">@foreach($errors->all() as $error)<p class="error">{{ $error }}</p>@endforeach</div>@endif
<div class="form-panel">
    <label>Organization<select name="organization_id"><option value="">System / all organizations</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id', $category->organization_id) == $organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
    <label>Name<input name="name" value="{{ old('name', $category->name) }}" required></label>
    <label>Slug<input name="slug" value="{{ old('slug', $category->slug) }}"></label>
    <label>Description<textarea name="description">{{ old('description', $category->description) }}</textarea></label>
    <label>Sort order<input name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></label>
    <label><input type="hidden" name="is_active" value="0"><input style="width:auto;" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))> Active</label>
    <div class="actions"><button class="button" type="submit">Save category</button><a class="button secondary" href="{{ route('documents.categories.index') }}">Cancel</a></div>
</div>
