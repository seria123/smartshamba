@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-camera"></i> AI Crop Disease Analysis</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>How it works:</strong> Upload one or more photos of your crop, and our AI will analyze them to detect 
                        diseases, pest infestations, and other issues. Supported issues include leaf blight, powdery mildew, 
                        bacterial spot, root rot, aphid infestation, spider mites, and nutrient deficiencies.
                    </div>

                    <form action="{{ route('crop_analyses.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="analysisForm">
                        @csrf

                        <!-- Multiple Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">Upload Crop Images</label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" 
                                       name="images[]" 
                                       id="imageInput"
                                       accept="image/*"
                                       class="d-none"
                                       multiple
                                       required>
                                <div class="text-center p-4" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="mb-1">Click to upload or drag and drop</p>
                                    <small class="text-muted">PNG, JPG, GIF, WebP (Max 10MB each). Multiple images supported.</small>
                                </div>
                                <div class="preview-area d-none" id="previewArea">
                                    <div class="image-gallery" id="imageGallery"></div>
                                    <button type="button" class="btn btn-sm btn-danger remove-all-images" id="removeAllImages">
                                        <i class="fas fa-times"></i> Clear All
                                    </button>
                                </div>
                            </div>
                            @error('images.*')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tip: Upload multiple images from different angles for better accuracy.</small>
                        </div>

                        <!-- Field Selection -->
                        <div class="mb-4">
                            <label for="field_id" class="form-label">Associated Field (Optional)</label>
                            <select name="field_id" id="field_id" class="form-select">
                                <option value="">Select a field</option>
                                @foreach($fields as $field)
                                <option value="{{ $field->id }}" {{ old('field_id', request('field_id')) == $field->id ? 'selected' : '' }}>
                                    {{ $field->name }} ({{ $field->location ?? 'No location' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Link this analysis to a specific field for better tracking and weather context.</small>
                        </div>

                        <!-- Crop Cycle Selection -->
                        <div class="mb-4">
                            <label for="crop_cycle_id" class="form-label">Crop Cycle (Optional)</label>
                            <select name="crop_cycle_id" id="crop_cycle_id" class="form-select">
                                <option value="">Select a crop cycle</option>
                                @foreach($cropCycles as $cycle)
                                <option value="{{ $cycle->id }}" {{ old('crop_cycle_id', request('crop_cycle_id')) == $cycle->id ? 'selected' : '' }}>
                                    {{ $cycle->crop->name ?? $cycle->crop_name }} 
                                    ({{ $cycle->field->name ?? 'No field' }})
                                    - {{ $cycle->start_date?->format('M Y') ?? 'No start date' }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Link this analysis to a specific crop cycle to track disease history over time.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn" disabled>
                                <i class="fas fa-brain"></i> Analyze Crops
                            </button>
                            <a href="{{ route('crop_analyses.index') }}" class="btn btn-outline-secondary">
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
                        <li>Include close-ups of symptoms and wider shots of the whole plant</li>
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
    const fileInput = document.getElementById('imageInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewArea = document.getElementById('previewArea');
    const imageGallery = document.getElementById('imageGallery');
    const removeAllBtn = document.getElementById('removeAllImages');
    const analyzeBtn = document.getElementById('analyzeBtn');

    let selectedFiles = [];

    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            addFiles(files);
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
        
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        if (files.length > 0) {
            addFiles(files);
            // Update file input
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }
    });

    // Add files to selection
    function addFiles(files) {
        selectedFiles = [...selectedFiles, ...files];
        updatePreview();
        analyzeBtn.disabled = selectedFiles.length === 0;
    }

    // Update preview
    function updatePreview() {
        if (selectedFiles.length > 0) {
            uploadPlaceholder.classList.add('d-none');
            previewArea.classList.remove('d-none');
            
            imageGallery.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-item position-relative d-inline-block m-1';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function() {
                        selectedFiles.splice(index, 1);
                        // Rebuild file input
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(f => dataTransfer.items.add(f));
                        fileInput.files = dataTransfer.files;
                        updatePreview();
                        analyzeBtn.disabled = selectedFiles.length === 0;
                    };
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    imageGallery.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        } else {
            uploadPlaceholder.classList.remove('d-none');
            previewArea.classList.add('d-none');
        }
    }

    // Remove all images
    removeAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        selectedFiles = [];
        fileInput.value = '';
        updatePreview();
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
.preview-item {
    border-radius: 4px;
    overflow: hidden;
}
</style>
@endpush
