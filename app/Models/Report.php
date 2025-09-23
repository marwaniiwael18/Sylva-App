<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'image_urls'
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
