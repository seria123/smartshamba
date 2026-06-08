@extends('layouts.MainLayout')

@section('title', 'Support Ticket - SmartShamba')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Support Ticket #{{ $supportTicket->reference_number }}</h1>
        <a href="{{ route('support-tickets.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i> Back to Tickets
        </a>
    </div>

    <!-- Ticket Details -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4 pb-3 border-b">
            <div>
                <h3 class="font-semibold text-lg text-gray-900">{{ $supportTicket->subject }}</h3>
                <p class="text-sm text-gray-500">Submitted {{ $supportTicket->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex space-x-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $supportTicket->priority_color }}-100 text-{{ $supportTicket->priority_color }}-800">
                    {{ ucfirst($supportTicket->priority) }} Priority
                </span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $supportTicket->status_color }}-100 text-{{ $supportTicket->status_color }}-800">
                    {{ ucfirst(str_replace('_', ' ', $supportTicket->status)) }}
                </span>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Category</label>
                <p class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $supportTicket->category) }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Channel</label>
                    <p class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $supportTicket->support_channel ?? 'in_app') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Assigned Role</label>
                    <p class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $supportTicket->assigned_role ?? 'Smart routing') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">SLA Due</label>
                    <p class="text-sm {{ $supportTicket->sla_status === 'breached' ? 'text-red-600' : 'text-gray-900' }}">{{ $supportTicket->sla_due_at?->format('M d, Y H:i') ?? '-' }}</p>
                </div>
            </div>

            @if($supportTicket->auto_tags)
                <div>
                    <label class="text-sm font-medium text-gray-500">Auto Tags</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($supportTicket->auto_tags as $tag)
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($supportTicket->suggested_solutions)
                <div>
                    <label class="text-sm font-medium text-gray-500">Suggested Solutions</label>
                    <div class="mt-2 space-y-2">
                        @foreach($supportTicket->suggested_solutions as $solution)
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">{{ $solution }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <label class="text-sm font-medium text-gray-500">Message</label>
                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-800 whitespace-pre-line">{{ $supportTicket->message }}</p>
                </div>
            </div>

            @if($supportTicket->media_paths)
                <div>
                    <label class="text-sm font-medium text-gray-500">Uploaded Media</label>
                    <div class="mt-2 grid gap-2 md:grid-cols-2">
                        @foreach($supportTicket->media_paths as $path)
                            <a href="{{ asset('storage/'.$path) }}" target="_blank" class="rounded-lg border border-gray-200 p-3 text-sm text-emerald-700 hover:bg-emerald-50">
                                <i class="fas fa-paperclip mr-2"></i>{{ basename($path) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($supportTicket->context_snapshot)
                <div>
                    <label class="text-sm font-medium text-gray-500">Attached Farm Context</label>
                    <div class="mt-2 rounded-lg bg-gray-50 p-4 text-sm text-gray-800">
                        <p><strong>Farm:</strong> {{ data_get($supportTicket->context_snapshot, 'farm.name', 'Not set') }}</p>
                        <p><strong>Location:</strong> {{ data_get($supportTicket->context_snapshot, 'farm.location', 'Not set') }}</p>
                        <p><strong>Crops:</strong> {{ implode(', ', data_get($supportTicket->context_snapshot, 'crops', [])) ?: 'None listed' }}</p>
                        <p><strong>Livestock count:</strong> {{ data_get($supportTicket->context_snapshot, 'livestock_count', 0) }}</p>
                        <p><strong>Weather:</strong> {{ data_get($supportTicket->context_snapshot, 'latest_weather.weather_condition', 'Unknown') }} {{ data_get($supportTicket->context_snapshot, 'latest_weather.temperature') ? '- '.data_get($supportTicket->context_snapshot, 'latest_weather.temperature').' C' : '' }}</p>
                    </div>
                </div>
            @endif

            @if($supportTicket->closed_at)
            <div>
                <label class="text-sm font-medium text-gray-500">Closed At</label>
                <p class="text-sm text-gray-900">{{ $supportTicket->closed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>

    @if(in_array($supportTicket->status, ['resolved', 'closed'], true))
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Was this helpful?</h2>
            <form method="POST" action="{{ route('support-tickets.rate', $supportTicket) }}" class="space-y-3">
                @csrf
                <select name="satisfaction_rating" class="w-full border-gray-300 rounded-lg" required>
                    <option value="">Select rating</option>
                    @for($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}" {{ $supportTicket->satisfaction_rating == $rating ? 'selected' : '' }}>{{ $rating }} / 5</option>
                    @endfor
                </select>
                <textarea name="satisfaction_comment" rows="3" class="w-full border-gray-300 rounded-lg" placeholder="Optional comment">{{ $supportTicket->satisfaction_comment }}</textarea>
                <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Save Rating</button>
            </form>
        </x-ui.card>
    @endif
</div>
@endsection
