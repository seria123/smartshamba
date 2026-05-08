@csrf
@if($errors->any())<div class="module-card">@foreach($errors->all() as $error)<p class="error">{{ $error }}</p>@endforeach</div>@endif
<div class="form-panel">
    <label>Organization<select name="organization_id" required><option value="">Select organization</option>@foreach($organizations as $organization)<option value="{{ $organization->id }}" @selected(old('organization_id', $rule->organization_id) == $organization->id)>{{ $organization->name }}</option>@endforeach</select></label>
    <label>Farm<select name="farm_id"><option value="">Organization-wide</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(old('farm_id', $rule->farm_id) == $farm->id)>{{ $farm->name }}</option>@endforeach</select></label>
    <label>Code<input name="code" value="{{ old('code', $rule->code) }}" required></label>
    <label>Name<input name="name" value="{{ old('name', $rule->name) }}" required></label>
    <label>Description<textarea name="description">{{ old('description', $rule->description) }}</textarea></label>
    <label>Source module<input name="source_module" value="{{ old('source_module', $rule->source_module) }}" placeholder="inventory" required></label>
    <label>Signal type<input name="signal_type" value="{{ old('signal_type', $rule->signal_type) }}" placeholder="inventory.low_stock" required></label>
    <label>Severity<select name="severity">@foreach(['low','medium','high','critical'] as $severity)<option value="{{ $severity }}" @selected(old('severity', $rule->severity ?: 'medium') === $severity)>{{ ucfirst($severity) }}</option>@endforeach</select></label>
    <label>Settings JSON<textarea name="settings">{{ old('settings', $rule->settings ? json_encode($rule->settings, JSON_PRETTY_PRINT) : '') }}</textarea></label>
    <label><input type="hidden" name="is_active" value="0"><input style="width: auto;" type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->exists ? $rule->is_active : true))> Active</label>
    <div class="actions"><button class="button" type="submit">Save rule</button><a class="button secondary" href="{{ route('notifications.rules.index') }}">Cancel</a></div>
</div>
