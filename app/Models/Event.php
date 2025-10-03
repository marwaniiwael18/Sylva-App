<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

        'organized_by_user_id',

    ];

    protected $casts = [
        'date' => 'datetime',

    ];

    /**
     * Types d'événements disponibles
     */
    public const TYPES = [
        'Tree Planting' => 'Tree Planting',
        'Maintenance' => 'Maintenance',
        'Awareness' => 'Awareness',
        'Workshop' => 'Workshop',
    ];

    /**
     * L'utilisateur qui organise l'événement
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organized_by_user_id');
    }

    /**
     * Les participants à l'événement
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('registered_at')
                    ->withTimestamps();
    }

    /**
     * Vérifier si un utilisateur participe à cet événement
     */
    public function hasParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Compter le nombre de participants
     */
    public function getParticipantsCountAttribute(): int
    {
        return $this->participants()->count();
    }

    /**
     * Vérifier si l'événement est passé
     */
    public function getIsPastAttribute(): bool
    {
        return $this->date < now();
    }

    /**
     * Formater la date pour l'affichage
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y à H:i');
    }

    /**
     * Donations related to this event
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'event_id');
    }

    /**
     * Get total donations amount for this event
     */
    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()->where('payment_status', 'succeeded')->sum('amount');
    }

    /**
     * Scope for active events
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>', now())->where('status', 'active');
    }
}