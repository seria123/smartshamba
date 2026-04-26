@extends('layouts.MainLayout')

@section('title', 'Diagnose Disease - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Diagnose Disease for {{ $livestock->name ?: 'Livestock' }}</h4>
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

                    <!-- Livestock Info -->
                    <div class="alert alert-info">
                        <h6>Livestock Information:</h6>
                        <p class="mb-1"><strong>Name:</strong> {{ $livestock->name ?: 'Unnamed' }}</p>
                        <p class="mb-1"><strong>Tag:</strong> {{ $livestock->tag_number ?: 'N/A' }}</p>
                        <p class="mb-1"><strong>Type:</strong> {{ $livestock->type->name ?? 'Unknown' }}</p>
                        <p class="mb-0"><strong>Status:</strong>
                            <span class="badge bg-{{ $livestock->status === 'healthy' ? 'success' : ($livestock->status === 'sick' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($livestock->status) }}
                            </span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('diseases.store') }}">
                        @csrf
                        <input type="hidden" name="livestock_id" value="{{ $livestock->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Disease Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="species" class="form-label">Species <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="species" name="species"
                                           value="{{ old('species', $livestock->type->name ?? '') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- AI Photo Diagnosis -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">AI Photo Diagnosis</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-3">Take a photo of the affected area and use AI to automatically detect the disease and generate recommendations.</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="disease_photo" class="form-label">Upload Disease Photo</label>
                                                    <input type="file" class="form-control" id="disease_photo" name="disease_photo" accept="image/*" capture="environment">
                                                    <small class="text-muted">Upload a clear photo of the affected area</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-outline-primary w-100" id="aiDiagnoseBtn" onclick="analyzeLivestockDisease()">
                                                        <i class="bi bi-camera"></i> Analyze with AI
                                                    </button>
                                                    <small class="text-muted d-block mt-1">Uses OpenAI Vision to detect disease</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="aiResults" class="d-none">
                                            <hr>
                                            <h6>AI Diagnosis Results:</h6>
                                            <div class="alert alert-info">
                                                <div id="aiDiseaseName"><strong>Disease:</strong> <span></span></div>
                                                <div id="aiSeverity"><strong>Severity:</strong> <span></span></div>
                                                <div id="aiDescription"><strong>Description:</strong> <span></span></div>
                                                <div id="aiRecommendation"><strong>Recommendation:</strong> <span></span></div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="populateAIData()">
                                                <i class="bi bi-arrow-down"></i> Populate Form with AI Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="severity" class="form-label">Severity</label>
                                    <select class="form-select" id="severity" name="severity">
                                        <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('severity', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="diagnosed_date" class="form-label">Diagnosed Date</label>
                                    <input type="date" class="form-control" id="diagnosed_date" name="diagnosed_date"
                                           value="{{ old('diagnosed_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cause" class="form-label">Cause <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cause" name="cause"
                                   value="{{ old('cause') }}" required>
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
                                    <textarea class="form-control" id="treatment" name="treatment" rows="2">{{ old('treatment') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mortality_rate" class="form-label">Mortality Rate (%)</label>
                                    <input type="number" step="0.1" max="100" class="form-control" id="mortality_rate" name="mortality_rate"
                                           value="{{ old('mortality_rate') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('livestock.show', $livestock) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-danger">Record Disease Diagnosis</button>
                        </div>
                    </form>

<script>
function analyzeLivestockDisease() {
    const photoInput = document.getElementById('disease_photo');
    const btn = document.getElementById('aiDiagnoseBtn');
    const resultsDiv = document.getElementById('aiResults');
    
    if (!photoInput.files || !photoInput.files[0]) {
        alert('Please select a photo first');
        return;
    }
    
    const file = photoInput.files[0];
    const reader = new FileReader();
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Analyzing...';
    btn.disabled = true;
    
    reader.onload = function(e) {
        const base64 = e.target.result.split(',')[1];
        
        fetch("{{ route('diseases.ai-analyze') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                image: base64,
                livestock_type: "{{ $livestock->type->name ?? 'livestock' }}"
            })
        })
        .then(response => response.json())
        .then(data => {
            document.querySelector('#aiDiseaseName span').textContent = data.diagnosis;
            document.querySelector('#aiSeverity span').textContent = data.severity;
            document.querySelector('#aiDescription span').textContent = data.description;
            document.querySelector('#aiRecommendation span').textContent = data.recommendation;
            resultsDiv.classList.remove('d-none');
            btn.innerHTML = '<i class="bi bi-camera"></i> Analyze with AI';
            btn.disabled = false;
        })
        .catch(err => {
            console.error(err);
            alert('Analysis failed: ' + (err.message || 'Unknown error'));
            btn.innerHTML = '<i class="bi bi-camera"></i> Analyze with AI';
            btn.disabled = false;
        });
    };
    
    reader.readAsDataURL(file);
}

function populateAIData() {
    const disease = document.querySelector('#aiDiseaseName span').textContent;
    const severity = document.querySelector('#aiSeverity span').textContent;
    const description = document.querySelector('#aiDescription span').textContent;
    const recommendation = document.querySelector('#aiRecommendation span').textContent;
    
    document.getElementById('name').value = disease;
    document.getElementById('severity').value = severity;
    document.getElementById('cause').value = disease + ' infection';
    document.getElementById('symptoms').value = description;
    document.getElementById('treatment').value = recommendation;
    document.getElementById('prevention').value = 'Isolate affected animal, maintain hygiene, monitor other livestock';
    
    alert('Form populated with AI diagnosis results!');
}
</script>
@endsection
