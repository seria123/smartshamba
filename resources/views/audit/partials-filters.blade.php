<form method="GET" class="module-card">
    <div class="form-grid">
        <label>From <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
        <label>To <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
        <label>Organization
            <select name="organization_id">
                <option value="">All organizations</option>
                @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected(($filters['organization_id'] ?? null) == $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Farm
            <select name="farm_id">
                <option value="">All farms</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>{{ $farm->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Module <input name="module" value="{{ $filters['module'] ?? '' }}" placeholder="finance"></label>
        <label>Event <input name="event" value="{{ $filters['event'] ?? '' }}" placeholder="created"></label>
        <label>Search <input name="keyword" value="{{ $filters['keyword'] ?? $filters['search'] ?? '' }}" placeholder="actor, subject, description"></label>
    </div>
    <div class="button-row">
        <button class="button" type="submit">Apply filters</button>
        <a class="button secondary" href="{{ url()->current() }}">Reset</a>
    </div>
</form>
