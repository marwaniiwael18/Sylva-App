<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $table = 'forum_posts'; // Keep old table name for now

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
     * Get the author of the blog post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the related event.
     */
    public function relatedEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'related_event_id');
    }

    /**
     * Get all comments for this blog post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'forum_post_id'); // Keep old column name for now
    }
}
