@extends('layouts.MainLayout')

@section('title', 'Edit Livestock Type - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Livestock Type</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('livestock-types.update', $livestockType) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $livestockType->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="{{ old('slug', $livestockType->slug) }}" required>
                            <div class="form-text">URL-friendly identifier</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $livestockType->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_individual_tracking" name="requires_individual_tracking" value="1"
                                       {{ old('requires_individual_tracking', $livestockType->requires_individual_tracking) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_individual_tracking">
                                    Requires Individual Tracking
                                </label>
                            </div>
                            <div class="form-text">Check if this livestock type needs individual identification</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('livestock-types.show', $livestockType) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection