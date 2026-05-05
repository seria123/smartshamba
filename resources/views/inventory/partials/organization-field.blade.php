<div class="field-group">
    <label for="organization_id">Organization</label>
    <select id="organization_id" name="organization_id" @if($required ?? true) required @endif>
        <option value="">System-level / select organization</option>
        @foreach ($organizations as $organization)
            <option value="{{ $organization->id }}" @selected((string) old('organization_id', $record?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
        @endforeach
    </select>
    @error('organization_id') <span class="error">{{ $message }}</span> @enderror
</div>
