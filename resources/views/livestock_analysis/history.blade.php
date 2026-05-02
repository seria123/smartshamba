@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-cow text-primary me-2"></i>
                Analysis History
            </h2>
            <p class="text-muted mb-0">
                Livestock: 
                <strong>{{ $livestock->tag_number ?? 'Untagged' }}</strong> - 
                {{ $livestock->type ?? 'Unknown Type' }} 
                ({{ $livestock->breed ?? 'Unknown' }})
            </p>
        </div>
        <div>
            <a href="{{ route('livestock-analysis.create', ['livestock_id' => $livestock->id]) }}" 
               class="btn btn-primary">
                <i class="fas fa-camera me-1"></i> New Analysis
            </a>
            <a href="{{ route('livestock.show', $livestock) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Livestock
            </a>
        </div>
    </div>

    @if($analyses->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No analyses recorded for this animal yet. 
            <a href="{{ route('livestock-analysis.create', ['livestock_id' => $livestock->id]) }}" class="alert-link">Upload first image</a>
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
                             style="height: 180px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-1">
                                {{ $analysis->diagnosis ?? 'Unknown' }}
                            </h6>
                            <span class="badge bg-{{ $analysis->severity_color }}">
                                {{ ucfirst($analysis->severity ?? 'N/A') }}
                            </span>
                        </div>

                        <p class="card-text small text-muted mb-2">
                            {{ $analysis->created_at->format('M d, Y') }}
                        </p>

                        <p class="card-text small">
                            {{ Str::limit($analysis->description, 80) }}
                        </p>
                    </div>

                    <div class="card-footer bg-transparent">
                         <a href="{{ route('livestock-analysis.show', $analysis) }}"
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye"></i> View Details
                        </a>
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
