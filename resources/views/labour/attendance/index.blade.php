@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Workers and labour</p>
            <h1>Attendance</h1>
        </div>
        <a class="button" href="{{ route('labour.attendance.create') }}">Record attendance</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Worker</th>
                    <th>Farm</th>
                    <th>Team</th>
                    <th>Status</th>
                    <th>Hours</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->date->format('Y-m-d') }}</td>
                        <td>{{ $record->worker->name }}</td>
                        <td>{{ $record->farm->name }}</td>
                        <td>{{ $record->team?->name ?? 'None' }}</td>
                        <td>{{ ucfirst($record->status) }}</td>
                        <td>{{ $record->hours_worked ?? 'Not set' }}</td>
                        <td><a href="{{ route('labour.attendance.edit', $record) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
@endsection
