@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tint"></i> Edit Irrigation Zone</h1>
                <a href="{{ route('irrigation.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('irrigation.update', $irrigation) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Zone Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $irrigation->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="field_id" class="form-label">Field *</label>
                                    <select class="form-control @error('field_id') is-invalid @enderror" 
                                            id="field_id" name="field_id" required>
                                        <option value="">Select Field</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ old('field_id', $irrigation->field_id) == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description', $irrigation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="controller_id" class="form-label">Controller ID</label>
                                    <input type="text" class="form-control @error('controller_id') is-invalid @enderror" 
                                           id="controller_id" name="controller_id" value="{{ old('controller_id', $irrigation->controller_id) }}">
                                    @error('controller_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valve_id" class="form-label">Valve ID</label>
                                    <input type="text" class="form-control @error('valve_id') is-invalid @enderror" 
                                           id="valve_id" name="valve_id" value="{{ old('valve_id', $irrigation->valve_id) }}">
                                    @error('valve_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ old('status', $irrigation->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="flow_rate_lph" class="form-label">Flow Rate (LPH)</label>
                                    <input type="number" class="form-control @error('flow_rate_lph') is-invalid @enderror" 
                                           id="flow_rate_lph" name="flow_rate_lph" step="0.01" min="0" value="{{ old('flow_rate_lph', $irrigation->flow_rate_lph) }}">
                                    @error('flow_rate_lph')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Default Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" name="duration_minutes" min="1" value="{{ old('duration_minutes', $irrigation->duration_minutes) }}">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="soil_moisture_threshold" class="form-label">Moisture Threshold (%)</label>
                                    <input type="number" class="form-control @error('soil_moisture_threshold') is-invalid @enderror" 
                                           id="soil_moisture_threshold" name="soil_moisture_threshold" step="0.1" min="0" max="100" value="{{ old('soil_moisture_threshold', $irrigation->soil_moisture_threshold) }}">
                                    @error('soil_moisture_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="schedule_type" class="form-label">Schedule Type</label>
                                    <select class="form-control @error('schedule_type') is-invalid @enderror" 
                                            id="schedule_type" name="schedule_type">
                                        @foreach($scheduleTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('schedule_type', $irrigation->schedule_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('schedule_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" name="start_time" value="{{ old('start_time', $irrigation->start_time) }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" name="end_time" value="{{ old('end_time', $irrigation->end_time) }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $irrigation->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $irrigation->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Zone
                            </button>
                            <a href="{{ route('irrigation.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
