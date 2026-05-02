@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-6">

            {{-- IMAGES GALLERY --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5><i class="fas fa-images"></i> Uploaded Images</h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-2 p-2">
                        @foreach($cropAnalysis->all_images as $index => $imagePath)
                        <div class="col-6 col-md-4">
                            <div class="position-relative">
                                <img src="{{ Storage::url($imagePath) }}"
                                     class="img-fluid rounded"
                                     style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal{{ $index }}">
                                @if($index === 0)
                                <span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- DETAILS --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Analysis Details</h5>
                </div>
                <div class="card-body">

                    @php
                        $confidence = $cropAnalysis->confidence_score ?? 0;
                        $confidenceColor = $confidence > 80 ? 'success' : ($confidence > 50 ? 'warning' : 'danger');
                    @endphp

                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $cropAnalysis->status_color }}">
                                    {{ ucfirst($cropAnalysis->status) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Field:</strong></td>
                            <td>{{ $cropAnalysis->field?->name ?? 'Not specified' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Crop Cycle:</strong></td>
                            <td>
                                @if($cropAnalysis->cropCycle)
                                    <a href="{{ route('crop_cycles.show', $cropAnalysis->cropCycle) }}">
                                        {{ $cropAnalysis->cropCycle->crop->name ?? $cropAnalysis->cropCycle->crop_name }}
                                    </a>
                                    ({{ $cropAnalysis->cropCycle->start_date?->format('M Y') }})
                                @else
                                    Not specified
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ $cropAnalysis->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>

                        <tr>
                            <td><strong>Confidence:</strong></td>
                            <td>
                                <div class="progress" style="height: 25px;">
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
                <div class="card-header text-white bg-{{ $cropAnalysis->severity_color ?? 'primary' }}">
                    <h4 class="mb-0">
                        <i class="fas fa-stethoscope"></i> Diagnosis Result
                    </h4>
                </div>

                <div class="card-body text-center">

                    {{-- ICON --}}
                    <div class="display-4 mb-3">
                        @if(strtolower($cropAnalysis->diagnosis) === 'healthy' || strtolower($cropAnalysis->diagnosis) === 'analysis unavailable')
                            <span class="text-success">🌱</span>
                        @else
                            <span class="text-danger">🍂</span>
                        @endif
                    </div>

                    <h3>{{ $cropAnalysis->diagnosis ?? 'Unknown Condition' }}</h3>

                    @if($cropAnalysis->severity)
                        <span class="badge bg-{{ $cropAnalysis->severity_color }} fs-6">
                            {{ ucfirst($cropAnalysis->severity) }} Severity
                        </span>
                    @endif

                    @if($cropAnalysis->confidence_score)
                        <div class="mt-2">
                            <small class="text-muted">Confidence: {{ number_format($cropAnalysis->confidence_score, 1) }}%</small>
                        </div>
                    @endif

                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6><i class="fas fa-align-left"></i> Description</h6>
                    <p class="text-muted mb-0">
                        {{ $cropAnalysis->description ?? 'No description available.' }}
                    </p>
                </div>
            </div>

            {{-- RECOMMENDATION --}}
            <div class="card mb-4 shadow-sm border-success">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb"></i> Recommended Action</h6>

                    <div class="alert alert-success">
                        {{ $cropAnalysis->recommendation ?? 'No recommendations available.' }}
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-success">
                            ✅ Apply Recommendation
                        </button>

                        <a href="{{ route('crop_analyses.create', ['field_id' => $cropAnalysis->field_id, 'crop_cycle_id' => $cropAnalysis->crop_cycle_id]) }}" class="btn btn-outline-warning">
                            🔄 Re-analyze Crop
                        </a>
                    </div>

                </div>
            </div>

            {{-- DETECTED ISSUES --}}
            @if(!empty($cropAnalysis->detected_issues))
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h5><i class="fas fa-bug"></i> Detected Issues</h5>
                    </div>

                    <div class="card-body">
                        @foreach($cropAnalysis->detected_issues as $issue)
                            <div class="border rounded p-3 mb-2">

                                <div class="d-flex justify-content-between">
                                    <strong>
                                        {{ ucwords(str_replace('_', ' ', $issue['type'] ?? $issue)) }}
                                    </strong>

                                    @isset($issue['affected_area_percent'])
                                        <span class="badge bg-danger">
                                            {{ $issue['affected_area_percent'] }}% affected
                                        </span>
                                    @endisset
                                </div>

                                @isset($issue['location'])
                                    <small class="text-muted d-block mt-1">
                                        Location: {{ $issue['location'] }}
                                    </small>
                                @endisset

                                @isset($issue['severity'])
                                    <small class="text-muted d-block">
                                        Severity: {{ ucfirst($issue['severity']) }}
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
                    @if($cropAnalysis->status !== 'reviewed')
                        <form action="{{ route('crop_analyses.markReviewed', $cropAnalysis) }}" method="POST">
                            @csrf
                            <button class="btn btn-success w-100">
                                ✔ Mark Reviewed
                            </button>
                        </form>
                    @endif

                    {{-- DELETE --}}
                    <form action="{{ route('crop_analyses.destroy', $cropAnalysis) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100"
                                onclick="return confirm('Delete this analysis?')">
                            🗑 Delete Analysis
                        </button>
                    </form>

                    {{-- NAVIGATION --}}
                    <a href="{{ route('crop_analyses.index') }}" class="btn btn-outline-secondary w-100">
                        Back to List
                    </a>

                    <a href="{{ route('crop_analyses.create') }}" class="btn btn-primary w-100">
                        📷 New Analysis
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Image Modals --}}
@foreach($cropAnalysis->all_images as $index => $imagePath)
<div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image {{ $index + 1 }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Storage::url($imagePath) }}" class="img-fluid" alt="Analysis image">
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
