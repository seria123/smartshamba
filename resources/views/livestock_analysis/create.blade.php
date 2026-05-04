@extends('layouts.MainLayout')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                <i class="fas fa-cow text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Livestock Disease Analysis</h1>
                <p class="text-gray-600 mt-1">AI-powered health assessment for your animals</p>
            </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-amber-500 to-amber-600 rounded-full mt-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Info Banner -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-900 mb-1">How It Works</h3>
                    <p class="text-amber-800 text-sm leading-relaxed">
                        Upload a clear photo of your livestock. Our AI will analyze the image to detect 
                        potential diseases, health issues, and provide treatment recommendations to keep 
                        your animals healthy and thriving.
                    </p>
                </div>
            </div>

            <!-- Analysis Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-camera text-amber-600"></i>
                        Upload Animal Image
                    </h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('livestock-analysis.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="analysisForm">
                        @csrf

                        <!-- Livestock Selection -->
                        <div class="mb-6">
                            <label for="livestock_id" class="form-label">
                                <i class="fas fa-cow text-gray-400 me-2"></i>Select Animal (Optional)
                            </label>
                            <select name="livestock_id" 
                                    id="livestock_id" 
                                    class="form-select-modern @error('livestock_id') is-invalid @enderror">
                                <option value="">-- General Analysis (no specific animal) --</option>
                                @foreach($livestock as $animal)
                                    <option value="{{ $animal->id }}" 
                                            {{ old('livestock_id', $selectedLivestockId) == $animal->id ? 'selected' : '' }}>
                                        {{ $animal->tag_number ?? 'Untagged' }} • 
                                        {{ $animal->type->name ?? 'Unknown Type' }} 
                                        • {{ $animal->breed ?? 'Unknown Breed' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('livestock_id')
                                <div class="text-red-600 text-sm mt-1.5 flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1.5">
                                <i class="fas fa-link text-gray-400"></i>
                                Selecting an animal automatically creates a health record if issues are detected
                            </p>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-6">
                            <label class="form-label">Animal Photo *</label>
                            
                            <div class="upload-area" id="uploadArea">
                                <input type="file" 
                                       name="image" 
                                       id="imageInput"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       class="d-none"
                                       required>
                                
                                <!-- Upload Placeholder -->
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-amber-600"></i>
                                        </div>
                                        <p class="text-gray-700 font-medium mb-1">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500">JPEG, PNG, JPG, GIF, WebP (Max 10MB)</p>
                                    </div>
                                </div>

                                <!-- Image Preview + Preview Image -->
                                <div class="preview-area d-none" id="previewArea">
                                    <div class="preview-container">
                                        <img id="preview" src="#" alt="Preview" class="preview-main-image">
                                        <button type="button" 
                                                class="btn-remove-image" 
                                                id="removeImageBtn">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            @error('image')
                            <div class="text-red-600 text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror

                            <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                <span>Min dimensions: 100x100px. Clear, well-lit photos yield best results</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" 
                                    class="btn-primary flex-1 flex items-center justify-center gap-2"
                                    id="analyzeBtn">
                                <i class="fas fa-brain"></i>
                                <span>Analyze Image</span>
                            </button>
                            
                            <a href="{{ route('livestock-analysis.index') }}" 
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
                        <i class="fas fa-camera text-amber-600"></i>
                        Photography Tips
                    </h3>
                </div>
                
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-sun text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Use good lighting; avoid shadows</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-search text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Focus on visible symptoms (skin, eyes, coat)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-user text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Include the whole animal for context</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-ban text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Avoid blurry, dark, or grainy images</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-repeat text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Take multiple angles if needed</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-stethoscope text-amber-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Capture close-ups of any visible issues</span>
                        </li>
                    </ul>

                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Common Issues Detected</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Respiratory</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Digestive</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Skin Conditions</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Parasites</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Infections</span>
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">Nutritional</span>
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
    const previewImg = document.getElementById('preview');
    const removeBtn = document.getElementById('removeImageBtn');
    const analyzeBtn = document.getElementById('analyzeBtn');

    let selectedFile = null;
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    // Upload area click handler
    uploadArea.addEventListener('click', function(e) {
        if (e.target === uploadArea || e.target.closest('.upload-placeholder')) {
            fileInput.click();
        }
    });

    // File selection handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            validateAndAddFile(file);
        }
    });

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-amber-500', 'bg-amber-50');
        uploadArea.classList.remove('border-gray-300');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-amber-500', 'bg-amber-50');
        uploadArea.classList.add('border-gray-300');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-amber-500', 'bg-amber-50');
        uploadArea.classList.add('border-gray-300');
        
        const file = Array.from(e.dataTransfer.files).find(f => f.type.startsWith('image/'));
        if (file) {
            validateAndAddFile(file);
            fileInput.files = e.dataTransfer.files;
        }
    });

    // Validate and add file
    function validateAndAddFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file (JPEG, PNG, JPG, GIF, WebP)');
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            alert('File exceeds 10MB limit. Please choose a smaller image.');
            return;
        }

        selectedFile = file;
        updatePreview();
        analyzeBtn.disabled = false;
    }

    // Update preview display
    function updatePreview() {
        if (selectedFile) {
            uploadPlaceholder.classList.add('d-none');
            previewArea.classList.remove('d-none');
            analyzeBtn.disabled = false;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(selectedFile);
        } else {
            uploadPlaceholder.classList.remove('d-none');
            previewArea.classList.add('d-none');
            analyzeBtn.disabled = true;
        }
    }

    // Remove image
    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        selectedFile = null;
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
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    @apply border-amber-500 bg-amber-50;
}

.upload-placeholder {
    @apply w-full;
}

.preview-area {
    @apply w-full h-full flex items-center justify-center bg-gray-50 rounded-xl;
    min-height: 300px;
}

.preview-container {
    @apply relative max-w-full max-h-full p-4;
}

.preview-main-image {
    @apply max-w-full max-h-[350px] rounded-lg shadow-lg object-contain;
}

.btn-remove-image {
    @apply absolute top-2 right-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 
           text-white rounded-lg text-sm font-medium transition-colors duration-200
           shadow-md flex items-center gap-1;
}

/* Ensure proper focus states */
.form-select-modern:focus,
input:focus {
    @apply ring-2 ring-amber-500 ring-offset-2 border-amber-500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .preview-main-image {
        max-h: 250px;
    }
}
</style>
@endpush
