@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <h1 class="text-2xl font-bold mb-0">Field Presence - Who is in the Field?</h1>

        <div class="card-modern p-4">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="field_id" class="form-label">Select Field</label>
                    <select name="field_id" id="field_id" class="form-select-modern" onchange="this.form.submit()">
                        <option value="">-- All Fields --</option>
                        @foreach($fields as $field)
                        <option value="{{ $field->id }}" {{ $fieldId == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="card-modern p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Assigned Field</th>
                            <th>Checked In</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presence as $location)
                        <tr>
                            <td>
                                <a href="{{ route('staff.show', $location->staff) }}">
                                    <strong>{{ $location->staff->fullName() }}</strong>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-{{ $location->staff->role_badge_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $location->staff->role)) }}
                                </span>
                            </td>
                            <td>{{ $location->field->name ?? '-' }}</td>
                            <td>{{ $location->checked_in_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($location->checked_out_at)
                                {{ $location->checked_in_at->diffForHumans($location->checked_out_at, true) }}
                                @else
                                <span class="text-success fw-bold">Still inside</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $location->is_active ? 'success' : 'info' }}">
                                    {{ $location->is_active ? 'Present' : 'Departed' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No staff currently in the field. Select a field to check.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
