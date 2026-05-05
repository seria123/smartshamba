@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Tasks / Work Orders</p>
            <h1>Work orders</h1>
        </div>
        <a class="button" href="{{ route('tasks.work-orders.create') }}">New work order</a>
    </header>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Farm</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Due</th>
                    <th>Tasks</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workOrders as $workOrder)
                    <tr>
                        <td>{{ $workOrder->work_order_number }}</td>
                        <td><a href="{{ route('tasks.work-orders.show', $workOrder) }}">{{ $workOrder->title }}</a></td>
                        <td>{{ $workOrder->farm->name }}</td>
                        <td>{{ ucfirst($workOrder->category) }}</td>
                        <td>{{ ucfirst($workOrder->priority) }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($workOrder->status)) }}</td>
                        <td>{{ $workOrder->due_date?->format('Y-m-d') ?? 'Not set' }}</td>
                        <td>{{ $workOrder->tasks->count() }}</td>
                        <td><a href="{{ route('tasks.work-orders.edit', $workOrder) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9">No work orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $workOrders->links() }}
@endsection
