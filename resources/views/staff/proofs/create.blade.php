@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Upload Proof of Work - {{ $staff->fullName() }}</h1>
            <a href="{{ route('staff.proofs.index', $staff) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-modern p-6">
            <form action="{{ route('staff.proofs.store', $staff) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="task_id" class="form-label">Related Task</label>
                        <select name="task_id" id="task_id" class="form-select-modern">
                            <option value="">-- Select Task --</option>
                            @foreach($tasks as $task)
                            <option value="{{ $task->id }}">{{ $task->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="field_id" class="form-label">Field</label>
                        <select name="field_id" id="field_id" class="form-select-modern">
                            <option value="">-- Select Field --</option>
                            @foreach($fields as $field)
                            <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="proof_type" class="form-label">Proof Type <span class="text-danger">*</span></label>
                        <select name="proof_type" id="proof_type" class="form-select-modern" required>
                            <option value="">-- Select Type --</option>
                            <option value="before_photo">Before Photo</option>
                            <option value="after_photo">After Photo</option>
                            <option value="progress_photo">Progress Photo</option>
                            <option value="damage_photo">Damage Photo</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea-modern" rows="3" placeholder="Describe what the photo shows..."></textarea>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="taken_at" class="form-label">Date/Time Taken</label>
                        <input type="datetime-local" name="taken_at" id="taken_at" class="form-input-modern">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="photo" class="form-label">Photo <span class="text-danger">*</span></label>
                        <input type="file" name="photo" id="photo" class="form-input-modern" accept="image/*" required>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('staff.proofs.index', $staff) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
