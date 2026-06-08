@extends('layouts.MainLayout')

@section('title', 'Comments & Feedback - SmartShamba')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-comments text-green-600"></i>
                Comments & Feedback
            </h1>
            <p class="text-sm text-gray-500 mt-1">Threaded discussions, reactions, moderation, AI insights, analytics, voice notes, and feedback-to-action.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $analytics['total'] }}</p>
            <p class="text-sm text-gray-500">Total comments</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-rose-700">{{ $analytics['complaints'] }}</p>
            <p class="text-sm text-gray-500">Complaints</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-emerald-700">{{ $analytics['suggestions'] }}</p>
            <p class="text-sm text-gray-500">Suggestions</p>
        </x-ui.card>
        <x-ui.card class="text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $analytics['urgent'] }}</p>
            <p class="text-sm text-gray-500">Urgent issues</p>
        </x-ui.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Community Discussion Board</h2>
            <form action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="commentable_type" value="{{ $commentableType }}">
                <input type="hidden" name="commentable_id" value="{{ $commentableId }}">

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block text-sm font-medium text-gray-700">Board
                        <select name="board_type" class="mt-1 w-full rounded-lg border-gray-300">
                            @foreach(['community' => 'Community', 'farm_logs' => 'Farm Logs', 'crop_discussion' => 'Crop Discussions', 'feedback' => 'Product Feedback'] as $value => $label)
                                <option value="{{ $value }}" {{ old('board_type', request('board_type', 'community')) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-gray-700">Voice Note
                        <input type="file" name="voice_note" accept=".mp3,.wav,.m4a,.ogg,.webm" class="mt-1 w-full rounded-lg border-gray-300">
                    </label>
                </div>

                <label class="block text-sm font-medium text-gray-700">Your Comment
                    <textarea name="body" rows="4" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Share a farm update, ask the community, mention @Agronomist, or tag #Maize #Dairy #Finance..." required>{{ old('body') }}</textarea>
                </label>
                @error('body')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                    <i class="fas fa-paper-plane mr-2"></i> Post Comment
                </button>
            </form>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Feedback Analytics</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span>Happy</span><span class="font-semibold text-emerald-700">{{ $analytics['happy'] }}</span></div>
                <div class="flex justify-between"><span>Frustrated</span><span class="font-semibold text-rose-700">{{ $analytics['frustrated'] }}</span></div>
                <div>
                    <p class="font-medium text-gray-700 mb-2">Common Topics</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($analytics['top_tags'] as $tag => $count)
                            <a href="{{ route('comments.index', ['tag' => $tag]) }}" class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">#{{ $tag }} {{ $count }}</a>
                        @empty
                            <span class="text-gray-500">No tags yet</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    @if(auth()->user()->isAdmin() && $reportedComments->count())
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Moderation Review</h2>
            <div class="space-y-3">
                @foreach($reportedComments as $report)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-amber-900"><strong>{{ ucfirst($report->reason) }}:</strong> {{ Str::limit($report->comment?->body, 120) }}</p>
                                <p class="text-xs text-amber-700">Reported by {{ $report->user?->name ?? 'Unknown' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('comments.moderate', $report->comment) }}">
                                    @csrf
                                    <input type="hidden" name="moderation_status" value="visible">
                                    <button class="rounded bg-white px-3 py-1 text-xs text-emerald-700 border">Keep</button>
                                </form>
                                <form method="POST" action="{{ route('comments.moderate', $report->comment) }}">
                                    @csrf
                                    <input type="hidden" name="moderation_status" value="hidden">
                                    <button class="rounded bg-rose-600 px-3 py-1 text-xs text-white">Hide</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    <x-ui.card>
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <select name="board_type" class="rounded-lg border-gray-300">
                <option value="">All boards</option>
                @foreach(['community' => 'Community', 'farm_logs' => 'Farm Logs', 'crop_discussion' => 'Crop Discussions', 'feedback' => 'Product Feedback'] as $value => $label)
                    <option value="{{ $value }}" {{ request('board_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="insight_type" class="rounded-lg border-gray-300">
                <option value="">All insights</option>
                @foreach(['complaint' => 'Complaints', 'suggestion' => 'Suggestions', 'urgent_issue' => 'Urgent Issues', 'general' => 'General'] as $value => $label)
                    <option value="{{ $value }}" {{ request('insight_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input name="tag" value="{{ request('tag') }}" class="rounded-lg border-gray-300" placeholder="Filter by tag">
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white">Filter</button>
        </form>
    </x-ui.card>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Discussion</h3>
            <span class="text-sm text-gray-500">Most useful comments surface through helpful and important reactions.</span>
        </div>

        @if($comments->count() === 0)
            <p class="text-gray-500">No comments yet. Be the first to comment.</p>
        @else
            <div class="space-y-6">
                @foreach ($comments as $comment)
                    @include('comments.partials.comment-card', ['comment' => $comment, 'depth' => 0])
                @endforeach
            </div>

            <div class="mt-6">{{ $comments->links() }}</div>
        @endif
    </div>
</div>
@endsection
