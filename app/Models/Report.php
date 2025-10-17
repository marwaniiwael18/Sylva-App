<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'urgency',
        'status',
        'latitude',
        'longitude',
        'address',
        'images',
        'user_id',
        'validated_by',
        'validated_at',
        'validation_notes'
    ];

    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'validated_at' => 'datetime'
    ];

    protected $appends = [
        'image_urls',
        'vote_score',
        'total_comments',
        'total_reactions'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get all activities for this report (comments, votes, reactions)
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ReportActivity::class)->latest();
    }

    /**
     * Get all comments for this report
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ReportActivity::class)
                    ->where('activity_type', 'comment')
                    ->whereNull('parent_id')
                    ->with('replies')
                    ->latest();
    }

    /**
     * Get all votes for this report
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReportActivity::class)
                    ->where('activity_type', 'vote');
    }

    /**
     * Get all reactions for this report
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(ReportActivity::class)
                    ->where('activity_type', 'reaction');
    }

    /**
     * Get vote score (upvotes - downvotes)
     */
    public function getVoteScoreAttribute(): int
    {
        return $this->votes()->sum('vote_value');
    }

    /**
     * Get total comments count (including replies)
     */
    public function getTotalCommentsAttribute(): int
    {
        return $this->activities()->where('activity_type', 'comment')->count();
    }

    /**
     * Get total reactions count
     */
    public function getTotalReactionsAttribute(): int
    {
        return $this->reactions()->count();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
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
            'pending' => 'yellow',
            'validated' => 'blue',
            'in_progress' => 'orange',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getUrgencyColorAttribute()
    {
        return match($this->urgency) {
            'low' => 'green',
            'medium' => 'orange',
            'high' => 'red',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'tree_planting' => '🌳',
            'maintenance' => '🔧',
            'pollution' => '⚠️',
            'green_space_suggestion' => '🌱',
            default => '📍'
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
}
