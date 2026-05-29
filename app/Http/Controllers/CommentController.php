<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

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
            ->with(['user', 'replies.user'])
            ->latest();

        if ($commentableType) {
            $query->where('commentable_type', $commentableType);
        }

        if ($commentableId) {
            $query->where('commentable_id', $commentableId);
        }

        $comments = $query->paginate(20);

        return view('comments.index', compact('comments', 'commentableType', 'commentableId'));
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
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'commentable_type' => $validated['commentable_type'] ?? null,
            'commentable_id' => $validated['commentable_id'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        $comment->load('user');

        return back()->with('success', 'Comment added successfully.');
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
}
