@extends('layouts.MainLayout')

@section('title', 'Customer Support - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Customer Support</h1>
        <a href="{{ route('support-tickets.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus"></i>
            <span>New Ticket</span>
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $tickets->where('status', 'open')->count() }}</p>
            <p class="text-sm text-gray-500">Open</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $tickets->where('status', 'in_progress')->count() }}</p>
            <p class="text-sm text-gray-500">In Progress</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $tickets->where('status', 'waiting_reply')->count() }}</p>
            <p class="text-sm text-gray-500">Waiting Reply</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $tickets->whereIn('status', ['resolved', 'closed'])->count() }}</p>
            <p class="text-sm text-gray-500">Resolved/Closed</p>
        </x-ui.card>
    </div>

    <!-- Filters -->
    <x-ui.card>
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_reply" {{ request('status') == 'waiting_reply' ? 'selected' : '' }}>Waiting Reply</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Priority</label>
                <select name="priority" class="form-control">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="account" {{ request('category') == 'account' ? 'selected' : '' }}>Account</option>
                    <option value="farm_management" {{ request('category') == 'farm_management' ? 'selected' : '' }}>Farm Management</option>
                    <option value="livestock" {{ request('category') == 'livestock' ? 'selected' : '' }}>Livestock</option>
                    <option value="crop" {{ request('category') == 'crop' ? 'selected' : '' }}>Crop & Field</option>
                    <option value="feature_request" {{ request('category') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </x-ui.card>

    <!-- Tickets List -->
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono">{{ $ticket->reference_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->subject, 50) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $ticket->priority_color }}-100 text-{{ $ticket->priority_color }}-800">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $ticket->status_color }}-100 text-{{ $ticket->status_color }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('support-tickets.show', $ticket) }}" class="text-emerald-600 hover:text-emerald-700">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No support tickets found</p>
                                <a href="{{ route('support-tickets.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg mt-4">
                                    <i class="fas fa-plus"></i>
                                    <span>Create First Ticket</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </x-ui.card>
</div>
@endsection