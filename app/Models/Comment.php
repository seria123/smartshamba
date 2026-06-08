<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'body',
        'topic_tags',
        'mentions',
        'insight_type',
        'sentiment',
        'is_urgent',
        'voice_note_path',
        'board_type',
        'moderation_status',
        'converted_to_type',
        'converted_to_id',
    ];

    protected $casts = [
        'commentable_id' => 'integer',
        'parent_id' => 'integer',
        'topic_tags' => 'array',
        'mentions' => 'array',
        'is_urgent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for threaded replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies to this one).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with(['user', 'reactions'])
            ->orderBy('created_at', 'asc');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }

    public function reactionCount(string $type): int
    {
        return $this->reactions->where('reaction_type', $type)->count();
    }

    public function getUsefulnessScoreAttribute(): int
    {
        return ($this->reactionCount('helpful') * 3)
            + ($this->reactionCount('important') * 2)
            + $this->reactionCount('like');
    }

    /**
     * Morph to the associated model.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
