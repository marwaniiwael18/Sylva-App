<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreeCare extends Model
{
    use HasFactory;

    protected $table = 'tree_care';

    protected $fillable = [
        'tree_id',
        'user_id',
        'event_id',
        'activity_type',
        'notes',
        'images',
        'performed_at',
        'condition_after'
    ];

    protected $casts = [
        'images' => 'array',
        'performed_at' => 'date',
    ];

    protected $appends = [
        'image_urls',
        'activity_type_name',
        'condition_color'
    ];

    // Activity types constants
    public const ACTIVITY_TYPES = [
        'watering' => 'Watering',
        'pruning' => 'Pruning',
        'fertilizing' => 'Fertilizing',
        'disease_treatment' => 'Disease Treatment',
        'inspection' => 'Inspection',
        'other' => 'Other'
    ];

    // Condition constants
    public const CONDITIONS = [
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor'
    ];

    // Relationships
    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    public function maintainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
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

    public function getActivityTypeNameAttribute(): string
    {
        return self::ACTIVITY_TYPES[$this->activity_type] ?? $this->activity_type;
    }

    public function getConditionNameAttribute(): ?string
    {
        return $this->condition_after ? (self::CONDITIONS[$this->condition_after] ?? $this->condition_after) : null;
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->condition_after) {
            'excellent' => 'green',
            'good' => 'blue',
            'fair' => 'yellow',
            'poor' => 'red',
            default => 'gray'
        };
    }

    public function getActivityIconAttribute(): string
    {
        return match($this->activity_type) {
            'watering' => '💧',
            'pruning' => '✂️',
            'fertilizing' => '🌱',
            'disease_treatment' => '💊',
            'inspection' => '🔍',
            'other' => '🛠️',
            default => '🌳'
        };
    }

    public function getPerformedAtFormattedAttribute(): string
    {
        return $this->performed_at->format('M j, Y');
    }

    // Scopes
    public function scopeByTree($query, $treeId)
    {
        return $query->where('tree_id', $treeId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByActivityType($query, $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition_after', $condition);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('performed_at', '>=', now()->subDays($days));
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('performed_at', now()->month)
                     ->whereYear('performed_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('performed_at', now()->year);
    }
}
