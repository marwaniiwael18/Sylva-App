<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'related_event_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the author of the forum post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the related report.
     */
    public function relatedReport(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'related_event_id');
    }

    /**
     * Get all comments for this forum post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
