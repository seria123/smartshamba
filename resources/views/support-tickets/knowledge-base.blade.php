@extends('layouts.MainLayout')

@section('title', 'Help Center - SmartShamba')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Knowledge Base</h1>
            <p class="text-sm text-gray-500">FAQs and guides for irrigation, disease control, finance, livestock, and system support.</p>
        </div>
        <a href="{{ route('support-tickets.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <x-ui.card>
        <form method="GET" class="grid gap-3 md:grid-cols-3">
            <input name="q" value="{{ request('q') }}" class="rounded-lg border-gray-300 md:col-span-2" placeholder="Search help center...">
            <select name="category" class="rounded-lg border-gray-300">
                <option value="">All categories</option>
                @foreach(['crop' => 'Crops', 'livestock' => 'Livestock', 'finance' => 'Finance', 'system_bug' => 'System Bugs'] as $value => $label)
                    <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700 md:col-span-3">Search</button>
        </form>
    </x-ui.card>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($articles as $article)
            <x-ui.card>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ str_replace('_', ' ', $article['category']) }}</p>
                <h2 class="mt-1 text-lg font-semibold text-gray-900">{{ $article['title'] }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $article['summary'] }}</p>
                <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
                    @foreach($article['steps'] as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ul>
            </x-ui.card>
        @endforeach
    </div>
</div>
@endsection
