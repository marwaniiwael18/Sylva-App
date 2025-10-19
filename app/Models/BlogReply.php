<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogReply extends Model
{
    use HasFactory;

    protected $table = 'forum_replies'; // Keep old table name for now

    protected $fillable = [
        'content',
        'images',
        'forum_id',
        'user_id',
        'parent_id',
        'is_solution',
        'sentiment',
        'sentiment_score'
    ];

    protected $casts = [
        'images' => 'array',
        'is_solution' => 'boolean'
    ];

    protected $appends = [
        'image_urls'
    ];

    // Relationships
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'forum_id'); // Keep old column name for now
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogReply::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BlogReply::class, 'parent_id');
    }

    // Accessors
    public function getImageUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return array_map(function($imagePath) {
            return asset('storage/' . $imagePath);
        }, $this->images);
    }

    // Methods
    public function markAsSolution()
    {
        $this->update(['is_solution' => true]);
        $this->blog->update(['status' => 'resolved']);
    }
}
