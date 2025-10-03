<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'images',
        'forum_id',
        'user_id',
        'parent_id',
        'is_solution'
    ];

    protected $casts = [
        'images' => 'array',
        'is_solution' => 'boolean'
    ];

    protected $appends = [
        'image_urls'
    ];

    // Relationships
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
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

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
