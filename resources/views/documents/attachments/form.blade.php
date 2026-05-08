@csrf
@if($errors->any())<div class="module-card">@foreach($errors->all() as $error)<p class="error">{{ $error }}</p>@endforeach</div>@endif
<div class="form-panel">
    @if(! $attachment->exists)<label>File<input name="file" type="file" required></label>@endif
    <label>Title<input name="title" value="{{ old('title', $attachment->title) }}" required></label>
    <label>Organization<select name="organization_id" required><option value="">Select organization</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id', $attachment->organization_id) == $organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
    <label>Farm<select name="farm_id"><option value="">General</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(old('farm_id', $attachment->farm_id) == $farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
    <label>Category<select name="attachment_category_id"><option value="">Uncategorized</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('attachment_category_id', $attachment->attachment_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <label>Source module<input name="attachable_module" value="{{ old('attachable_module', $attachment->attachable_module) }}"></label>
    <label>Source type<input name="attachable_type" value="{{ old('attachable_type', $attachment->attachable_type) }}"></label>
    <label>Source ID<input name="attachable_id" type="number" value="{{ old('attachable_id', $attachment->attachable_id) }}"></label>
    <label>Source label<input name="attachable_label" value="{{ old('attachable_label', $attachment->attachable_label) }}"></label>
    <label>Description<textarea name="description">{{ old('description', $attachment->description) }}</textarea></label>
    <div class="actions"><button class="button" type="submit">Save attachment</button><a class="button secondary" href="{{ route('documents.attachments.index') }}">Cancel</a></div>
</div>
