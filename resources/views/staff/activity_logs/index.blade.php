@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Activity Logs - {{ $staff->fullName() }}</h1>
        </div>

        <div class="card-modern p-4 mb-4">
            <form action="{{ route('staff.activity_logs.store', $staff) }}" method="POST" class="row g-3">
                @csrf
                <div class="col-12 col-md-3">
                    <label for="activity_type" class="form-label">Activity Type <span class="text-danger">*</span></label>
                    <input type="text" name="activity_type" id="activity_type" class="form-input-modern" placeholder="e.g., planting, spraying" required>
                </div>
                <div class="col-12 col-md-4">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-input-modern" placeholder="e.g., Sprayed Field B" required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="field_id" class="form-label">Field</label>
                    <select name="field_id" id="field_id" class="form-select-modern">
                        <option value="">-- Select Field --</option>
                        @foreach(\App\Models\Field::all() as $field)
                        <option value="{{ $field->id }}">{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i> Log Activity
                    </button>
                </div>
            </form>
        </div>

        <div class="card-modern overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity Type</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Field</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $log->activity_type === 'planting' ? 'success' : ($log->activity_type === 'spraying' ? 'warning' : 'info') }}">
                                    {{ ucfirst($log->activity_type) }}
                                </span>
                            </td>
                            <td>{{ $log->title }}</td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                            <td>{{ $log->field->name ?? '-' }}</td>
                            <td>{{ $log->duration ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No activity logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
