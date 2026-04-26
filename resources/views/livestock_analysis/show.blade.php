@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-check text-primary me-2"></i>Analysis Results</h2>
        <div>
            @if($analysis->status === 'analyzed')
                <form action="{{ route('livestock_analysis.markReviewed', $analysis) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Mark Reviewed
                    </button>
                </form>
            @endif
            <a href="{{ route('livestock_analysis.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Analysis Image -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Uploaded Image</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if($analysis->image_path)
                        <img src="{{ asset('storage/' . $analysis->image_path) }}" 
                             alt="Analysis image" 
                             class="img-fluid rounded">
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>No image available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Analysis Details -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>AI Diagnosis</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold">Diagnosis</label>
                        <h3 class="text-primary mb-2">{{ $analysis->diagnosis ?? 'Pending' }}</h3>
                        
                        @if($analysis->description)
                            <p class="mb-3">{{ $analysis->description }}</p>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Severity</label>
                                <div>
                                    @if($analysis->severity)
                                        <span class="badge bg-{{ $analysis->severity_color }} fs-6">
                                            {{ ucfirst($analysis->severity) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not assessed</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Confidence</label>
                                <div>
                                    @if($analysis->confidence_score)
                                        <span class="badge bg-info fs-6">
                                            {{ round($analysis->confidence_score, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Status</label>
                                <div>
                                    <span class="badge bg-{{ $analysis->status_color }} fs-6">
                                        {{ ucfirst($analysis->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div>
                        <label class="text-muted small text-uppercase fw-bold">
                            <i class="fas fa-lightbulb me-1 text-warning"></i> AI Recommendation
                        </label>
                        <div class="p-3 bg-light rounded mt-2">
                            {!! nl2br(e($analysis->recommendation)) !!}
                        </div>
                    </div>

                    @if($analysis->detected_issues && count($analysis->detected_issues) > 0)
                        <hr class="my-4">
                        <div>
                            <label class="text-muted small text-uppercase fw-bold">Detected Issues</label>
                            <ul class="list-group list-group-flush mt-2">
                                @foreach($analysis->detected_issues as $issue)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            {{ ucfirst(str_replace('_', ' ', $issue['type'] ?? 'issue')) }}
                                            @if(isset($issue['description']))
                                                <small class="text-muted d-block">{{ $issue['description'] }}</small>
                                            @endif
                                        </div>
                                        @if(isset($issue['confidence']))
                                            <span class="badge bg-secondary">{{ round($issue['confidence'], 1) }}%</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($analysis->livestock)
                        <hr class="my-4">
                        <div>
                            <label class="text-muted small text-uppercase fw-bold">Linked Livestock</label>
                            <div class="mt-2">
                                <a href="{{ route('livestock.show', $analysis->livestock) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-cow me-1"></i>
                                    {{ $analysis->livestock->tag_number ?? 'View Animal' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-muted small">
                    Created: {{ $analysis->created_at->format('M d, Y g:i A') }}
                    @if($analysis->status === 'reviewed' && $analysis->updated_at != $analysis->created_at)
                        <br>Reviewed: {{ $analysis->updated_at->format('M d, Y g:i A') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Livestock Disease Records (if any) -->
    @if($analysis->livestock)
        @php
            $diseaseRecords = \App\Models\LivestockDisease::where('livestock_id', $analysis->livestock->id)
                                        ->where('name', $analysis->diagnosis)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(3)
                                        ->get();
        @endphp

        @if($diseaseRecords->count() > 0)
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-heartbeat me-2 text-danger"></i>Related Disease Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Severity</th>
                                            <th>Status</th>
                                            <th>Treatment</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($diseaseRecords as $record)
                                            <tr>
                                                <td>{{ $record->diagnosed_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $record->severity == 'high' ? 'danger' : ($record->severity == 'medium' ? 'warning' : 'success') }}">
                                                        {{ ucfirst($record->severity) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $record->status == 'treated' ? 'success' : ($record->status == 'active' ? 'danger' : 'secondary') }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ Str::limit($record->treatment, 50) }}</td>
                                                <td>
                                                    <a href="{{ route('diseases.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
