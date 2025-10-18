<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    use HasFactory;

    protected $fillable = [
        'species',
        'latitude',
        'longitude',
        'planting_date',
        'status',
        'type',
        'planted_by_user',
        'images',
        'description',
        'address'
    ];

    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'planting_date' => 'date'
    ];

    protected $appends = [
        'image_urls'
    ];

    protected $attributes = [
        'status' => 'Not Yet'
    ];

    // Relationships
    public function plantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'planted_by_user');
    }

    public function careRecords(): HasMany
    {
        return $this->hasMany(TreeCare::class);
    }

    public function latestCare()
    {
        return $this->hasOne(TreeCare::class)->latestOfMany('performed_at');
    }

    public function recentCare($days = 30)
    {
        return $this->careRecords()
            ->where('performed_at', '>=', now()->subDays($days))
            ->orderBy('performed_at', 'desc');
    }

    // Scopes
    public function scopePlanted($query)
    {
        return $query->where('status', 'Planted');
    }

    public function scopeNotPlanted($query)
    {
        return $query->where('status', 'Not Yet');
    }

    public function scopeSick($query)
    {
        return $query->where('status', 'Sick');
    }

    public function scopeDead($query)
    {
        return $query->where('status', 'Dead');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySpecies($query, $species)
    {
        return $query->where('species', 'like', '%' . $species . '%');
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

    public function scopePlantedBy($query, $userId)
    {
        return $query->where('planted_by_user', $userId);
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Planted' => 'green',
            'Not Yet' => 'yellow',
            'Sick' => 'orange',
            'Dead' => 'red',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'Fruit' => '🍎',
            'Ornamental' => '🌸',
            'Forest' => '🌲',
            'Medicinal' => '🌿',
            default => '🌳'
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

    public function getPlantingDateFormattedAttribute()
    {
        return $this->planting_date->format('M j, Y');
    }

    public function getCoordinatesAttribute()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    // Care-related helper methods
    public function getLastCareDateAttribute()
    {
        return $this->latestCare?->performed_at;
    }

    public function getCareCountAttribute(): int
    {
        return $this->careRecords()->count();
    }

    public function getLastConditionAttribute(): ?string
    {
        return $this->latestCare?->condition_after;
    }

    public function needsCare($daysThreshold = 7): bool
    {
        if (!$this->last_care_date) {
            return true;
        }
        
        return $this->last_care_date->diffInDays(now()) > $daysThreshold;
    }

    public function getHealthScoreAttribute(): ?int
    {
        $lastCare = $this->latestCare;
        
        if (!$lastCare || !$lastCare->condition_after) {
            return null;
        }

        return match($lastCare->condition_after) {
            'excellent' => 100,
            'good' => 75,
            'fair' => 50,
            'poor' => 25,
            default => null
        };
    }
}
