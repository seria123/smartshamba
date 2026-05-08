<form method="GET" class="form-panel" style="max-width: none; margin-bottom: 24px;">
    <div class="summary-grid" style="margin-bottom: 0;">
        <label>Organization<select name="organization_id"><option value="">All organizations</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(($filters['organization_id'] ?? null) == $organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
        <label>Farm<select name="farm_id"><option value="">All farms</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
        <label>Category<select name="attachment_category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(($filters['attachment_category_id'] ?? null) == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label>Source module<input name="attachable_module" value="{{ $filters['attachable_module'] ?? '' }}"></label>
        <label>Source type<input name="attachable_type" value="{{ $filters['attachable_type'] ?? '' }}"></label>
        <label>Search<input name="search" value="{{ $filters['search'] ?? '' }}"></label>
        <label>From<input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"></label>
        <label>To<input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"></label>
    </div>
    <div class="actions"><button class="button secondary" type="submit">Filter</button><a class="button secondary" href="{{ url()->current() }}">Reset</a></div>
</form>
