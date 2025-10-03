<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_moderator',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_moderator' => 'boolean',
        ];
    }

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isModerator(): bool
    {
        return $this->is_moderator;
    }

    public function canValidateReports(): bool
    {
        return $this->is_admin || $this->is_moderator;
    }


    /**
     * Les événements organisés par cet utilisateur
     */
    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organized_by_user_id');
    }

    /**
     * Les événements auxquels cet utilisateur participe
     */
    public function participatingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
                    ->withPivot('registered_at')
                    ->withTimestamps();
    }

    // Donations relationship
    public function donations()
    {
        return $this->hasMany(Donation::class, 'user_id');
    }

    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()->where('payment_status', 'succeeded')->sum('amount');
    }
}
