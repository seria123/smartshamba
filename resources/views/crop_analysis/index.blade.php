@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">

    {{-- 📊 DASHBOARD SUMMARY --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Total Analyses</h5>
                    <h3>{{ $analyses->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Reviewed</h5>
                    <h3>{{ $analyses->where('status','reviewed')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Pending</h5>
                    <h3>{{ $analyses->where('status','pending')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>High Severity</h5>
                    <h3>{{ $analyses->where('severity','high')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="fas fa-leaf"></i> AI Crop Analysis</h1>
        <a href="{{ route('crop_analysis.create') }}" class="btn btn-primary">
            <i class="fas fa-camera"></i> New Analysis
        </a>
    </div>

    {{-- 🔍 FILTER --}}
    <form method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="analyzed">Analyzed</option>
                    <option value="reviewed">Reviewed</option>
                </select>
            </div>

            <div class="col-md-4">
                <select name="severity" class="form-select">
                    <option value="">All Severity</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div class="col-md-4">
                <button class="btn btn-outline-primary w-100">
                    🔍 Filter
                </button>
            </div>
        </div>
    </form>

    {{-- EMPTY STATE --}}
    @if($analyses->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-leaf fa-4x text-muted mb-3"></i>
                <h4>No crop analyses yet</h4>
                <p class="text-muted">Upload a photo of your crops to detect diseases and get recommendations.</p>
                <a href="{{ route('crop_analysis.create') }}" class="btn btn-primary">
                    <i class="fas fa-camera"></i> Start Analysis
                </a>
            </div>
        </div>
    @else

        {{-- GRID --}}
        <div class="row">
            @foreach($analyses as $analysis)
                @php
                    $confidence = $analysis->confidence_score ?? 0;
                    $confidenceColor = $confidence > 80 ? 'success' : ($confidence > 50 ? 'warning' : 'danger');
                @endphp

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-{{ $analysis->severity_color ?? 'secondary' }} shadow-sm">

                        {{-- IMAGE --}}
                        <img src="{{ Storage::url($analysis->image_path) }}"
                             class="card-img-top"
                             style="height: 200px; object-fit: cover;">

                        <div class="card-body">

                            {{-- STATUS + SEVERITY --}}
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-{{ $analysis->status_color }}">
                                    {{ ucfirst($analysis->status) }}
                                </span>

                                @if($analysis->severity)
                                    <span class="badge bg-{{ $analysis->severity_color }}">
                                        {{ ucfirst($analysis->severity) }}
                                    </span>
                                @endif
                            </div>

                            {{-- TITLE --}}
                            <h5 class="card-title">
                                {{ $analysis->diagnosis ?? 'Unknown Condition' }}
                            </h5>

                            {{-- DESCRIPTION --}}
                            <p class="card-text small text-muted">
                                {{ \Illuminate\Support\Str::limit($analysis->description, 100) }}
                            </p>

                            {{-- 🌱 RECOMMENDATION --}}
                            @if($analysis->recommendation)
                                <div class="bg-light p-2 rounded small mb-2">
                                    <strong>💡 Advice:</strong>
                                    {{ \Illuminate\Support\Str::limit($analysis->recommendation, 120) }}
                                </div>
                            @endif

                            {{-- FOOT INFO --}}
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    {{ $analysis->created_at->diffForHumans() }}
                                </small>

                                <span class="badge bg-{{ $confidenceColor }}">
                                    {{ number_format($confidence, 1) }}%
                                </span>
                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="card-footer bg-white d-flex justify-content-between flex-wrap gap-1">

                            <a href="{{ route('crop_analysis.show', $analysis) }}"
                               class="btn btn-sm btn-outline-primary">
                                View
                            </a>

                            @if($analysis->status !== 'reviewed')
                                <form action="{{ route('crop_analysis.markReviewed', $analysis) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        ✔ Reviewed
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('crop_analysis.destroy', $analysis) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this analysis?')">
                                    🗑
                                </button>
                            </form>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-center">
            {{ $analyses->links() }}
        </div>

    @endif
</div>
@endsection