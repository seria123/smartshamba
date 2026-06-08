@extends('layouts.MainLayout')

@section('title', 'Farm Help Bot - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Farm Help Bot</h1>
            <p class="text-sm text-gray-500">Instant self-service answers using SmartShamba help guides and your farm context.</p>
        </div>
        <a href="{{ route('support-tickets.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <x-ui.card>
        <form method="GET" class="grid gap-2 md:grid-cols-[1fr_auto_auto]">
            <input name="question" value="{{ $question }}" class="rounded-lg border-gray-300" placeholder="Why are my crops yellow? How do I vaccinate chickens?">
            <select name="answer_channel" class="rounded-lg border-gray-300">
                <option value="smart_ai" {{ ($answerChannel ?? 'smart_ai') === 'smart_ai' ? 'selected' : '' }}>SmartShamba AI</option>
                <option value="local" {{ ($answerChannel ?? 'smart_ai') === 'local' ? 'selected' : '' }}>Local guides</option>
            </select>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700"><i class="fas fa-paper-plane mr-1"></i> Ask</button>
        </form>
        <p class="mt-3 text-xs text-gray-500">SmartShamba AI uses your farm context and matching help guides. Local guides work without an AI API key.</p>
    </x-ui.card>

    @if($answer)
        <x-ui.card>
            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900"><i class="fas fa-robot text-emerald-600 mr-2"></i>Suggested Answer</h2>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $answerSource }}</span>
            </div>
            <p class="text-gray-800">{{ $answer }}</p>
        </x-ui.card>
    @endif

    <x-ui.card>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Relevant Help Guides</h2>
        <div class="space-y-3">
            @foreach(array_slice($articles, 0, 5) as $article)
                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="font-medium text-gray-900">{{ $article['title'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $article['summary'] }}</p>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</div>
@endsection
