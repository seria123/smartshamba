@extends('layouts.MainLayout')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0"><i class="fas fa-chart-bar mr-2"></i>Generate Report</h1>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card-modern p-4 sm:p-6 space-y-6">
            <form method="POST" action="{{ route('reports.generate') }}">
                @csrf

                {{-- Farm & Report Type --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="farm_id" class="form-label">Farm <span class="text-danger">*</span></label>
                        <select class="form-select-modern @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                            <option value="">Select Farm</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                        @error('farm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="report_type" class="form-label">Report Type <span class="text-danger">*</span></label>
                        <select class="form-select-modern @error('report_type') is-invalid @enderror" id="report_type" name="report_type" required>
                            <option value="">Select Type</option>
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('report_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('report_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-input-modern @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-textarea-modern @error('description') is-invalid @enderror" id="description"
                        name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Period --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="report_period_start" class="form-label">Period Start <span class="text-danger">*</span></label>
                        <input type="date" class="form-input-modern @error('report_period_start') is-invalid @enderror"
                            id="report_period_start" name="report_period_start"
                            value="{{ old('report_period_start', now()->subWeek()->toDateString()) }}" required>
                        @error('report_period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="report_period_end" class="form-label">Period End <span class="text-danger">*</span></label>
                        <input type="date" class="form-input-modern @error('report_period_end') is-invalid @enderror"
                            id="report_period_end" name="report_period_end"
                            value="{{ old('report_period_end', now()->toDateString()) }}" required>
                        @error('report_period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary text-center">
                        <i class="fas fa-cog me-2"></i>Generate Report
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection