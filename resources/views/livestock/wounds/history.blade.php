@extends('layouts.MainLayout')

@section('title', 'Wound History - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-bandaid"></i> Wound History for {{ $livestock->name }}
                        </h4>
                        <a href="{{ route('wounds.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Analysis
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($analyses->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-bandaid" style="font-size: 4rem; color: #ccc;"></i>
                        <p class="mt-3 text-muted">No wound analysis records for this animal yet.</p>
                        <a href="{{ route('wounds.create') }}?livestock_id={{ $livestock->id }}" class="btn btn-primary">
                            Create First Analysis
                        </a>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Wound Type</th>
                                    <th>Severity</th>
                                    <th>Healing Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $wound)
                                <tr>
                                    <td>{{ $wound->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $wound->wound_type ?? 'Pending' }}</td>
                                    <td><span class="badge bg-{{ $wound->severity_color }}">{{ ucfirst($wound->severity) }}</span></td>
                                    <td>{{ $wound->estimated_healing_time }}</td>
                                    <td><span class="badge bg-{{ $wound->status_color }}">{{ ucfirst($wound->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('wounds.show', $wound) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $analyses->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
