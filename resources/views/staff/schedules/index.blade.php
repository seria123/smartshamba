@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Schedules - {{ $staff->fullName() }}</h1>
        </div>

        <div class="card-modern p-4 mb-4">
            <form action="{{ route('staff.schedules.store', $staff) }}" method="POST" class="row g-3">
                @csrf
                <div class="col-12 col-md-2">
                    <label for="schedule_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="schedule_date" id="schedule_date" class="form-input-modern" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12 col-md-2">
                    <label for="field_id" class="form-label">Field</label>
                    <select name="field_id" id="field_id" class="form-select-modern">
                        <option value="">-- No Field --</option>
                        @foreach($fields as $field)
                        <option value="{{ $field->id }}">{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-input-modern">
                </div>
                <div class="col-12 col-md-2">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-input-modern">
                </div>
                <div class="col-12 col-md-2">
                    <label for="shift_type" class="form-label">Shift</label>
                    <select name="shift_type" id="shift_type" class="form-select-modern">
                        <option value="">-- Select --</option>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="night">Night</option>
                        <option value="full_day">Full Day</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i> Add Schedule
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
                            <th>Field</th>
                            <th>Time</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->schedule_date->format('M d, Y') }}</td>
                            <td>{{ $schedule->field->name ?? '-' }}</td>
                            <td>{{ $schedule->start_time ?? '-' }} - {{ $schedule->end_time ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $schedule->shift_type ?? 'N/A')) }}</td>
                            <td>
                                <span class="badge bg-{{ $schedule->status === 'checked_in' ? 'success' : ($schedule->status === 'missed' ? 'danger' : ($schedule->status === 'checked_out' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('staff.schedules.update', [$staff, $schedule]) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline w-auto">
                                        <option value="scheduled" {{ $schedule->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="checked_in" {{ $schedule->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                        <option value="checked_out" {{ $schedule->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                        <option value="missed" {{ $schedule->status === 'missed' ? 'selected' : '' }}>Missed</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                                </form>
                                <form action="{{ route('staff.schedules.destroy', [$staff, $schedule]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this schedule?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No schedules found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
