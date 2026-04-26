@extends('layouts.MainLayout')

@section('title', 'Wound Analysis - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Wound Analysis Records</h4>
                        <div>
                            @can('isAdmin')
                            <a href="{{ route('wounds.highUrgency') }}" class="btn btn-warning">
                                <i class="bi bi-exclamation-circle"></i> High Urgency
                            </a>
                            @endcan
                            <a href="{{ route('wounds.create') }}" class="btn btn-primary">
                                <i class="bi bi-camera"></i> New Analysis
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Wound Type</th>
                                    <th>Severity</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Livestock</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analyses as $wound)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $wound->image_path) }}" alt="Wound" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>{{ $wound->wound_type ?? 'Pending' }}</td>
                                    <td><span class="badge bg-{{ $wound->severity_color }}">{{ ucfirst($wound->severity) }}</span></td>
                                    <td><span class="badge bg-{{ $wound->urgency_color }}">{{ ucfirst($wound->urgency) }}</span></td>
                                    <td><span class="badge bg-{{ $wound->status_color }}">{{ ucfirst($wound->status) }}</span></td>
                                    <td>{{ $wound->livestock->name ?? '-' }}</td>
                                    <td>{{ $wound->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('wounds.show', $wound) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('isAdmin')
                                        <form method="POST" action="{{ route('wounds.destroy', $wound) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-camera" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-2">No wound analysis records yet.</p>
                                        <a href="{{ route('wounds.create') }}" class="btn btn-primary mt-2">Create First Analysis</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $analyses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
