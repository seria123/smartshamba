@extends('layouts.MainLayout')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                <i class="fas fa-camera text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crop Disease Analysis</h1>
                <p class="text-gray-600 mt-1">AI-powered detection for healthy harvests</p>
            </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full mt-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Info Banner -->
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-emerald-900 mb-1">How It Works</h3>
                    <p class="text-emerald-800 text-sm leading-relaxed">
                        Upload clear photos of your crops. Our AI analyzes images to detect diseases, 
                        pest infestations, and nutrient deficiencies, then provides tailored recommendations 
                        for treatment and prevention.
                    </p>
                </div>
            </div>

            <!-- Analysis Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-upload text-emerald-600"></i>
                        Upload Images
                    </h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('crop_analyses.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="analysisForm">
                        @csrf

                        <!-- Image Upload Area -->
                        <div class="mb-6">
                            <label class="form-label">Crop Photos</label>
                            
                            <div class="upload-area" id="uploadArea">
                                <input type="file" 
                                       name="images[]" 
                                       id="imageInput"
                                       accept="image/*"
                                       class="d-none"
                                       multiple
                                       required>
                                
                                <!-- Upload Placeholder -->
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-emerald-600"></i>
                                        </div>
                                        <p class="text-gray-700 font-medium mb-1">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500">PNG, JPG, GIF, WebP (Max 10MB each). Multiple images supported.</p>
                                    </div>
                                </div>

                                <!-- Image Preview Area -->
                                <div class="preview-area d-none" id="previewArea">
                                    <div class="image-gallery" id="imageGallery"></div>
                                    <button type="button" 
                                            class="btn-remove-all" 
                                            id="removeAllImages">
                                        <i class="fas fa-times"></i> Clear All Images
                                    </button>
                                </div>
                            </div>
                            
                            @error('images.*')
                            <div class="text-red-600 text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                            
                            <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                <span>Tip: Upload multiple angles for accurate diagnosis</span>
                            </div>
                        </div>

                        <!-- Field Selection -->
                        <div class="mb-6">
                            <label for="field_id" class="form-label">Associated Field</label>
                            <select name="field_id" 
                                    id="field_id" 
                                    class="form-select-modern">
                                <option value="">Select a field (optional)</option>
                                @foreach($fields as $field)
                                <option value="{{ $field->id }}" 
                                        {{ old('field_id', request('field_id')) == $field->id ? 'selected' : '' }}>
                                    {{ $field->name }} 
                                    @if($field->location)
                                    • {{ $field->location }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1.5">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                Linking to a field adds weather context to your analysis
                            </p>
                        </div>

                        <!-- Crop Cycle Selection -->
                        <div class="mb-6">
                            <label for="crop_cycle_id" class="form-label">Crop Cycle</label>
                            <select name="crop_cycle_id" 
                                    id="crop_cycle_id" 
                                    class="form-select-modern">
                                <option value="">Select a crop cycle (optional)</option>
                                @foreach($cropCycles as $cycle)
                                <option value="{{ $cycle->id }}" 
                                        {{ old('crop_cycle_id', request('crop_cycle_id')) == $cycle->id ? 'selected' : '' }}>
                                    {{ $cycle->crop->name ?? $cycle->crop_name }} 
                                    • {{ $cycle->field->name ?? 'No field' }}
                                    • {{ $cycle->start_date?->format('M Y') ?? 'No dates' }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1.5">
                                <i class="fas fa-chart-line text-gray-400"></i>
                                Track disease history across growing seasons
                            </p>
                        </div>

                        <!-- Submit Button -->
                       <div class="flex flex-col sm:flex-row gap-3 pt-2">
    <button type="submit"
            class="btn-primary flex-1 flex items-center justify-center gap-2"
            id="analyzeBtn">

        <i class="fas fa-brain"></i>
        <span>Analyze Crops</span>
    </button>
                            
                            <a href="{{ route('crop_analyses.index') }}" 
                               class="btn-secondary flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tips Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 sticky top-24">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-600"></i>
                        Photography Tips
                    </h3>
                </div>
                
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-sun text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Use natural daylight for best results</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-crosshairs text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Focus clearly on the affected area</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-layer-group text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Capture both healthy and diseased parts</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-ban text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Avoid blurry, dark, or backlit images</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-camera text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Take multiple photos from different angles</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-search-plus text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Include close-ups of symptoms and wider plant views</span>
                        </li>
                    </ul>

                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">What We Detect</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Leaf Blight</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Powdery Mildew</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Bacterial Spot</span>
                            <span class="px-3 py-1 bg-brown-100 text-brown-700 rounded-full text-xs font-medium">Root Rot</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Aphids</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Spider Mites</span>
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">Nutrient Deficiency</span>
                        </div>
                    </div>
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
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    const MAX_FILES = 10;

    // Upload area click handler
    uploadArea.addEventListener('click', function(e) {
        if (e.target === uploadArea || e.target.closest('.upload-placeholder')) {
            fileInput.click();
        }
    });

    // File selection handler
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            addFiles(files);
        }
    });

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-emerald-500', 'bg-emerald-50');
        uploadArea.classList.remove('border-gray-300');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-emerald-500', 'bg-emerald-50');
        uploadArea.classList.add('border-gray-300');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-emerald-500', 'bg-emerald-50');
        uploadArea.classList.add('border-gray-300');
        
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        if (files.length > 0) {
            addFiles(files);
            // Sync with file input
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }
    });

    // Add files with validation
    function addFiles(files) {
        const validFiles = [];
        const errors = [];

        files.forEach(file => {
            if (!file.type.startsWith('image/')) {
                errors.push(`${file.name} is not an image`);
                return;
            }
            if (file.size > MAX_FILE_SIZE) {
                errors.push(`${file.name} exceeds 10MB limit`);
                return;
            }
            if (selectedFiles.length + validFiles.length >= MAX_FILES) {
                errors.push(`Maximum ${MAX_FILES} files allowed`);
                return;
            }
            validFiles.push(file);
        });

        if (errors.length > 0) {
            alert(errors.join('\n'));
        }

        selectedFiles = [...selectedFiles, ...validFiles];

        // Sync with file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;

        updatePreview();
        analyzeBtn.disabled = selectedFiles.length === 0;
    }

    // Update preview display
    function updatePreview() {
        if (selectedFiles.length > 0) {
            uploadPlaceholder.classList.add('d-none');
            previewArea.classList.remove('d-none');
            analyzeBtn.disabled = false;
            analyzeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            uploadPlaceholder.classList.remove('d-none');
            previewArea.classList.add('d-none');
            analyzeBtn.disabled = true;
            analyzeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        renderImageGallery();
    }

    // Render image gallery
    function renderImageGallery() {
        imageGallery.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-item';
                wrapper.dataset.index = index;

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                img.alt = `Preview ${index + 1}`;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'preview-remove';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = function(evt) {
                    evt.stopPropagation();
                    removeFile(index);
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                imageGallery.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    // Remove single file
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        
        updatePreview();
    }

    // Remove all images
    removeAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        selectedFiles = [];
        fileInput.value = '';
        updatePreview();
        analyzeBtn.disabled = true;
    });

    // Form submission handling
    document.getElementById('analysisForm').addEventListener('submit', function() {
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
    });
});
</script>
@endpush

