@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cow text-primary me-2"></i>Livestock Disease Analyses</h2>
        <div>
            <a href="{{ route('livestock_analysis.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Analysis
            </a>
        </div>
    </div>

    @if($analyses->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No analyses yet. 
            <a href="{{ route('livestock_analysis.create') }}" class="alert-link">Upload an image</a> to start AI-powered disease detection.
        </div>
    @else
        <div class="row">
            @foreach($analyses as $analysis)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    @if($analysis->image_path)
                        <img src="{{ asset('storage/' . $analysis->image_path) }}" 
                             class="card-img-top" 
                             alt="Livestock analysis image"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-1">
                                @if($analysis->livestock)
                                    {{ $analysis->livestock->tag_number ?? 'Unknown Livestock' }}
                                @else
                                    General Analysis
                                @endif
                            </h5>
                            <span class="badge bg-{{ $analysis->severity_color }}">
                                {{ ucfirst($analysis->severity ?? 'N/A') }}
                            </span>
                        </div>

                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $analysis->created_at->format('M d, Y') }}
                        </p>

                        <h6 class="card-subtitle mb-2 text-primary">
                            {{ $analysis->diagnosis ?? 'Pending Analysis' }}
                        </h6>

                        @if($analysis->description)
                            <p class="card-text small mb-3">
                                {{ Str::limit($analysis->description, 100) }}
                            </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $analysis->status_color }}">
                                {{ ucfirst($analysis->status) }}
                            </span>
                            @if($analysis->confidence_score)
                                <small class="text-muted">
                                    {{ round($analysis->confidence_score, 1) }}% confidence
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <a href="{{ route('livestock_analysis.show', $analysis) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($analysis->status === 'pending')
                                <span class="btn btn-sm btn-outline-warning disabled">
                                    <i class="fas fa-clock"></i> Processing
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $analyses->links() }}
        </div>
    @endif
</div>
@endsection
