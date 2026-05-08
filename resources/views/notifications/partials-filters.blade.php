<form method="GET" class="form-panel" style="max-width: none; margin-bottom: 24px;">
    <div class="summary-grid" style="margin-bottom: 0;">
        <label>Organization<select name="organization_id"><option value="">All organizations</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(($filters['organization_id'] ?? null) == $organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
        <label>Farm<select name="farm_id"><option value="">All farms</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
        <label>Status<select name="status"><option value="">Any status</option>@foreach(['unread','read','dismissed','resolved'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
        <label>Severity<select name="severity"><option value="">Any severity</option>@foreach(['low','medium','high','critical'] as $severity)<option value="{{ $severity }}" @selected(($filters['severity'] ?? null) === $severity)>{{ ucfirst($severity) }}</option>@endforeach</select></label>
        <label>Source<input name="source_module" value="{{ $filters['source_module'] ?? '' }}" placeholder="inventory"></label>
        <label>From<input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"></label>
        <label>To<input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"></label>
    </div>
    <div class="actions"><button class="button secondary" type="submit">Filter</button><a class="button secondary" href="{{ url()->current() }}">Reset</a></div>
</form>
