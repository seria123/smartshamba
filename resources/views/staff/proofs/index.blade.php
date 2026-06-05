@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Proof of Work - {{ $staff->fullName() }}</h1>
            <a href="{{ route('staff.proofs.create', $staff) }}" class="btn btn-primary">
                <i class="fas fa-upload me-1"></i> Upload Proof
            </a>
        </div>

        <div class="row">
            @forelse($proofs as $proof)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $proof->photo_path) }}" class="card-img-top" alt="Proof" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">{{ ucfirst(str_replace('_', ' ', $proof->proof_type)) }}</h6>
                            <span class="badge bg-{{ $proof->status === 'approved' ? 'success' : ($proof->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($proof->status) }}
                            </span>
                        </div>
                        <p class="card-text small text-muted">{{ Str::limit($proof->description, 80) }}</p>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>{{ $proof->created_at->diffForHumans() }}
                        </small>
                        @if($proof->task)
                        <br><small class="text-muted"><i class="fas fa-tasks me-1"></i>{{ $proof->task->title }}</small>
                        @endif
                    </div>
                    <div class="card-footer bg-white">
                        @if($proof->status === 'pending')
                        <form action="{{ route('staff.proofs.approve', [$staff, $proof]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check me-1"></i> Approve
                            </button>
                        </form>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $proof->id }}">
                            <i class="fas fa-times me-1"></i> Reject
                        </button>
                        @endif
                    </div>
                </div>

                @if($proof->status === 'pending')
                <div class="modal fade" id="rejectModal{{ $proof->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Reject Proof</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('staff.proofs.reject', [$staff, $proof]) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="review_notes" class="form-label">Reason <span class="text-danger">*</span></label>
                                        <textarea name="review_notes" id="review_notes" class="form-textarea-modern" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">No proof of work uploaded yet</div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
