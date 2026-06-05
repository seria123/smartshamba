@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Location History - {{ $staff->fullName() }}</h1>
            <form action="{{ route('staff.locations.store', $staff) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="hidden" name="action" value="checkin">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-map-marker-alt me-1"></i> Check In
                </button>
            </form>
        </div>

        <div class="card-modern overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Field</th>
                            <th>Location Type</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                        <tr>
                            <td>{{ $location->created_at->format('M d, Y') }}</td>
                            <td>{{ $location->field->name ?? '-' }}</td>
                            <td>{{ ucfirst($location->location_type) }}</td>
                            <td>{{ $location->checked_in_at ? $location->checked_in_at->format('M d, H:i') : '-' }}</td>
                            <td>{{ $location->checked_out_at ? $location->checked_out_at->format('M d, H:i') : '-' }}</td>
                            <td>
                                @if($location->checked_in_at && $location->checked_out_at)
                                {{ $location->checked_in_at->diffForHumans($location->checked_out_at, true) }}
                                @elseif($location->checked_in_at && !$location->checked_out_at)
                                <span class="text-success">Currently checked in</span>
                                @endif
                            </td>
                            <td>
                                @if($location->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Ended</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No location records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
