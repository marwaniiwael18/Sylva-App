<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