@push('styles')
<style>
.upload-area {
    @apply border-2 border-dashed border-gray-300 rounded-2xl transition-all duration-300 cursor-pointer;
    min-height: 200px;
}

.upload-area:hover {
    @apply border-emerald-500 bg-emerald-50;
}

.upload-placeholder {
    @apply w-full;
}

.preview-area {
    @apply p-4 bg-gray-50 rounded-xl;
    min-height: 120px;
}

.image-gallery {
    @apply flex flex-wrap gap-3;
}

.preview-item {
    @apply relative group rounded-lg overflow-hidden shadow-sm border border-gray-200;
    width: 100px;
    height: 100px;
}

.preview-image {
    @apply w-full h-full object-cover;
    transition: transform 0.2s;
}

.preview-item:hover .preview-image {
    transform: scale(1.05);
}

.preview-remove {
    @apply absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 
           text-white rounded-full flex items-center justify-center
           opacity-0 group-hover:opacity-100 transition-opacity duration-200
           shadow-md text-xs;
}

.btn-remove-all {
    @apply mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 
           rounded-lg text-sm font-medium transition-colors duration-200;
}

/* Ensure proper focus states */
.form-select-modern:focus,
input:focus {
    @apply ring-2 ring-emerald-500 ring-offset-2 border-emerald-500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .preview-item {
        width: 80px;
        height: 80px;
    }
}
</style>
@endpush
