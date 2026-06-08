@php
    $depth = $depth ?? 0;
    $reactionLabels = [
        'like' => ['icon' => 'thumbs-up', 'label' => 'Like'],
        'helpful' => ['icon' => 'check-circle', 'label' => 'Helpful'],
        'important' => ['icon' => 'exclamation-triangle', 'label' => 'Important'],
    ];
@endphp

<div class="border rounded-lg p-4 hover:shadow-md transition {{ $comment->is_urgent ? 'border-amber-300 bg-amber-50' : 'bg-white' }}">
    <div class="flex items-start justify-between gap-3 mb-2">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-gray-800">{{ $comment->user->name ?? 'Unknown User' }}</span>
                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ str_replace('_', ' ', $comment->board_type ?? 'community') }}</span>
                    @if($comment->insight_type)
                        <span class="rounded-full {{ $comment->is_urgent ? 'bg-amber-200 text-amber-900' : 'bg-sky-100 text-sky-800' }} px-2 py-0.5 text-xs font-semibold">{{ str_replace('_', ' ', $comment->insight_type) }}</span>
                    @endif
                    @if($comment->converted_to_type)
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Converted: {{ str_replace('_', ' ', $comment->converted_to_type) }}</span>
                    @endif
                </div>
                @if($comment->topic_tags || $comment->mentions)
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($comment->topic_tags ?? [] as $tag)
                            <a href="{{ route('comments.index', ['tag' => $tag]) }}" class="text-xs text-emerald-700">#{{ $tag }}</a>
                        @endforeach
                        @foreach($comment->mentions ?? [] as $mention)
                            <span class="text-xs text-blue-700">@{{ $mention }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
            <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
            </form>
        @endif
    </div>

    <p class="text-gray-700 mb-3 whitespace-pre-wrap">{{ $comment->body }}</p>

    @if($comment->voice_note_path)
        <audio controls class="mb-3 w-full">
            <source src="{{ asset('storage/'.$comment->voice_note_path) }}">
        </audio>
    @endif

    <div class="flex flex-wrap items-center gap-2 mb-3">
        @foreach($reactionLabels as $type => $reaction)
            <form method="POST" action="{{ route('comments.react', $comment) }}">
                @csrf
                <input type="hidden" name="reaction_type" value="{{ $type }}">
                <button class="rounded-full border border-gray-200 px-3 py-1 text-xs text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-{{ $reaction['icon'] }} mr-1"></i>{{ $reaction['label'] }} {{ $comment->reactionCount($type) }}
                </button>
            </form>
        @endforeach

        <details class="relative">
            <summary class="cursor-pointer rounded-full border border-gray-200 px-3 py-1 text-xs text-gray-700">Report</summary>
            <form method="POST" action="{{ route('comments.report', $comment) }}" class="mt-2 rounded-lg border bg-white p-3 shadow-sm space-y-2 min-w-64">
                @csrf
                <select name="reason" class="w-full rounded border-gray-300 text-sm" required>
                    @foreach(['spam' => 'Spam', 'abuse' => 'Abuse', 'misinformation' => 'Misinformation', 'off_topic' => 'Off topic', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input name="details" class="w-full rounded border-gray-300 text-sm" placeholder="Optional details">
                <button class="rounded bg-rose-600 px-3 py-1 text-xs text-white">Send Report</button>
            </form>
        </details>

        @if(auth()->user()->isAdmin() || $comment->user_id === auth()->id())
            <details>
                <summary class="cursor-pointer rounded-full border border-gray-200 px-3 py-1 text-xs text-gray-700">Convert</summary>
                <form method="POST" action="{{ route('comments.convert', $comment) }}" class="mt-2 flex gap-2">
                    @csrf
                    <select name="convert_to" class="rounded border-gray-300 text-sm">
                        <option value="task">Task</option>
                        <option value="feature_request">Feature request</option>
                        <option value="bug_report">Bug report</option>
                    </select>
                    <button class="rounded bg-emerald-600 px-3 py-1 text-xs text-white">Create</button>
                </form>
            </details>
        @endif
    </div>

    <form action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data" class="ml-0 sm:ml-8 flex flex-col gap-2">
        @csrf
        <input type="hidden" name="commentable_type" value="{{ $comment->commentable_type }}">
        <input type="hidden" name="commentable_id" value="{{ $comment->commentable_id }}">
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <input type="hidden" name="board_type" value="{{ $comment->board_type ?? 'community' }}">
        <div class="flex gap-2">
            <input type="text" name="body" placeholder="Reply to this comment..." class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500" required>
            <button type="submit" class="text-green-600 text-sm hover:underline">Reply</button>
        </div>
    </form>

    @if($comment->replies->count() > 0)
        <div class="mt-4 ml-4 sm:ml-10 space-y-4 border-l-2 border-green-200 pl-4">
            @foreach ($comment->replies as $reply)
                @include('comments.partials.comment-card', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
