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

            <div>
                <label class="text-sm font-medium text-gray-500">Message</label>
                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-800 whitespace-pre-line">{{ $supportTicket->message }}</p>
                </div>
            </div>

            @if($supportTicket->closed_at)
            <div>
                <label class="text-sm font-medium text-gray-500">Closed At</label>
                <p class="text-sm text-gray-900">{{ $supportTicket->closed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>
</div>
@endsection