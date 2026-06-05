@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Skills - {{ $staff->fullName() }}</h1>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Skill</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.skills.store', $staff) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="skill_name" class="form-label">Skill Name <span class="text-danger">*</span></label>
                                <input type="text" name="skill_name" id="skill_name" class="form-input-modern" required>
                            </div>
                            <div class="mb-3">
                                <label for="skill_category" class="form-label">Category</label>
                                <select name="skill_category" id="skill_category" class="form-select-modern">
                                    @foreach($skillCategories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="proficiency_level" class="form-label">Proficiency <span class="text-danger">*</span></label>
                                <select name="proficiency_level" id="proficiency_level" class="form-select-modern">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="certification" class="form-label">Certification</label>
                                <input type="text" name="certification" id="certification" class="form-input-modern">
                            </div>
                            <div class="mb-3">
                                <label for="certification_expiry" class="form-label">Certification Expiry</label>
                                <input type="date" name="certification_expiry" id="certification_expiry" class="form-input-modern">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i> Add Skill
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Skills List</h5>
                    </div>
                    <div class="card-body">
                        @if($staff->skills->count() > 0)
                        <div class="row">
                            @foreach($staff->skills as $skill)
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $skill->skill_name }}</h6>
                                                <small class="text-muted">{{ str_replace('_', ' ', ucwords($skill->skill_category ?? 'general')) }}</small>
                                            </div>
                                            <span class="badge bg-{{ $skill->proficiency_level === 'expert' ? 'success' : ($skill->proficiency_level === 'advanced' ? 'primary' : ($skill->proficiency_level === 'intermediate' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($skill->proficiency_level) }}
                                            </span>
                                        </div>
                                        @if($skill->certification)
                                        <div class="mt-2">
                                            <small><i class="fas fa-certificate me-1"></i>{{ $skill->certification }}</small>
                                        </div>
                                        @endif
                                        <form action="{{ route('staff.skills.destroy', [$staff, $skill]) }}" method="POST" class="mt-2">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this skill?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted text-center py-4">No skills recorded yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
