@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-camera text-primary me-2"></i>New Livestock Analysis</h2>
        <a href="{{ route('livestock_analysis.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Analyses
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How it works:</strong> Upload a clear photo of your livestock. Our AI will analyze the image for potential diseases and provide treatment recommendations.
                    </div>

                    <form action="{{ route('livestock_analysis.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="livestock_id" class="form-label">
                                <i class="fas fa-cow me-1 text-primary"></i> Select Livestock (Optional)
                            </label>
                            <select name="livestock_id" id="livestock_id" class="form-select @error('livestock_id') is-invalid @enderror">
                                <option value="">-- General Analysis (no specific animal) --</option>
                                @foreach($livestock as $animal)
                                    <option value="{{ $animal->id }}" {{ old('livestock_id') == $animal->id ? 'selected' : '' }}>
                                        {{ $animal->tag_number ?? 'Untagged' }} - 
                                        {{ $animal->type ?? 'Unknown Type' }} 
                                        ({{ $animal->breed ?? 'Unknown Breed' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Select a specific animal to automatically create a disease record if an issue is detected.
                            </small>
                            @error('livestock_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-1 text-primary"></i> Upload Image *
                            </label>
                            <input type="file" 
                                   name="image" 
                                   id="image" 
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   required>

                            <div class="form-text">
                                Max: 10MB. Supported: JPEG, PNG, JPG, GIF, WebP. Min dimensions: 100x100px.
                            </div>

                            <div class="mt-3">
                                <img id="preview" src="#" alt="Preview" class="img-thumbnail d-none" style="max-height: 300px;">
                            </div>

                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-1"></i> Analyze Image
                            </button>
                            <a href="{{ route('livestock_analysis.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-lightbulb text-warning me-2"></i>Tips for Better Analysis</h5>
                    <ul class="mb-0">
                        <li>Take clear, well-lit photos of the affected area</li>
                        <li>Include the entire animal if possible for context</li>
                        <li>Avoid blurry or dark images</li>
                        <li>Focus on visible symptoms: skin, eyes, coat, posture, discharge</li>
                        <li>Take multiple angles if needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('preview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
