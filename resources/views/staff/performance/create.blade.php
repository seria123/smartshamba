@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">New Performance Review - {{ $staff->fullName() }}</h1>
            <a href="{{ route('staff.performance.index', $staff) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-modern p-6">
            <form action="{{ route('staff.performance.store', $staff) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label for="review_period_start" class="form-label">Period Start <span class="text-danger">*</span></label>
                        <input type="date" name="review_period_start" id="review_period_start" class="form-input-modern" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="review_period_end" class="form-label">Period End <span class="text-danger">*</span></label>
                        <input type="date" name="review_period_end" id="review_period_end" class="form-input-modern" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="reviewed_by" class="form-label">Reviewed By</label>
                        <select name="reviewed_by" id="reviewed_by" class="form-select-modern">
                            <option value="">-- Select Reviewer --</option>
                            @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}">{{ $reviewer->fullName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="farm_id" class="form-label">Farm <span class="text-danger">*</span></label>
                        <select name="farm_id" id="farm_id" class="form-select-modern" required>
                            @foreach($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="tasks_completed" class="form-label">Tasks Completed <span class="text-danger">*</span></label>
                        <input type="number" name="tasks_completed" id="tasks_completed" class="form-input-modern" min="0" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="tasks_assigned" class="form-label">Tasks Assigned <span class="text-danger">*</span></label>
                        <input type="number" name="tasks_assigned" id="tasks_assigned" class="form-input-modern" min="0" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="attendance_rate" class="form-label">Attendance Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" name="attendance_rate" id="attendance_rate" class="form-input-modern" min="0" max="100" step="0.1" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="work_quality_score" class="form-label">Quality Score (0-100)</label>
                        <input type="number" name="work_quality_score" id="work_quality_score" class="form-input-modern" min="0" max="100" step="0.1">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="efficiency_rating" class="form-label">Efficiency Rating (0-100)</label>
                        <input type="number" name="efficiency_rating" id="efficiency_rating" class="form-input-modern" min="0" max="100" step="0.1">
                    </div>
                    <div class="col-12">
                        <label for="strengths" class="form-label">Strengths</label>
                        <textarea name="strengths" id="strengths" class="form-textarea-modern" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label for="areas_for_improvement" class="form-label">Areas for Improvement</label>
                        <textarea name="areas_for_improvement" id="areas_for_improvement" class="form-textarea-modern" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-textarea-modern" rows="3"></textarea>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('staff.performance.index', $staff) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Review
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
