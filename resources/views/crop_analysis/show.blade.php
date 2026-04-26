@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-6">

            {{-- IMAGE --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5><i class="fas fa-image"></i> Uploaded Image</h5>
                </div>
                <div class="card-body p-0 text-center">
                    <img src="{{ Storage::url($crop_analysis->image_path) }}"
                         class="img-fluid"
                         style="max-height: 500px; object-fit: contain;">
                </div>
            </div>

            {{-- DETAILS --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Analysis Details</h5>
                </div>
                <div class="card-body">

                    @php
                        $confidence = $crop_analysis->confidence_score ?? 0;
                        $confidenceColor = $confidence > 80 ? 'success' : ($confidence > 50 ? 'warning' : 'danger');
                    @endphp

                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $crop_analysis->status_color }}">
                                    {{ ucfirst($crop_analysis->status) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Field:</strong></td>
                            <td>{{ $crop_analysis->field?->name ?? 'Not specified' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ $crop_analysis->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>

                        <tr>
                            <td><strong>Confidence:</strong></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $confidenceColor }}"
                                         style="width: {{ $confidence }}%">
                                        {{ number_format($confidence, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-6">

            {{-- DIAGNOSIS --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white bg-{{ $crop_analysis->severity_color ?? 'primary' }}">
                    <h4 class="mb-0">
                        <i class="fas fa-stethoscope"></i> Diagnosis Result
                    </h4>
                </div>

                <div class="card-body text-center">

                    {{-- ICON --}}
                    <div class="display-4 mb-3">
                        @if(strtolower($crop_analysis->diagnosis) === 'healthy')
                            <span class="text-success">🌱</span>
                        @else
                            <span class="text-danger">🍂</span>
                        @endif
                    </div>

                    <h3>{{ $crop_analysis->diagnosis }}</h3>

                    @if($crop_analysis->severity)
                        <span class="badge bg-{{ $crop_analysis->severity_color }} fs-6">
                            {{ ucfirst($crop_analysis->severity) }} Severity
                        </span>
                    @endif

                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6><i class="fas fa-align-left"></i> Description</h6>
                    <p class="text-muted mb-0">
                        {{ $crop_analysis->description }}
                    </p>
                </div>
            </div>

            {{-- RECOMMENDATION --}}
            <div class="card mb-4 shadow-sm border-success">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb"></i> Recommended Action</h6>

                    <div class="alert alert-success">
                        {{ $crop_analysis->recommendation }}
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-success">
                            ✅ Apply Recommendation
                        </button>

                        <button class="btn btn-outline-warning">
                            🔄 Re-analyze Crop
                        </button>
                    </div>

                </div>
            </div>

            {{-- DETECTED ISSUES --}}
            @if(!empty($crop_analysis->detected_issues))
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h5><i class="fas fa-bug"></i> Detected Issues</h5>
                    </div>

                    <div class="card-body">
                        @foreach($crop_analysis->detected_issues as $issue)
                            <div class="border rounded p-3 mb-2">

                                <div class="d-flex justify-content-between">
                                    <strong>
                                        {{ ucwords(str_replace('_', ' ', $issue['type'])) }}
                                    </strong>

                                    @isset($issue['affected_area_percent'])
                                        <span class="badge bg-danger">
                                            {{ $issue['affected_area_percent'] }}%
                                        </span>
                                    @endisset
                                </div>

                                @isset($issue['location'])
                                    <small class="text-muted">
                                        Location: {{ $issue['location'] }}
                                    </small>
                                @endisset

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ACTIONS --}}
            <div class="card shadow-sm">
                <div class="card-body d-grid gap-2">

                    {{-- REVIEW --}}
                    @if($crop_analysis->status !== 'reviewed')
                        <form action="{{ route('crop_analysis.markReviewed', $crop_analysis) }}" method="POST">
                            @csrf
                            <button class="btn btn-success w-100">
                                ✔ Mark Reviewed
                            </button>
                        </form>
                    @endif

                    {{-- DELETE --}}
                    <form action="{{ route('crop_analysis.destroy', $crop_analysis) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100"
                                onclick="return confirm('Delete this analysis?')">
                            🗑 Delete Analysis
                        </button>
                    </form>

                    {{-- NAVIGATION --}}
                    <a href="{{ route('crop_analysis.index') }}" class="btn btn-outline-secondary w-100">
                        Back to List
                    </a>

                    <a href="{{ route('crop_analysis.create') }}" class="btn btn-primary w-100">
                        📷 New Analysis
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection