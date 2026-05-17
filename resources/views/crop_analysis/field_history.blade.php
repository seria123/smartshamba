@extends('layouts.app')

@section('title', 'Field History')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">🌿 Field History</h2>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    @if(isset($fields) && count($fields) > 0)

        <div class="row">
            @foreach($fields as $field)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h5 class="card-title">
                                🌾 {{ $field->name ?? 'Unnamed Field' }}
                            </h5>

                            <p class="mb-1">
                                <strong>Crop:</strong> {{ $field->crop ?? 'N/A' }}
                            </p>

                            <p class="mb-1">
                                <strong>Size:</strong> {{ $field->size ?? 'N/A' }} acres
                            </p>

                            <p class="mb-1">
                                <strong>Last Analysis:</strong>
                                {{ $field->updated_at ? $field->updated_at->format('d M Y') : 'Never' }}
                            </p>

                            <p class="text-muted mt-2 mb-0">
                                {{ $field->notes ?? 'No notes available.' }}
                            </p>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="alert alert-info">
            No field history found yet. Start by adding a field analysis 🌱
        </div>
    @endif

</div>
@endsection