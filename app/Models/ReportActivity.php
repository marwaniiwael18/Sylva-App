<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'activity_type',
        'content',
        'reaction_type',
        'vote_value',
        'parent_id',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'vote_value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['user']; // Always load user data

    /**
     * Get the report this activity belongs to
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who created this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent activity (for replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ReportActivity::class, 'parent_id');
    }

    /**
     * Get all replies to this activity
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ReportActivity::class, 'parent_id')->latest();
    }

    /**
     * Scope to get only comments
     */
    public function scopeComments($query)
    {
        return $query->where('activity_type', 'comment');
    }

    /**
     * Scope to get only votes
     */
    public function scopeVotes($query)
    {
        return $query->where('activity_type', 'vote');
    }

    /**
     * Scope to get only reactions
     */
    public function scopeReactions($query)
    {
        return $query->where('activity_type', 'reaction');
    }

    /**
     * Scope to get pinned activities
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to get root activities (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if activity is a comment
     */
    public function isComment(): bool
    {
        return $this->activity_type === 'comment';
    }

    /**
     * Check if activity is a vote
     */
    public function isVote(): bool
    {
        return $this->activity_type === 'vote';
    }

    /**
     * Check if activity is a reaction
     */
    public function isReaction(): bool
    {
        return $this->activity_type === 'reaction';
    }
}
