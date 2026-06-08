@extends('layouts.MainLayout')

@section('title', 'Customer Support - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Customer Support</h1>
            <p class="text-sm text-gray-500">Smart ticketing, farm help bot, knowledge base, SLA tracking, and multi-channel support.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('support-tickets.chat') }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border">
                <i class="fas fa-robot"></i><span>Farm Help Bot</span>
            </a>
            <a href="{{ route('support-tickets.knowledge-base') }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border">
                <i class="fas fa-book-open"></i><span>Help Center</span>
            </a>
            <a href="{{ route('support-tickets.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus"></i><span>New Ticket</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $allTickets->where('status', 'open')->count() }}</p>
            <p class="text-sm text-gray-500">Open</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $allTickets->where('status', 'in_progress')->count() }}</p>
            <p class="text-sm text-gray-500">In Progress</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $avgFirstResponse ? floor($avgFirstResponse / 60).'h '.round($avgFirstResponse % 60).'m' : '-' }}</p>
            <p class="text-sm text-gray-500">Avg First Response</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $avgResolution ? floor($avgResolution / 60).'h '.round($avgResolution % 60).'m' : '-' }}</p>
            <p class="text-sm text-gray-500">Avg Resolution</p>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach([
            ['icon' => 'comments', 'label' => 'In-app chat'],
            ['icon' => 'envelope', 'label' => 'Email'],
            ['icon' => 'sms', 'label' => 'SMS'],
            ['icon' => 'brand-whatsapp', 'label' => 'WhatsApp'],
        ] as $channel)
            <x-ui.card class="text-center">
                <i class="fas fa-{{ $channel['icon'] }} text-emerald-600 text-xl mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">{{ $channel['label'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Self-Service Help</h2>
        <div class="grid gap-3 md:grid-cols-3">
            @foreach(array_slice($knowledgeArticles, 0, 3) as $article)
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="font-medium text-gray-900">{{ $article['title'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $article['summary'] }}</p>
                </div>
            @endforeach
        </div>
    </x-ui.card>

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
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="crop" {{ request('category') == 'crop' ? 'selected' : '' }}>Crops</option>
                    <option value="livestock" {{ request('category') == 'livestock' ? 'selected' : '' }}>Livestock</option>
                    <option value="finance" {{ request('category') == 'finance' ? 'selected' : '' }}>Finance</option>
                    <option value="system_bug" {{ request('category') == 'system_bug' ? 'selected' : '' }}>System Bugs</option>
                    <option value="farm_management" {{ request('category') == 'farm_management' ? 'selected' : '' }}>Farm Management</option>
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
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Assigned</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">SLA</th>
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
                                @if($ticket->auto_tags)
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach(array_slice($ticket->auto_tags, 0, 3) as $tag)
                                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">
                                {{ str_replace('_', ' ', $ticket->assigned_role ?? 'auto routing') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="{{ $ticket->sla_status === 'breached' ? 'text-red-600' : 'text-emerald-700' }}">
                                    {{ $ticket->sla_due_at?->diffForHumans() ?? '-' }}
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
                            <td colspan="9" class="px-6 py-12 text-center">
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
