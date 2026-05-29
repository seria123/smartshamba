@extends('layouts.MainLayout')

@section('title', 'Comments - SmartShamba')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-comments text-green-600"></i>
            Comments
        </h1>
    </div>

    <!-- Comment Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add a Comment</h3>
        <form action="{{ route('comments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="commentable_type" value="">
            <input type="hidden" name="commentable_id" value="">

            <div class="mb-4">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-2">Your Comment</label>
                <textarea
                    name="body"
                    id="body"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    placeholder="Write your comment here..."
                    required
                >{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium"
            >
                <i class="fas fa-paper-plane mr-2"></i> Post Comment
            </button>
        </form>
    </div>

    <!-- Comments List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">All Comments</h3>

        @if($comments->count() === 0)
            <p class="text-gray-500">No comments yet. Be the first to comment!</p>
        @else
            <div class="space-y-6">
                @foreach ($comments as $comment)
                    <div class="border rounded-lg p-4 hover:shadow-md transition">

                        <!-- Comment Header -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-800">
                                    {{ $comment->user->name ?? 'Unknown User' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                                      onsubmit="return confirm('Delete this comment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Comment Body -->
                        <p class="text-gray-700 mb-3 whitespace-pre-wrap">{{ $comment->body }}</p>

                        <!-- Reply Prompt -->
                        <div class="ml-10">
                            <form action="{{ route('comments.store') }}" method="POST"
                                  class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="commentable_type" value="{{ $comment->commentable_type }}">
                                <input type="hidden" name="commentable_id" value="{{ $comment->commentable_id }}">
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <input
                                    type="text"
                                    name="body"
                                    placeholder="Reply to this comment..."
                                    class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                    required
                                >
                                <button type="submit" class="text-green-600 text-sm hover:underline">
                                    Reply
                                </button>
                            </form>
                        </div>

                        <!-- Nested Replies -->
                        @if($comment->replies->count() > 0)
                            <div class="mt-4 ml-10 space-y-4 border-l-2 border-green-200 pl-4">
                                @foreach ($comment->replies as $reply)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                                    {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-gray-800 text-sm">
                                                    {{ $reply->user->name ?? 'Unknown User' }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            @if(auth()->id() === $reply->user_id || auth()->user()->isAdmin())
                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST"
                                                      onsubmit="return confirm('Delete this reply?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="text-gray-700 text-sm">{{ $reply->body }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
