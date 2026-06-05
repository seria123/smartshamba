@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Field Assignments - {{ $staff->fullName() }}</h1>
        </div>

        <div class="card-modern p-6">
            <form action="{{ route('staff.field_assignments.store', $staff) }}" method="POST" class="row g-3 mb-4">
                @csrf
                <div class="col-12 col-md-3">
                    <label for="field_id" class="form-label">Field <span class="text-danger">*</span></label>
                    <select name="field_id" id="field_id" class="form-select-modern" required>
                        <option value="">-- Select Field --</option>
                        @foreach($fields as $field)
                        <option value="{{ $field->id }}">{{ $field->name }} ({{ $field->size_hectares }} ha)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="farm_id" class="form-label">Farm <span class="text-danger">*</span></label>
                    <select name="farm_id" id="farm_id" class="form-select-modern" required>
                        @foreach($farms as $farm)
                        <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="assigned_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="assigned_date" id="assigned_date" class="form-input-modern" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_primary" id="is_primary">
                        <label class="form-check-label" for="is_primary">Primary Field</label>
                    </div>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i> Assign
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Farm</th>
                            <th>Primary</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff->fieldAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->field->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->farm->name ?? 'N/A' }}</td>
                            <td>
                                @if($assignment->is_primary)
                                <span class="badge bg-warning">Primary</span>
                                @else
                                <span class="badge bg-secondary">Secondary</span>
                                @endif
                            </td>
                            <td>{{ $assignment->assigned_date->format('M d, Y') }}</td>
                            <td>
                                @if($assignment->unassigned_date)
                                <span class="badge bg-secondary">Unassigned</span>
                                @else
                                <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(!$assignment->unassigned_date)
                                <form action="{{ route('staff.field_assignments.destroy', [$staff, $assignment]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Unassign this field?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No field assignments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
