<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'type',
        'status',
        'organized_by_user_id',
        'max_participants',
        'current_participants'
    ];

    protected $casts = [
        'date' => 'datetime',
        'max_participants' => 'integer',
        'current_participants' => 'integer'
    ];

    // Relationships
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'event_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organized_by_user_id');
    }

    // Accessors
    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()->where('payment_status', 'succeeded')->sum('amount');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>', now())->where('status', 'active');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->date > now();
    }
}