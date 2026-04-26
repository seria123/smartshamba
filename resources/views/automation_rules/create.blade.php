@extends('layouts.MainLayout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Create Automation Rule</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('automation_rules.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="field_id" class="form-label">Field</label>
                            <select name="field_id" id="field_id" class="form-select @error('field_id') is-invalid @enderror" required>
                                <option value="">Select a field</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}">{{ $field->name }}</option>
                                @endforeach
                            </select>
                            @error('field_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Rule Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sensor_type" class="form-label">Sensor Type</label>
                            <select name="sensor_type" id="sensor_type" class="form-select @error('sensor_type') is-invalid @enderror" required>
                                <option value="">Select sensor type</option>
                                @foreach($sensorTypes as $type)
                                    <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                            @error('sensor_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="operator" class="form-label">Operator</label>
                                    <select name="operator" id="operator" class="form-select @error('operator') is-invalid @enderror" required>
                                        <option value="<">Less than (<)</option>
                                        <option value="<=">Less than or equal (<=)</option>
                                        <option value=">">Greater than (>)</option>
                                        <option value=">=">Greater than or equal (>=)</option>
                                        <option value="==">Equal (==)</option>
                                        <option value="!=">Not equal (!=)</option>
                                    </select>
                                    @error('operator')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="threshold" class="form-label">Threshold Value</label>
                                    <input type="number" step="0.01" name="threshold" id="threshold" class="form-control @error('threshold') is-invalid @enderror" required>
                                    @error('threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="action" class="form-label">Action</label>
                            <select name="action" id="action" class="form-select @error('action') is-invalid @enderror" required>
                                <option value="">Select action</option>
                                @foreach($actions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('action')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cooldown_minutes" class="form-label">Cooldown (minutes)</label>
                                    <input type="number" name="cooldown_minutes" id="cooldown_minutes" class="form-control" value="30" min="1" max="1440">
                                    <small class="text-muted">Minimum time between actions</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5>Example Automation Rules:</h5>
                            <ul class="text-muted">
                                <li>If soil moisture < 30% → Turn irrigation ON 💧</li>
                                <li>If temperature > 35°C → Activate cooling ❄️</li>
                                <li>If humidity < 40% → Turn irrigation ON 💧</li>
                                <li>If light intensity > 80000 lux → Activate shade ☂️</li>
                                <li>If rain detected = true → Turn irrigation OFF 🚫</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('automation_rules.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Rule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
