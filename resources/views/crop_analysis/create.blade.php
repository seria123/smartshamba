@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-camera"></i> AI Crop Analysis</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>How it works:</strong> Upload a photo of your crop, and our AI will analyze it to detect 
                        diseases, pest infestations, and other issues. Supported issues include leaf blight, powdery mildew, 
                        bacterial spot, root rot, aphid infestation, spider mites, and nutrient deficiencies.
                    </div>

                    <form action="{{ route('crop_analysis.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="analysisForm">
                        @csrf

                        <div class="mb-4">
                            <label for="image" class="form-label">Upload Crop Image</label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       accept="image/*"
                                       class="d-none"
                                       required>
                                <div class="text-center p-4" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="mb-1">Click to upload or drag and drop</p>
                                    <small class="text-muted">PNG, JPG, GIF, WebP (Max 10MB)</small>
                                </div>
                                <div class="preview-area d-none" id="previewArea">
                                    <img id="imagePreview" class="img-fluid rounded" alt="Preview">
                                    <button type="button" class="btn btn-sm btn-danger remove-image" id="removeImage">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                            @error('image')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="field_id" class="form-label">Associated Field (Optional)</label>
                            <select name="field_id" id="field_id" class="form-select">
                                <option value="">Select a field</option>
                                @foreach($fields as $field)
                                <option value="{{ $field->id }}">{{ $field->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Link this analysis to a specific field for better tracking.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn" disabled>
                                <i class="fas fa-brain"></i> Analyze Crop
                            </button>
                            <a href="{{ route('crop_analysis.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-lightbulb"></i> Tips for Best Results</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Take photos in good natural lighting</li>
                        <li>Focus on the affected area of the plant</li>
                        <li>Include both healthy and diseased parts for comparison</li>
                        <li>Avoid blurry or dark images</li>
                        <li>Take multiple photos from different angles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewArea = document.getElementById('previewArea');
    const imagePreview = document.getElementById('imagePreview');
    const removeImage = document.getElementById('removeImage');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                uploadPlaceholder.classList.add('d-none');
                previewArea.classList.remove('d-none');
                analyzeBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-primary');
    });

    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('border-primary');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-primary');
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });

    // Remove image
    removeImage.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        imagePreview.src = '';
        previewArea.classList.add('d-none');
        uploadPlaceholder.classList.remove('d-none');
        analyzeBtn.disabled = true;
    });
});
</script>

<style>
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}
.upload-area:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}
.preview-area {
    position: relative;
    padding: 1rem;
}
.preview-area img {
    max-height: 300px;
    object-fit: contain;
}
.remove-image {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
}
</style>
@endpush

