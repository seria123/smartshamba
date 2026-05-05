<div class="field-group">
    <label for="site_id">Site</label>
    <select id="site_id" name="site_id">
        <option value="">No site</option>
        @foreach ($sites as $site)
            <option value="{{ $site->id }}" @selected((string) old('site_id', $record?->site_id) === (string) $site->id)>{{ $site->name }} - {{ $site->farm->name }}</option>
        @endforeach
    </select>
    @error('site_id') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="field_id">Field</label>
    <select id="field_id" name="field_id">
        <option value="">No field</option>
        @foreach ($fields as $field)
            <option value="{{ $field->id }}" @selected((string) old('field_id', $record?->field_id) === (string) $field->id)>{{ $field->name }} - {{ $field->farm->name }}</option>
        @endforeach
    </select>
    @error('field_id') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="paddock_id">Paddock</label>
    <select id="paddock_id" name="paddock_id">
        <option value="">No paddock</option>
        @foreach ($paddocks as $paddock)
            <option value="{{ $paddock->id }}" @selected((string) old('paddock_id', $record?->paddock_id) === (string) $paddock->id)>{{ $paddock->name }} - {{ $paddock->farm->name }}</option>
        @endforeach
    </select>
    @error('paddock_id') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="field-group">
    <label for="warehouse_id">Warehouse</label>
    <select id="warehouse_id" name="warehouse_id">
        <option value="">No warehouse</option>
        @foreach ($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $record?->warehouse_id) === (string) $warehouse->id)>{{ $warehouse->name }} - {{ $warehouse->farm->name }}</option>
        @endforeach
    </select>
    @error('warehouse_id') <span class="error">{{ $message }}</span> @enderror
</div>
