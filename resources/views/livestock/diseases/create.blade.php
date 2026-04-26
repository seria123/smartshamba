@extends('layouts.MainLayout')

@section('title', 'Add Disease - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add Disease Record</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('diseases.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Disease Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="species" class="form-label">Species <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="species" name="species" value="{{ old('species') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="severity" class="form-label">Severity</label>
                                    <select class="form-select" id="severity" name="severity">
                                        <option value="low" {{ old('severity', 'low') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('severity', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="treated" {{ old('status') == 'treated' ? 'selected' : '' }}>Treated</option>
                                        <option value="chronic" {{ old('status') == 'chronic' ? 'selected' : '' }}>Chronic</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="diagnosed_date" class="form-label">Diagnosed Date</label>
                                    <input type="date" class="form-control" id="diagnosed_date" name="diagnosed_date" value="{{ old('diagnosed_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mortality_rate" class="form-label">Mortality Rate (%)</label>
                                    <input type="number" step="0.1" max="100" class="form-control" id="mortality_rate" name="mortality_rate" value="{{ old('mortality_rate') }}">
                                </div>
                            </div>
                        </div>



                        <div class="mb-3">
                            <label for="cause" class="form-label">Cause <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cause" name="cause" value="{{ old('cause') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="symptoms" class="form-label">Symptoms <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="symptoms" name="symptoms" rows="3" required>{{ old('symptoms') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transmission" class="form-label">Transmission</label>
                                    <textarea class="form-control" id="transmission" name="transmission" rows="2">{{ old('transmission') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prevention" class="form-label">Prevention</label>
                                    <textarea class="form-control" id="prevention" name="prevention" rows="2">{{ old('prevention') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="treatment" class="form-label">Treatment</label>
                                    <textarea class="form-control" id="treatment" name="treatment" rows="3">{{ old('treatment') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="treated_by" class="form-label">Treated By</label>
                                    <input type="number" class="form-control" id="treated_by" name="treated_by" value="{{ old('treated_by') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('diseases.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-danger">Save Disease Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection