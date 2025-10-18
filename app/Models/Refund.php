<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'processed_by',
        'amount',
        'currency',
        'status',
        'reason',
        'admin_notes',
        'stripe_refund_id',
        'processed_at',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Enums
    const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled'
    ];

    // Relationships
    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForDonation($query, $donationId)
    {
        return $query->where('donation_id', $donationId);
    }

    // Methods
    public function canProcess(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now()
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'admin_notes' => $reason
        ]);
    }

    public function processStripeRefund()
    {
        if (!$this->donation->stripe_charge_id) {
            throw new \Exception('No charge ID found for this donation - cannot process Stripe refund');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $refund = \Stripe\Refund::create([
            'charge' => $this->donation->stripe_charge_id,
            'amount' => $this->amount * 100, // Stripe expects amount in cents
            'reason' => 'requested_by_customer',
            'metadata' => [
                'refund_id' => $this->id,
                'donation_id' => $this->donation_id,
                'processed_by' => $this->processed_by,
                'reason' => $this->reason,
            ],
        ]);

        $this->update([
            'stripe_refund_id' => $refund->id,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return $refund;
    }
}
