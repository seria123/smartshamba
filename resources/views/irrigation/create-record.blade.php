@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tint"></i> Create Irrigation Record</h1>
                <a href="{{ route('irrigation.logs') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Logs
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Irrigation Zone: {{ $irrigation->name }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('irrigation.store-record', $irrigation) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="started_at" class="form-label">Date & Time Started *</label>
                                    <input type="datetime-local" class="form-control @error('started_at') is-invalid @enderror" 
                                           id="started_at" name="started_at" required>
                                    @error('started_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ended_at" class="form-label">Date & Time Ended</label>
                                    <input type="datetime-local" class="form-control @error('ended_at') is-invalid @enderror" 
                                           id="ended_at" name="ended_at">
                                    @error('ended_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="irrigation_method" class="form-label">Irrigation Method *</label>
                                    <select class="form-control @error('irrigation_method') is-invalid @enderror" 
                                            id="irrigation_method" name="irrigation_method" required>
                                        <option value="">Select Method</option>
                                        @foreach($methods as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('irrigation_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="water_source" class="form-label">Water Source *</label>
                                    <select class="form-control @error('water_source') is-invalid @enderror" 
                                            id="water_source" name="water_source" required>
                                        <option value="">Select Water Source</option>
                                        @foreach($sources as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('water_source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" name="duration_minutes" min="0" value="{{ old('duration_minutes') }}">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="water_used_liters" class="form-label">Water Used (liters)</label>
                                    <input type="number" class="form-control @error('water_used_liters') is-invalid @enderror" 
                                           id="water_used_liters" name="water_used_liters" step="0.01" min="0" value="{{ old('water_used_liters') }}">
                                    @error('water_used_liters')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estimated_volume_liters" class="form-label">Estimated Volume (liters)</label>
                                    <input type="number" class="form-control @error('estimated_volume_liters') is-invalid @enderror" 
                                           id="estimated_volume_liters" name="estimated_volume_liters" step="0.01" min="0" value="{{ old('estimated_volume_liters') }}">
                                    @error('estimated_volume_liters')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost (KES)</label>
                                    <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                           id="cost" name="cost" step="0.01" min="0" value="{{ old('cost') }}">
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost_type" class="form-label">Cost Type</label>
                                    <select class="form-control @error('cost_type') is-invalid @enderror" 
                                            id="cost_type" name="cost_type">
                                        <option value="">Select Cost Type</option>
                                        @foreach($costTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('cost_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Record
                            </button>
                            <a href="{{ route('irrigation.logs') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection