@extends('layouts.MainLayout')

@section('title', 'Wound Analysis - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Analyze Livestock Wound</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wounds.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="livestock_id" class="form-label">Livestock (Optional)</label>
                                    <select class="form-select" id="livestock_id" name="livestock_id">
                                        <option value="">Select Livestock</option>
                                        @foreach($livestock as $animal)
                                        <option value="{{ $animal->id }}">{{ $animal->name }} ({{ $animal->tag_number }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Optional: Associate with a specific animal</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Wound Photo <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" capture="environment" required>
                                    <small class="text-muted">Take a clear photo of the wound area. Ensure good lighting.</small>
                                </div>
                            </div>
                        </div>

                        <!-- AI Photo Analysis Preview -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">AI Wound Analysis</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">Take a photo and click "Analyze" to use AI for wound assessment after form submission.</p>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            The AI will identify wound type, severity, symptoms, and provide a treatment plan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wounds.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-camera"></i> Analyze Wound
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
