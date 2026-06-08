<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\CommentReport;
use App\Models\Field;
use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommentController extends Controller
{
    /**
     * Display comments (optionally filtered by commentable model and ID).
     */
    public function index(Request $request)
    {
        $commentableType = $request->query('commentable_type');
        $commentableId = $request->query('commentable_id');

        $query = Comment::query()
            ->with(['user', 'reactions', 'reports', 'replies.user', 'replies.reactions'])
            ->whereNull('parent_id')
            ->where('moderation_status', '!=', 'hidden')
            ->latest();

        if ($commentableType) {
            $query->where('commentable_type', $commentableType);
        }

        if ($commentableId) {
            $query->where('commentable_id', $commentableId);
        }

        if ($request->filled('board_type')) {
            $query->where('board_type', $request->board_type);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('topic_tags', $request->tag);
        }

        if ($request->filled('insight_type')) {
            $query->where('insight_type', $request->insight_type);
        }

        $comments = $query->paginate(20);
        $analytics = $this->analytics();
        $reportedComments = CommentReport::with(['comment.user', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return view('comments.index', compact('comments', 'commentableType', 'commentableId', 'analytics', 'reportedComments'));
    }

/**
      * Store a new comment.
      */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'nullable|string',
            'commentable_id' => 'nullable|integer',
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'board_type' => 'nullable|in:community,farm_logs,crop_discussion,feedback',
            'voice_note' => 'nullable|file|mimes:mp3,wav,m4a,ogg,webm|max:10240',
        ]);

        $insights = $this->insights($validated['body']);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'commentable_type' => $validated['commentable_type'] ?? null,
            'commentable_id' => $validated['commentable_id'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
            'topic_tags' => $this->extractTags($validated['body']),
            'mentions' => $this->extractMentions($validated['body']),
            'insight_type' => $insights['type'],
            'sentiment' => $insights['sentiment'],
            'is_urgent' => $insights['urgent'],
            'board_type' => $validated['board_type'] ?? 'community',
            'voice_note_path' => $request->hasFile('voice_note')
                ? $request->file('voice_note')->store('voice-notes/comments', 'public')
                : null,
        ]);

        $comment->load('user');
        $this->notifyReply($comment);
        $this->notifyMentions($comment);

        return back()->with('success', 'Comment added successfully.');
    }

    public function react(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'reaction_type' => 'required|in:like,helpful,important',
        ]);

        $reaction = CommentReaction::where([
            'comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'reaction_type' => $validated['reaction_type'],
        ])->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            CommentReaction::create([
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'reaction_type' => $validated['reaction_type'],
            ]);
        }

        return back()->with('success', 'Reaction updated.');
    }

    public function report(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'reason' => 'required|in:spam,abuse,misinformation,off_topic,other',
            'details' => 'nullable|string|max:1000',
        ]);

        CommentReport::create([
            'comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
        ]);

        $comment->update(['moderation_status' => 'reported']);

        return back()->with('success', 'Comment reported for review.');
    }

    public function moderate(Request $request, Comment $comment)
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => 'required|in:visible,hidden,reported',
        ]);

        $comment->update($validated);
        $comment->reports()->update(['status' => $validated['moderation_status'] === 'visible' ? 'reviewed' : 'actioned']);

        return back()->with('success', 'Moderation status updated.');
    }

    public function convert(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'convert_to' => 'required|in:task,feature_request,bug_report',
        ]);

        if ($validated['convert_to'] === 'task') {
            $field = Field::where('user_id', auth()->id())->first() ?? Field::first();

            if (! $field) {
                $comment->update(['converted_to_type' => 'task_pending_field', 'converted_to_id' => null]);

                return back()->with('success', 'Feedback marked for task creation. Add a field first to create a scheduled task.');
            }

            $task = Task::create([
                'field_id' => $field->id,
                'title' => 'Follow up comment: '.str($comment->body)->limit(60),
                'description' => $comment->body,
                'task_type' => Task::TYPE_OTHER,
                'status' => Task::STATUS_PENDING,
                'priority' => $comment->is_urgent ? Task::PRIORITY_URGENT : Task::PRIORITY_NORMAL,
                'scheduled_date' => now()->addDay(),
                'assigned_to' => auth()->id(),
            ]);

            $comment->update(['converted_to_type' => 'task', 'converted_to_id' => $task->id]);
        } else {
            $comment->update(['converted_to_type' => $validated['convert_to'], 'converted_to_id' => null]);
        }

        return back()->with('success', 'Feedback converted into action.');
    }

    /**
     * Remove a comment.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }

    private function extractTags(string $body): array
    {
        preg_match_all('/#([A-Za-z0-9_]+)/', $body, $matches);

        return collect($matches[1] ?? [])->map(fn ($tag) => strtolower($tag))->unique()->values()->all();
    }

    private function extractMentions(string $body): array
    {
        preg_match_all('/@([A-Za-z0-9_]+)/', $body, $matches);

        return collect($matches[1] ?? [])->unique()->values()->all();
    }

    private function insights(string $body): array
    {
        $lower = strtolower($body);
        $urgentWords = ['urgent', 'dying', 'emergency', 'outbreak', 'serious', 'disease', 'failed', 'broken'];
        $complaintWords = ['bad', 'problem', 'issue', 'frustrated', 'not working', 'slow', 'broken'];
        $suggestionWords = ['suggest', 'please add', 'feature', 'would like', 'improve', 'idea'];
        $positiveWords = ['thanks', 'helpful', 'good', 'great', 'works', 'solved'];

        $urgent = collect($urgentWords)->contains(fn ($word) => str_contains($lower, $word));
        $type = 'general';

        if (collect($complaintWords)->contains(fn ($word) => str_contains($lower, $word))) {
            $type = 'complaint';
        }

        if (collect($suggestionWords)->contains(fn ($word) => str_contains($lower, $word))) {
            $type = 'suggestion';
        }

        if ($urgent) {
            $type = 'urgent_issue';
        }

        $sentiment = collect($positiveWords)->contains(fn ($word) => str_contains($lower, $word)) ? 'happy' : ($type === 'general' ? 'neutral' : 'frustrated');

        return compact('type', 'sentiment', 'urgent');
    }

    private function analytics(): array
    {
        $comments = Comment::with('reactions')->get();

        return [
            'total' => $comments->count(),
            'complaints' => $comments->where('insight_type', 'complaint')->count(),
            'suggestions' => $comments->where('insight_type', 'suggestion')->count(),
            'urgent' => $comments->where('is_urgent', true)->count(),
            'happy' => $comments->where('sentiment', 'happy')->count(),
            'frustrated' => $comments->where('sentiment', 'frustrated')->count(),
            'top_tags' => $comments->flatMap(fn ($comment) => $comment->topic_tags ?? [])->countBy()->sortDesc()->take(6),
            'top_comments' => $comments->sortByDesc('usefulness_score')->take(3),
        ];
    }

    private function notifyReply(Comment $comment): void
    {
        if (! $comment->parent || $comment->parent->user_id === $comment->user_id) {
            return;
        }

        Notification::create([
            'user_id' => $comment->parent->user_id,
            'type' => Notification::TYPE_SYSTEM,
            'title' => 'New reply to your comment',
            'message' => ($comment->user?->name ?? 'Someone').' replied to your comment.',
            'priority' => Notification::PRIORITY_NORMAL,
            'data' => ['comment_id' => $comment->id],
        ]);
    }

    private function notifyMentions(Comment $comment): void
    {
        foreach ($comment->mentions ?? [] as $mention) {
            $users = \App\Models\User::where('name', 'like', str_replace('_', ' ', $mention).'%')->get();

            foreach ($users as $user) {
                if ($user->id === $comment->user_id) {
                    continue;
                }

                Notification::create([
                    'user_id' => $user->id,
                    'type' => Notification::TYPE_SYSTEM,
                    'title' => 'You were mentioned',
                    'message' => ($comment->user?->name ?? 'Someone').' mentioned you in a comment.',
                    'priority' => Notification::PRIORITY_NORMAL,
                    'data' => ['comment_id' => $comment->id],
                ]);
            }
        }
    }
}
