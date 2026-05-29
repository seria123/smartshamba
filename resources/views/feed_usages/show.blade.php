@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Feed Usage Details</h4>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <strong>Feed Type:</strong>
                        <p>{{ $feedUsage->feed_type }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Quantity (kg):</strong>
                        <p>{{ $feedUsage->quantity }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Animal Type:</strong>
                        <p>{{ ucfirst($feedUsage->animal_type) }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Usage Date:</strong>
                        <p>{{ $feedUsage->usage_date }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <p>{{ $feedUsage->notes ?? 'No notes provided' }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('feed_usages.index') }}" class="btn btn-secondary">
                            Back
                        </a>

                        <a href="{{ route('feed_usages.edit', $feedUsage->id) }}" class="btn btn-warning">
                            Edit
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection