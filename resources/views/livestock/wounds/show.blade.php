@extends('layouts.MainLayout')

@section('title', 'Wound Analysis Details - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Alert for urgent cases -->
            @if(in_array($wound->urgency, ['immediate', 'urgent']))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5><i class="bi bi-exclamation-triangle"></i> URGENT CASE</h5>
                This wound requires <strong>{{ $wound->urgency }}</strong> attention!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Wound Analysis Details</h4>
                        <div>
                            <span class="badge bg-{{ $wound->urgency_color }}">{{ ucfirst($wound->urgency) }}</span>
                            <span class="badge bg-{{ $wound->severity_color }}">{{ ucfirst($wound->severity) }}</span>
                            <span class="badge bg-{{ $wound->status_color }}">{{ ucfirst($wound->status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Image -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $wound->image_path) }}" alt="Wound Image" class="img-fluid rounded" style="max-width: 500px;">
                    </div>

                    <!-- Details Grid -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Wound Type</th>
                                    <td>{{ $wound->wound_type }}</td>
                                </tr>
                                <tr>
                                    <th>Severity</th>
                                    <td><span class="badge bg-{{ $wound->severity_color }}">{{ ucfirst($wound->severity) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Urgency</th>
                                    <td><span class="badge bg-{{ $wound->urgency_color }}">{{ ucfirst($wound->urgency) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Healing Time</th>
                                    <td>{{ $wound->estimated_healing_time }}</td>
                                </tr>
                                <tr>
                                    <th>Confidence</th>
                                    <td>{{ $wound->confidence_score }}%</td>
                                </tr>
                                @if($wound->livestock)
                                <tr>
                                    <th>Livestock</th>
                                    <td>{{ $wound->livestock->name }} ({{ $wound->livestock->tag_number }})</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Info</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Analyzed By</th>
                                    <td>{{ $wound->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $wound->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge bg-{{ $wound->status_color }}">{{ ucfirst($wound->status) }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Description</h6>
                            <p class="mb-0">{{ $wound->description }}</p>
                        </div>
                    </div>

                    <!-- Detected Issues -->
                    @if(!empty($wound->detected_issues))
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Detected Issues</h6>
                            <div class="row g-2">
                                @foreach($wound->detected_issues as $issue)
                                <div class="col-md-4">
                                    <div class="card border-{{ $issue['severity'] === 'severe' ? 'danger' : ($issue['severity'] === 'moderate' ? 'warning' : 'info') }} h-100">
                                        <div class="card-body p-2">
                                            <small class="text-muted">{{ ucfirst($issue['type']) }}</small>
                                            <h6 class="card-title mb-1">{{ $issue['description'] }}</h6>
                                            <small class="text-muted">Confidence: {{ $issue['confidence'] }}%</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Treatment Plan -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Treatment Plan</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $wound->treatment_plan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            @if($wound->status === 'analyzed')
                            <form method="POST" action="{{ route('wounds.treated', $wound) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Mark as Treated
                                </button>
                            </form>
                            @endif
                            @if($wound->status === 'treated')
                            <form method="POST" action="{{ route('wounds.healed', $wound) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-heart"></i> Mark as Healed
                                </button>
                            </form>
                            @endif
                            @can('isAdmin')
                            <form method="POST" action="{{ route('wounds.destroy', $wound) }}" class="d-inline" onsubmit="return confirm('Delete this analysis?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            @endcan
                            <a href="{{ route('wounds.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
