<div class="field-group">
    <label for="start_date">Start date</label>
    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $record?->start_date?->format('Y-m-d')) }}">
    @error('start_date') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="due_date">Due date</label>
    <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $record?->due_date?->format('Y-m-d')) }}">
    @error('due_date') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes">{{ old('notes', $record?->notes) }}</textarea>
    @error('notes') <span class="error">{{ $message }}</span> @enderror
</div>
