<div class="field-group">
    <label for="category">Category</label>
    <select id="category" name="category" required>
        @foreach (['general', 'crop', 'livestock', 'irrigation', 'inventory', 'maintenance', 'harvest', 'feeding', 'treatment', 'cleaning', 'security', 'other'] as $category)
            <option value="{{ $category }}" @selected(old('category', $record?->category ?? 'general') === $category)>{{ ucfirst($category) }}</option>
        @endforeach
    </select>
    @error('category') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="priority">Priority</label>
    <select id="priority" name="priority" required>
        @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
            <option value="{{ $priority }}" @selected(old('priority', $record?->priority ?? 'normal') === $priority)>{{ ucfirst($priority) }}</option>
        @endforeach
    </select>
    @error('priority') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="status">Status</label>
    <select id="status" name="status" required>
        @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(old('status', $record?->status ?? $statuses[0]) === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
        @endforeach
    </select>
    @error('status') <span class="error">{{ $message }}</span> @enderror
</div>
