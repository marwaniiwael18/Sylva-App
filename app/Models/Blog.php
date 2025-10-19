<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'latitude',
        'longitude',
        'address',
        'images',
        'status',
        'user_id',
        'views',
        'replies_count',
        'last_activity'
    ];

    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_activity' => 'datetime'
    ];

    protected $appends = [
        'image_urls'
    ];

    protected $attributes = [
        'status' => 'open',
        'priority' => 'low'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BlogReply::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeInRadius($query, $latitude, $longitude, $radiusKm = 10)
    {
        return $query->selectRaw(
            '*,
            ( 6371 * acos( cos( radians(?) ) *
              cos( radians( latitude ) ) *
              cos( radians( longitude ) - radians(?) ) +
              sin( radians(?) ) *
              sin( radians( latitude ) ) ) ) AS distance',
            [$latitude, $longitude, $latitude]
        )
        ->having('distance', '<', $radiusKm)
        ->orderBy('distance');
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'blue',
            'closed' => 'gray',
            'resolved' => 'green',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'orange',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'general' => '💬',
            'tree_care' => '🌳',
            'environmental' => '🌍',
            'events' => '📅',
            'suggestions' => '💡',
            default => '💬'
        };
    }

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

    public function getLastActivityFormattedAttribute()
    {
        return $this->last_activity ? $this->last_activity->diffForHumans() : $this->created_at->diffForHumans();
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    public function updateRepliesCount()
    {
        $this->update(['replies_count' => $this->replies()->count()]);
    }
}
