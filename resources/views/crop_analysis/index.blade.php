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
        <a href="{{ route('crop_analyses.create') }}" class="btn btn-primary">
            <i class="fas fa-camera"></i> New Analysis
        </a>
    </div>

    {{-- 🔍 FILTER --}}
    <form method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="analyzed" {{ request('status') === 'analyzed' ? 'selected' : '' }}>Analyzed</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="severity" class="form-select">
                    <option value="">All Severity</option>
                    <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="crop_cycle_id" class="form-select">
                    <option value="">All Crop Cycles</option>
                    @foreach($cropCycles as $cycle)
                    <option value="{{ $cycle->id }}" {{ request('crop_cycle_id') == $cycle->id ? 'selected' : '' }}>
                        {{ $cycle->crop->name ?? $cycle->crop_name }} - {{ $cycle->start_date?->format('M Y') ?? 'N/A' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
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
                <a href="{{ route('crop_analyses.create') }}" class="btn btn-primary">
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

                        {{-- PRIMARY IMAGE --}}
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

                            {{-- CROP CYCLE INFO --}}
                            @if($analysis->cropCycle)
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-seedling"></i>
                                    {{ $analysis->cropCycle->crop->name ?? $analysis->cropCycle->crop_name }}
                                    @if($analysis->cropCycle->field)
                                        • {{ $analysis->cropCycle->field->name }}
                                    @endif
                                </small>
                            @endif

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

                            <a href="{{ route('crop_analyses.show', $analysis) }}"
                               class="btn btn-sm btn-outline-primary">
                                View
                            </a>

                            @if($analysis->status !== 'reviewed')
                                <form action="{{ route('crop_analyses.markReviewed', $analysis) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        ✔ Reviewed
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('crop_analyses.destroy', $analysis) }}" method="POST">
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
            {{ $analyses->appends(request()->except('page'))->links() }}
        </div>

    @endif
</div>
@endsection
